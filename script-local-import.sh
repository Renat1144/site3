#!/usr/bin/env bash
set -euo pipefail

project_path="$(cd "$(dirname "$0")" && pwd)"
env_path="$project_path/.env"
compose_path="$project_path/docker-compose.yml"
uploads_path="$project_path/wp-content/uploads"
backups_path="$project_path/backups"
project_folder_name="$(basename "$project_path")"
archive_path=""
google_drive_sites_path=""
force=0
skip_backup=0
validate_only=0

while [[ $# -gt 0 ]]; do
    case "$1" in
        --force) force=1; shift ;;
        --skip-backup) skip_backup=1; shift ;;
        --validate-only) validate_only=1; shift ;;
        --google-drive-sites-path)
            [[ $# -ge 2 && "$2" != --* ]] || { echo '--google-drive-sites-path requires a path.' >&2; exit 2; }
            google_drive_sites_path="$2"
            shift 2
            ;;
        -*) echo "Unknown argument: $1" >&2; exit 2 ;;
        *)
            if [[ -n "$archive_path" ]]; then
                echo 'Only one ZIP path may be provided.' >&2
                exit 2
            fi
            archive_path="$1"
            shift
            ;;
    esac
done

read_env_value() {
    local key="$1"
    local value
    value="$(sed -n "s/^${key}=//p" "$env_path" | tail -n 1)"
    if [[ "$value" == \"*\" && "$value" == *\" ]]; then
        value="${value:1:${#value}-2}"
    elif [[ "$value" == \'*\' && "$value" == *\' ]]; then
        value="${value:1:${#value}-2}"
    fi
    printf '%s' "$value"
}

read_manifest_string() {
    local key="$1"
    sed -n "s/.*\"${key}\"[[:space:]]*:[[:space:]]*\"\([^\"]*\)\".*/\1/p" "$temporary_path/manifest.json" | head -n 1
}

find_google_drive_sites_path() {
    if [[ -n "$google_drive_sites_path" ]]; then
        printf '%s' "$google_drive_sites_path"
        return 0
    fi
    if [[ -n "${GOOGLE_DRIVE_SITES_PATH:-}" ]]; then
        printf '%s' "$GOOGLE_DRIVE_SITES_PATH"
        return 0
    fi
    if [[ -n "${CODEX_DRIVE_PATH:-}" ]]; then
        printf '%s' "$CODEX_DRIVE_PATH"
        return 0
    fi

    local candidate
    for candidate in \
        "$HOME"/Library/CloudStorage/*/Codex\ Drive \
        "$HOME"/Library/CloudStorage/GoogleDrive-*/My\ Drive/Codex\ Drive \
        "$HOME"/Library/CloudStorage/GoogleDrive-*/Мой\ диск/Codex\ Drive \
        "$HOME/Google Drive/Codex Drive" \
        "$HOME/My Drive/Codex Drive" \
        "$HOME/Мой диск/Codex Drive" \
        "/Volumes/GoogleDrive/My Drive/Codex Drive" \
        "/Volumes/GoogleDrive/Мой диск/Codex Drive"; do
        if [[ -d "$candidate" ]]; then
            printf '%s' "$candidate"
            return 0
        fi
    done

    # A path stored in .env may have come from another device. Use it only
    # after checking this Mac's standard Google Drive locations.
    local configured_path=""
    if [[ -f "$env_path" ]]; then
        configured_path="$(read_env_value SITE_TRANSFER_DIR)"
        if [[ -z "$configured_path" ]]; then
            configured_path="$(read_env_value GOOGLE_DRIVE_SITES_PATH)"
        fi
    fi
    if [[ -n "$configured_path" && -d "$configured_path" ]]; then
        printf '%s' "$configured_path"
        return 0
    fi
    return 1
}

command -v unzip >/dev/null 2>&1 || {
    echo 'The unzip command was not found.' >&2
    exit 1
}
[[ -f "$compose_path" ]] || {
    echo 'docker-compose.yml was not found next to this script.' >&2
    exit 1
}

archive_from_google_drive=0
if [[ -z "$archive_path" ]]; then
    archive_from_google_drive=1
    sites_path="$(find_google_drive_sites_path)" || {
        echo 'Google Drive folder "Codex Drive" was not found. Start Google Drive Desktop or pass --google-drive-sites-path with its local path.' >&2
        exit 1
    }
    cloud_archive_path="$(ls -1t "$sites_path"/"$project_folder_name"-transfer-*.zip 2>/dev/null | head -n 1 || true)"
    [[ -n "$cloud_archive_path" ]] || {
        echo "No $project_folder_name transfer ZIP was found in Google Drive: $sites_path" >&2
        exit 1
    }
    incoming_path="$backups_path/incoming"
    mkdir -p "$incoming_path"
    local_archive_path="$incoming_path/$(basename "$cloud_archive_path")"
    cp -f "$cloud_archive_path" "$local_archive_path"
    if [[ -f "$cloud_archive_path.sha256" ]]; then
        cp -f "$cloud_archive_path.sha256" "$local_archive_path.sha256"
    fi
    archive_path="$local_archive_path"
    echo "Latest Google Drive archive copied to the project: $archive_path"
fi
[[ -n "$archive_path" && -f "$archive_path" ]] || {
    echo "The $project_folder_name transfer ZIP was not found. Pass the ZIP path as the first argument." >&2
    exit 1
}
archive_path="$(cd "$(dirname "$archive_path")" && pwd)/$(basename "$archive_path")"

if [[ -f "$archive_path.sha256" ]]; then
    expected_hash="$(awk '{print $1}' "$archive_path.sha256" | head -n 1 | tr '[:upper:]' '[:lower:]')"
    actual_hash="$(shasum -a 256 "$archive_path" | awk '{print $1}' | tr '[:upper:]' '[:lower:]')"
    if [[ -z "$expected_hash" || "$expected_hash" != "$actual_hash" ]]; then
        echo 'The transfer ZIP checksum does not match. Wait for Google Drive to finish syncing and try again.' >&2
        exit 1
    fi
    echo 'Google Drive archive checksum verified.'
elif [[ "$archive_from_google_drive" -eq 1 ]]; then
    echo 'The SHA-256 file has not arrived from Google Drive yet. Wait for syncing to finish and run Start Work again.' >&2
    exit 1
fi

mkdir -p "$backups_path"
temporary_path="$backups_path/.transfer-temp-import-$(date '+%Y%m%d%H%M%S')-$$"
container_import="/tmp/wordpress-site2-import-$(date '+%s')-$$.sql"
container_import_created=0
compose=()
mkdir -p "$temporary_path"

cleanup() {
    if [[ "$container_import_created" -eq 1 && "${#compose[@]}" -gt 0 ]]; then
        "${compose[@]}" exec -T db rm -f "$container_import" >/dev/null 2>&1 || true
    fi
    case "$temporary_path" in
        "$backups_path"/.transfer-temp-import-*) rm -rf -- "$temporary_path" ;;
    esac
}
trap cleanup EXIT

while IFS= read -r entry; do
    case "$entry" in
        /*|../*|*/../*|*/..)
            echo "The ZIP contains an unsafe path: $entry" >&2
            exit 1
            ;;
    esac
done < <(unzip -Z1 "$archive_path")

unzip -q "$archive_path" -d "$temporary_path"
[[ -f "$temporary_path/manifest.json" ]] || {
    echo 'manifest.json is missing from the transfer archive.' >&2
    exit 1
}
grep -q '"format"[[:space:]]*:[[:space:]]*"wordpress-site2-transfer"' "$temporary_path/manifest.json" || {
    echo 'This ZIP is not a supported wordpress_site2 transfer archive.' >&2
    exit 1
}
grep -q '"formatVersion"[[:space:]]*:[[:space:]]*1' "$temporary_path/manifest.json" || {
    echo 'This transfer archive version is not supported.' >&2
    exit 1
}
manifest_project_folder="$(read_manifest_string projectFolderName)"
if [[ -n "$manifest_project_folder" && "$manifest_project_folder" != "$project_folder_name" ]]; then
    echo "This archive belongs to project '$manifest_project_folder', not '$project_folder_name'." >&2
    exit 1
fi
[[ -s "$temporary_path/database.sql" ]] || {
    echo 'database.sql is missing or empty in the transfer archive.' >&2
    exit 1
}

if [[ "$validate_only" -eq 1 ]]; then
    echo 'Transfer archive validation passed.'
    echo "Archive: $archive_path"
    echo 'No local WordPress data was changed.'
    exit 0
fi

command -v docker >/dev/null 2>&1 || {
    echo 'Docker Desktop was not found. Install and start Docker Desktop, then run the import again.' >&2
    exit 1
}

echo
echo 'The import will replace the local WordPress database and uploads with the state from:'
echo "$archive_path"
echo 'The project code is not replaced; update it through GitHub before importing.'
echo 'A recovery archive of the current local state will be created first.'
echo

if [[ "$force" -ne 1 ]]; then
    read -r -p 'Type IMPORT to continue: ' confirmation
    if [[ "$confirmation" != 'IMPORT' ]]; then
        echo 'Import cancelled. No data was changed.'
        exit 3
    fi
fi

if [[ ! -f "$env_path" ]]; then
    [[ -f "$temporary_path/local.env" ]] || {
        echo 'The target project has no .env and the archive contains no local.env.' >&2
        exit 1
    }
    cp "$temporary_path/local.env" "$env_path"
    echo 'Local environment settings restored because this project had no .env.'
else
    echo 'The existing .env was kept so the current Docker database credentials remain valid.'
fi

compose_project_name="$(read_env_value COMPOSE_PROJECT_NAME)"
compose_project_name="${compose_project_name:-$project_folder_name}"
target_site_url="$(read_env_value WP_SITE_URL)"
source_site_url="$(read_manifest_string siteUrl)"
compose=(docker compose --project-name "$compose_project_name" --env-file "$env_path" --file "$compose_path")

echo 'Starting the local WordPress containers...'
"${compose[@]}" up -d db wordpress
database_ready=0
for ((attempt=0; attempt<90; attempt++)); do
    if "${compose[@]}" exec -T db sh -lc 'mariadb --skip-column-names --batch --user="$MARIADB_USER" --password="$MARIADB_PASSWORD" "$MARIADB_DATABASE" -e SELECT@@version >/dev/null 2>&1' >/dev/null 2>&1; then
        database_ready=1
        break
    fi
    sleep 2
done
[[ "$database_ready" -eq 1 ]] || {
    echo 'The WordPress database did not become ready within three minutes.' >&2
    exit 1
}

timestamp="$(date '+%Y%m%d-%H%M%S')"
if [[ "$skip_backup" -ne 1 ]]; then
    [[ -f "$project_path/script-local-export.sh" ]] || {
        echo 'script-local-export.sh is missing, so the required pre-import backup cannot be created.' >&2
        exit 1
    }
    echo 'Creating a recovery archive of the current state...'
    bash "$project_path/script-local-export.sh" \
        --output-dir "$backups_path" \
        --archive-name "$project_folder_name-before-import-$timestamp.zip"
fi

echo 'Restoring WordPress uploads into the container...'
"${compose[@]}" exec -T wordpress sh -lc 'set -eu; mkdir -p /var/www/html/wp-content/uploads; find /var/www/html/wp-content/uploads -mindepth 1 -maxdepth 1 -exec rm -rf -- {} +'
if [[ -d "$temporary_path/uploads" ]]; then
    "${compose[@]}" cp "$temporary_path/uploads/." 'wordpress:/var/www/html/wp-content/uploads'
    "${compose[@]}" exec -T wordpress sh -lc 'chown -R www-data:www-data /var/www/html/wp-content/uploads 2>/dev/null || true'
fi

if [[ ! -f "$project_path/wp-config.php" && -f "$temporary_path/wp-config.php" ]]; then
    cp "$temporary_path/wp-config.php" "$project_path/wp-config.php"
fi

echo 'Restoring the WordPress database...'
"${compose[@]}" cp "$temporary_path/database.sql" "db:$container_import"
container_import_created=1
"${compose[@]}" exec -T db sh -lc \
    'mariadb --default-character-set=utf8mb4 --user="$MARIADB_USER" --password="$MARIADB_PASSWORD" "$MARIADB_DATABASE" < "$1"' \
    _ "$container_import"

if [[ -n "$source_site_url" && -n "$target_site_url" && "$source_site_url" != "$target_site_url" ]]; then
    echo "Updating WordPress URLs from $source_site_url to $target_site_url..."
    "${compose[@]}" run --rm wpcli search-replace "$source_site_url" "$target_site_url" --all-tables-with-prefix --skip-columns=guid
fi

"${compose[@]}" run --rm wpcli rewrite flush
"${compose[@]}" restart wordpress

echo
echo 'Import completed successfully.'
echo "Site: $target_site_url"
upload_file_count="$(sed -n 's/.*"uploadFileCount"[[:space:]]*:[[:space:]]*\([0-9][0-9]*\).*/\1/p' "$temporary_path/manifest.json" | head -n 1)"
echo "WordPress upload files restored from the archive: ${upload_file_count:-0}"
echo 'WordPress database restored: yes'
echo 'The WordPress users, passwords, pages, settings and uploads now match the imported snapshot.'

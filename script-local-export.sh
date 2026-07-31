#!/usr/bin/env bash
set -euo pipefail

project_path="$(cd "$(dirname "$0")" && pwd)"
env_path="$project_path/.env"
compose_path="$project_path/compose.yaml"
uploads_path="$project_path/wp-content/uploads"
backups_path="$project_path/backups"
project_folder_name="$(basename "$project_path")"
output_directory=""
archive_name=""
google_drive_sites_path=""
using_google_drive=0

while [[ $# -gt 0 ]]; do
    case "$1" in
        --output-dir)
            [[ $# -ge 2 && "$2" != --* ]] || { echo '--output-dir requires a path.' >&2; exit 2; }
            output_directory="$2"
            shift 2
            ;;
        --archive-name)
            [[ $# -ge 2 && "$2" != --* ]] || { echo '--archive-name requires a file name.' >&2; exit 2; }
            archive_name="$2"
            shift 2
            ;;
        --google-drive-sites-path)
            [[ $# -ge 2 && "$2" != --* ]] || { echo '--google-drive-sites-path requires a path.' >&2; exit 2; }
            google_drive_sites_path="$2"
            shift 2
            ;;
        *)
            echo "Unknown argument: $1" >&2
            exit 2
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

json_escape() {
    local value="$1"
    value="${value//\\/\\\\}"
    value="${value//\"/\\\"}"
    value="${value//$'\n'/\\n}"
    printf '%s' "$value"
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

command -v docker >/dev/null 2>&1 || {
    echo 'Docker Desktop was not found. Install and start Docker Desktop, then run the export again.' >&2
    exit 1
}
[[ -f "$env_path" ]] || {
    echo 'The local .env file was not found. Start the project once or create .env from .env.example.' >&2
    exit 1
}
[[ -f "$compose_path" ]] || {
    echo 'compose.yaml was not found next to this script.' >&2
    exit 1
}
command -v zip >/dev/null 2>&1 || {
    echo 'The zip command was not found.' >&2
    exit 1
}
command -v git >/dev/null 2>&1 || {
    echo 'Git was not found. It is required to collect the current project files safely.' >&2
    exit 1
}
git -C "$project_path" rev-parse --is-inside-work-tree >/dev/null 2>&1 || {
    echo 'The project folder is not a Git working tree, so its files cannot be collected safely.' >&2
    exit 1
}

compose_project_name="$(read_env_value COMPOSE_PROJECT_NAME)"
compose_project_name="${compose_project_name:-$project_folder_name}"
site_url="$(read_env_value WP_SITE_URL)"
compose=(docker compose --project-name "$compose_project_name" --env-file "$env_path" --file "$compose_path")

if [[ -z "$output_directory" ]]; then
    using_google_drive=1
    sites_path="$(find_google_drive_sites_path)" || {
        echo 'Google Drive folder "Codex Drive" was not found. Start Google Drive Desktop or pass --google-drive-sites-path with its local path.' >&2
        exit 1
    }
    output_directory="$sites_path"
fi
mkdir -p "$output_directory" "$backups_path"
output_directory="$(cd "$output_directory" && pwd)"
if [[ -z "$archive_name" ]]; then
    archive_name="$project_folder_name-transfer-$(date '+%d-%m-%Y_%H-%M').zip"
fi
if [[ "$archive_name" == */* || "$archive_name" != *.zip ]]; then
    echo 'Archive name must be a plain file name ending in .zip.' >&2
    exit 1
fi
archive_path="$output_directory/$archive_name"
[[ ! -e "$archive_path" ]] || {
    echo "The archive already exists: $archive_path" >&2
    exit 1
}

temporary_path="$backups_path/.transfer-temp-export-$(date '+%Y%m%d%H%M%S')-$$"
working_archive_path="$backups_path/.transfer-build-$(date '+%Y%m%d%H%M%S')-$$.zip"
container_dump="/tmp/wordpress-site2-export-$(date '+%s')-$$.sql"
container_dump_created=0
mkdir -p "$temporary_path/uploads"

cleanup() {
    if [[ "$container_dump_created" -eq 1 ]]; then
        "${compose[@]}" exec -T db rm -f "$container_dump" >/dev/null 2>&1 || true
    fi
    case "$temporary_path" in
        "$backups_path"/.transfer-temp-export-*) rm -rf -- "$temporary_path" ;;
    esac
    case "$working_archive_path" in
        "$backups_path"/.transfer-build-*) rm -f -- "$working_archive_path" ;;
    esac
    case "${archive_path:-}" in
        "$output_directory"/*.zip) rm -f -- "$archive_path.uploading" ;;
    esac
}
trap cleanup EXIT

echo 'Starting the WordPress database...'
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

echo 'Creating a consistent database dump...'
"${compose[@]}" exec -T db sh -lc \
    'mariadb-dump --single-transaction --quick --skip-lock-tables --default-character-set=utf8mb4 --user="$MARIADB_USER" --password="$MARIADB_PASSWORD" "$MARIADB_DATABASE" > "$1"' \
    _ "$container_dump"
container_dump_created=1
"${compose[@]}" cp "db:$container_dump" "$temporary_path/database.sql"
[[ -s "$temporary_path/database.sql" ]] || {
    echo 'The database dump is empty. The transfer archive was not created.' >&2
    exit 1
}

cp "$env_path" "$temporary_path/local.env"
wp_config_included=false
if [[ -f "$project_path/wp-config.php" ]]; then
    cp "$project_path/wp-config.php" "$temporary_path/wp-config.php"
    wp_config_included=true
fi
local_admin_included=false
if [[ -f "$project_path/.local-admin.txt" ]]; then
    cp "$project_path/.local-admin.txt" "$temporary_path/local-admin.txt"
    local_admin_included=true
fi
echo 'Copying WordPress uploads from the container...'
"${compose[@]}" exec -T wordpress sh -lc 'mkdir -p /var/www/html/wp-content/uploads'
"${compose[@]}" cp 'wordpress:/var/www/html/wp-content/uploads/.' "$temporary_path/uploads"

echo 'Copying the current project files and work scripts...'
project_files_path="$temporary_path/project-files"
mkdir -p "$project_files_path"
while IFS= read -r -d '' relative_path; do
    case "$relative_path" in
        ''|/*|../*|*/../*|*/..)
            echo "Git returned an unsafe project path: $relative_path" >&2
            exit 1
            ;;
    esac

    source_path="$project_path/$relative_path"
    [[ -f "$source_path" ]] || continue
    destination_path="$project_files_path/$relative_path"
    mkdir -p "$(dirname "$destination_path")"
    cp -p "$source_path" "$destination_path"
done < <(git -C "$project_path" -c core.quotepath=false ls-files --cached --others --exclude-standard -z)

for required_script in \
    'script-local-export.sh' \
    'script-local-export.ps1' \
    'script-local-export.command' \
    'script-local-export.cmd' \
    'script-local-import.sh' \
    'script-local-import.ps1' \
    'script-local-import.command' \
    'script-local-import.cmd' \
    'script-project-handoff.sh' \
    'script-project-handoff.ps1' \
    'script-static-export.sh' \
    'script-static-export.ps1' \
    'script-static-export.php' \
    'Начать работу.command' \
    'Начать работу.cmd' \
    'Закончить работу.command' \
    'Закончить работу.cmd'; do
    [[ -f "$project_files_path/$required_script" ]] || {
        echo "A required work script was not collected: $required_script" >&2
        exit 1
    }
done
[[ -f "$project_files_path/PROJECT_HANDOFF.md" ]] || {
    echo 'PROJECT_HANDOFF.md was not collected into the transfer archive.' >&2
    exit 1
}

upload_file_count="$(find "$temporary_path/uploads" -type f | wc -l | tr -d ' ')"
upload_bytes="$(du -sk "$temporary_path/uploads" | awk '{print $1 * 1024}')"
project_file_count="$(find "$project_files_path" -type f | wc -l | tr -d ' ')"
project_files_bytes="$(du -sk "$project_files_path" | awk '{print $1 * 1024}')"
created_at_utc="$(date -u '+%Y-%m-%dT%H:%M:%SZ')"

printf '%s\n' \
    '{' \
    '  "format": "wordpress-site2-transfer",' \
    '  "formatVersion": 2,' \
    "  \"createdAtUtc\": \"$(json_escape "$created_at_utc")\"," \
    '  "sourcePlatform": "macOS",' \
    "  \"projectFolderName\": \"$(json_escape "$project_folder_name")\"," \
    "  \"composeProjectName\": \"$(json_escape "$compose_project_name")\"," \
    "  \"siteUrl\": \"$(json_escape "$site_url")\"," \
    '  "databaseFile": "database.sql",' \
    '  "uploadsDirectory": "uploads",' \
    '  "projectFilesDirectory": "project-files",' \
    "  \"uploadFileCount\": $upload_file_count," \
    "  \"uploadBytes\": $upload_bytes," \
    "  \"projectFileCount\": $project_file_count," \
    "  \"projectFilesBytes\": $project_files_bytes," \
    '  "includesEnvironment": true,' \
    "  \"includesWpConfig\": $wp_config_included," \
    "  \"includesLocalAdminFile\": $local_admin_included," \
    '  "includesProjectFiles": true,' \
    '  "includesWorkScripts": true,' \
    '  "includesProjectHandoff": true,' \
    '  "privateArchive": true' \
    '}' > "$temporary_path/manifest.json"
transfer_file_count="$(find "$temporary_path" -type f | wc -l | tr -d ' ')"

echo 'Packing the private transfer archive...'
(
    cd "$temporary_path"
    zip -qry "$working_archive_path" .
)
mv "$working_archive_path" "$archive_path.uploading"
mv "$archive_path.uploading" "$archive_path"
if command -v shasum >/dev/null 2>&1; then
    archive_hash="$(shasum -a 256 "$archive_path" | awk '{print $1}')"
    printf '%s  %s' "$archive_hash" "$archive_name" > "$archive_path.sha256"
fi

echo
echo 'Transfer archive created successfully:'
echo "$archive_path"
echo "Files packed into the archive: $transfer_file_count"
if [[ -n "${archive_hash:-}" ]]; then
    echo "SHA-256: $archive_hash"
fi
if [[ "$using_google_drive" -eq 1 ]]; then
    echo "Google Drive folder: $output_directory"
    echo 'Wait until Google Drive shows that syncing is complete before importing on the other computer.'
else
    echo "Output folder: $output_directory"
fi
echo 'WARNING: this ZIP contains the database, WordPress accounts and local secrets. Keep it private and never upload it to GitHub.'

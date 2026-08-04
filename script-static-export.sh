#!/usr/bin/env bash
set -euo pipefail

project_path="$(cd "$(dirname "$0")" && pwd)"
env_path="$project_path/.env"
compose_path="$project_path/compose.yaml"

[[ -f "$env_path" ]] || {
    echo 'The local .env file was not found. Run local-setup.sh first.' >&2
    exit 1
}
[[ -f "$compose_path" ]] || {
    echo 'compose.yaml was not found next to this script.' >&2
    exit 1
}

read_env_value() {
    local key="$1"
    sed -n "s/^${key}=//p" "$env_path" | tail -n 1 | tr -d "'\""
}

compose_project_name="$(read_env_value COMPOSE_PROJECT_NAME)"
compose_project_name="${compose_project_name:-wordpress_site3}"
wordpress_port="$(read_env_value WORDPRESS_PORT)"
wordpress_port="${wordpress_port:-8082}"
compose=(docker compose --project-name "$compose_project_name" --env-file "$env_path" --file "$compose_path")

echo 'Starting WordPress for the static export...'
"${compose[@]}" up -d db wordpress

"${compose[@]}" exec -T wordpress php /var/www/html/script-static-export.php \
    --source-url="http://localhost:${wordpress_port}" \
    --output=/var/www/html/index.html

echo 'GitHub Pages snapshots are ready in index.html and static-pages/.'

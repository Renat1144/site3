#!/usr/bin/env bash
set -euo pipefail

project_path="$(cd "$(dirname "$0")" && pwd)"
handoff_path="$project_path/PROJECT_HANDOFF.md"
env_path="$project_path/.env"
compose_path="$project_path/compose.yaml"

[[ -f "$handoff_path" ]] || {
    echo 'PROJECT_HANDOFF.md was not found. The technical handoff snapshot was not written.' >&2
    exit 1
}

timestamp="$(date '+%Y-%m-%d %H:%M:%S %Z')"
platform="$(uname -s 2>/dev/null || printf 'unknown')"
branch='Git is unavailable'
commit='Git is unavailable'
git_status=''

if command -v git >/dev/null 2>&1 && git -C "$project_path" rev-parse --is-inside-work-tree >/dev/null 2>&1; then
    branch="$(git -C "$project_path" branch --show-current 2>/dev/null || true)"
    branch="${branch:-detached HEAD}"
    commit="$(git -C "$project_path" rev-parse --short HEAD 2>/dev/null || true)"
    commit="${commit:-unavailable}"
    git_status="$(git -C "$project_path" -c core.quotepath=false status --short --untracked-files=all 2>/dev/null || true)"
fi

docker_status='Not checked: Docker Compose is unavailable.'
if command -v docker >/dev/null 2>&1 && [[ -f "$env_path" && -f "$compose_path" ]]; then
    docker_output="$(docker compose --env-file "$env_path" --file "$compose_path" ps --format '{{.Service}}: {{.State}}' 2>/dev/null || true)"
    if [[ -n "$docker_output" ]]; then
        docker_status="$docker_output"
    else
        docker_status='No running project services were reported.'
    fi
fi

{
    printf '\n### Завершение работы: %s\n\n' "$timestamp"
    printf -- '- Платформа: `%s`\n' "$platform"
    printf -- '- Ветка Git: `%s`\n' "$branch"
    printf -- '- Commit: `%s`\n' "$commit"
    if [[ -n "$git_status" ]]; then
        printf -- '- Изменённые и новые файлы:\n\n'
        printf '```text\n%s\n```\n\n' "$git_status"
    else
        printf -- '- Изменённые и новые файлы: нет.\n\n'
    fi
    printf -- '- Состояние Docker Compose:\n\n'
    printf '```text\n%s\n```\n' "$docker_status"
} >> "$handoff_path"

echo "Technical project handoff appended: $handoff_path"

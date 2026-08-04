#!/bin/bash

script_dir="$(cd "$(dirname "$0")" && pwd)"
cd "$script_dir" || exit 1

clear
echo 'Start WordPress work'
echo '===================='
echo

/bin/bash "$script_dir/script-local-import.sh" --force
exit_code=$?

echo
if [[ "$exit_code" -eq 0 ]]; then
    echo 'The latest project state was restored successfully.'
else
    echo "Import failed with exit code $exit_code. Review the message above."
fi
echo
echo 'This window will close automatically in 10 seconds.'
sleep 10
exit "$exit_code"

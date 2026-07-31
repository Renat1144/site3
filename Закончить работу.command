#!/bin/bash

script_dir="$(cd "$(dirname "$0")" && pwd)"
cd "$script_dir" || exit 1

clear
echo 'Finish WordPress work'
echo '====================='
echo

/bin/bash "$script_dir/script-static-export.sh"
static_exit_code=$?
if [[ "$static_exit_code" -ne 0 ]]; then
    echo
    echo "Static page export failed with exit code $static_exit_code. Review the message above."
    echo 'The private transfer archive was not created.'
    echo
    echo 'This window will close automatically in 10 seconds.'
    sleep 10
    exit "$static_exit_code"
fi

/bin/bash "$script_dir/script-local-export.sh"
exit_code=$?

echo
if [[ "$exit_code" -eq 0 ]]; then
    echo 'Archive created successfully in Google Drive - Codex Drive.'
    echo 'Wait for Google Drive synchronization before switching devices.'
else
    echo "Export failed with exit code $exit_code. Review the message above."
fi
echo
echo 'This window will close automatically in 10 seconds.'
sleep 10
exit "$exit_code"

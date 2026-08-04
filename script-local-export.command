#!/bin/bash
script_dir="$(cd "$(dirname "$0")" && pwd)"
cd "$script_dir" || exit 1
/bin/bash "$script_dir/script-local-export.sh"
exit_code=$?
echo
echo 'This window will close automatically in 10 seconds.'
sleep 10
exit "$exit_code"

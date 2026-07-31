[CmdletBinding()]
param()

$ErrorActionPreference = 'Stop'
Set-StrictMode -Version Latest

$projectPath = Split-Path -Parent $MyInvocation.MyCommand.Path
$handoffPath = Join-Path $projectPath 'PROJECT_HANDOFF.md'
$envPath = Join-Path $projectPath '.env'
$composePath = Join-Path $projectPath 'compose.yaml'

if (-not (Test-Path -LiteralPath $handoffPath -PathType Leaf)) {
    throw 'PROJECT_HANDOFF.md was not found. The technical handoff snapshot was not written.'
}

$timestamp = Get-Date -Format 'yyyy-MM-dd HH:mm:ss K'
$branch = 'Git is unavailable'
$commit = 'Git is unavailable'
$gitStatus = @()

if (Get-Command git -ErrorAction SilentlyContinue) {
    & git -C $projectPath rev-parse --is-inside-work-tree *> $null
    if ($LASTEXITCODE -eq 0) {
        $branchOutput = @(& git -C $projectPath branch --show-current 2>$null)
        $branch = if ($LASTEXITCODE -eq 0 -and $branchOutput.Count -gt 0 -and $branchOutput[0]) { $branchOutput[0] } else { 'detached HEAD' }
        $commitOutput = @(& git -C $projectPath rev-parse --short HEAD 2>$null)
        $commit = if ($LASTEXITCODE -eq 0 -and $commitOutput.Count -gt 0 -and $commitOutput[0]) { $commitOutput[0] } else { 'unavailable' }
        $gitStatus = @(& git -C $projectPath -c core.quotepath=false status --short --untracked-files=all 2>$null)
    }
}

$dockerStatus = @('Not checked: Docker Compose is unavailable.')
if ((Get-Command docker -ErrorAction SilentlyContinue) -and
    (Test-Path -LiteralPath $envPath -PathType Leaf) -and
    (Test-Path -LiteralPath $composePath -PathType Leaf)) {
    $dockerOutput = @(& docker compose --env-file $envPath --file $composePath ps --format '{{.Service}}: {{.State}}' 2>$null)
    if ($LASTEXITCODE -eq 0 -and $dockerOutput.Count -gt 0) {
        $dockerStatus = $dockerOutput
    } else {
        $dockerStatus = @('No running project services were reported.')
    }
}

$lines = [System.Collections.Generic.List[string]]::new()
$lines.Add('')
$lines.Add("### Завершение работы: $timestamp")
$lines.Add('')
$lines.Add('- Платформа: `Windows`')
$lines.Add("- Ветка Git: ``$branch``")
$lines.Add("- Commit: ``$commit``")
if ($gitStatus.Count -gt 0) {
    $lines.Add('- Изменённые и новые файлы:')
    $lines.Add('')
    $lines.Add('```text')
    foreach ($statusLine in $gitStatus) {
        $lines.Add([string]$statusLine)
    }
    $lines.Add('```')
    $lines.Add('')
} else {
    $lines.Add('- Изменённые и новые файлы: нет.')
    $lines.Add('')
}
$lines.Add('- Состояние Docker Compose:')
$lines.Add('')
$lines.Add('```text')
foreach ($dockerLine in $dockerStatus) {
    $lines.Add([string]$dockerLine)
}
$lines.Add('```')

$utf8WithoutBom = [System.Text.UTF8Encoding]::new($false)
[System.IO.File]::AppendAllText($handoffPath, (($lines -join [Environment]::NewLine) + [Environment]::NewLine), $utf8WithoutBom)

Write-Host "Technical project handoff appended: $handoffPath" -ForegroundColor Green

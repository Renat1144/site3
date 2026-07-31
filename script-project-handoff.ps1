[CmdletBinding()]
param()

$ErrorActionPreference = 'Stop'
Set-StrictMode -Version Latest

function Find-GitExecutable {
    $candidates = [System.Collections.Generic.List[string]]::new()
    $gitCommand = Get-Command git.exe -ErrorAction SilentlyContinue | Select-Object -First 1
    if ($gitCommand -and $gitCommand.Source) {
        $candidates.Add($gitCommand.Source)
    }

    if ($env:ProgramFiles) {
        $candidates.Add((Join-Path $env:ProgramFiles 'Git\cmd\git.exe'))
    }
    $programFilesX86 = [Environment]::GetEnvironmentVariable('ProgramFiles(x86)')
    if ($programFilesX86) {
        $candidates.Add((Join-Path $programFilesX86 'Git\cmd\git.exe'))
    }
    if ($env:LOCALAPPDATA) {
        $candidates.Add((Join-Path $env:LOCALAPPDATA 'Programs\Git\cmd\git.exe'))
        $githubDesktopGit = Get-ChildItem -Path (Join-Path $env:LOCALAPPDATA 'GitHubDesktop\app-*\resources\app\git\cmd\git.exe') -File -ErrorAction SilentlyContinue |
            Sort-Object LastWriteTimeUtc -Descending |
            Select-Object -First 1
        if ($githubDesktopGit) {
            $candidates.Add($githubDesktopGit.FullName)
        }
    }
    if ($env:USERPROFILE) {
        $candidates.Add((Join-Path $env:USERPROFILE 'scoop\apps\git\current\cmd\git.exe'))
        $codexRuntimeGit = Get-ChildItem -Path (Join-Path $env:USERPROFILE '.cache\codex-runtimes\*\dependencies\native\git\cmd\git.exe') -File -ErrorAction SilentlyContinue |
            Sort-Object LastWriteTimeUtc -Descending |
            Select-Object -First 1
        if ($codexRuntimeGit) {
            $candidates.Add($codexRuntimeGit.FullName)
        }
    }

    foreach ($candidate in @($candidates | Select-Object -Unique)) {
        if ($candidate -and (Test-Path -LiteralPath $candidate -PathType Leaf)) {
            return [System.IO.Path]::GetFullPath($candidate)
        }
    }

    return $null
}

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

$gitExecutable = Find-GitExecutable
if ($gitExecutable) {
    & $gitExecutable -C $projectPath rev-parse --is-inside-work-tree *> $null
    if ($LASTEXITCODE -eq 0) {
        $branchOutput = @(& $gitExecutable -C $projectPath branch --show-current 2>$null)
        $branch = if ($LASTEXITCODE -eq 0 -and $branchOutput.Count -gt 0 -and $branchOutput[0]) { $branchOutput[0] } else { 'detached HEAD' }
        $commitOutput = @(& $gitExecutable -C $projectPath rev-parse --short HEAD 2>$null)
        $commit = if ($LASTEXITCODE -eq 0 -and $commitOutput.Count -gt 0 -and $commitOutput[0]) { $commitOutput[0] } else { 'unavailable' }
        $gitStatus = @(& $gitExecutable -C $projectPath -c core.quotepath=false status --short --untracked-files=all 2>$null)
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

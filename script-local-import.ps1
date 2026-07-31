[CmdletBinding()]
param(
    [string]$ArchivePath,
    [switch]$Force,
    [switch]$SkipBackup,
    [switch]$ValidateOnly,
    [string]$GoogleDriveSitesPath
)

$ErrorActionPreference = 'Stop'
Set-StrictMode -Version Latest

function Read-DotEnv {
    param([Parameter(Mandatory = $true)][string]$Path)

    $values = @{}
    Get-Content -LiteralPath $Path -Encoding UTF8 | ForEach-Object {
        if ($_ -match '^\s*([A-Za-z_][A-Za-z0-9_]*)=(.*)\s*$') {
            $value = $matches[2].Trim()
            if ($value.Length -ge 2 -and (
                ($value.StartsWith('"') -and $value.EndsWith('"')) -or
                ($value.StartsWith("'") -and $value.EndsWith("'"))
            )) {
                $value = $value.Substring(1, $value.Length - 2)
            }
            $values[$matches[1]] = $value
        }
    }
    return $values
}

function Invoke-Compose {
    param([Parameter(Mandatory = $true)][string[]]$Arguments)

    & docker @script:composeBaseArguments @Arguments
    if ($LASTEXITCODE -ne 0) {
        throw "Docker Compose command failed with exit code $LASTEXITCODE."
    }
}

function Find-GoogleDriveSitesPath {
    param([string]$ExplicitPath)

    if ($ExplicitPath) {
        return [System.IO.Path]::GetFullPath($ExplicitPath)
    }

    $candidates = @(
        'G:\Мой диск\Codex Drive',
        'G:\My Drive\Codex Drive',
        'G:\Codex Drive'
    )
    if ($env:CODEX_DRIVE_PATH) {
        $candidates += $env:CODEX_DRIVE_PATH
    }
    if ($env:GOOGLE_DRIVE_SITES_PATH) {
        $candidates += $env:GOOGLE_DRIVE_SITES_PATH
    }

    $fileSystemDrives = @(Get-PSDrive -PSProvider FileSystem -ErrorAction SilentlyContinue)
    foreach ($drive in $fileSystemDrives) {
        $isGoogleDrive = ($drive.Description -like '*Google Drive*') -or ($drive.DisplayRoot -like '*Google Drive*')
        if ($isGoogleDrive -or $drive.Name -eq 'G') {
            $candidates += (Join-Path $drive.Root 'Мой диск\Codex Drive')
            $candidates += (Join-Path $drive.Root 'My Drive\Codex Drive')
            $candidates += (Join-Path $drive.Root 'Codex Drive')
            $topLevelFolders = @(Get-ChildItem -LiteralPath $drive.Root -Directory -Force -ErrorAction SilentlyContinue)
            foreach ($topLevelFolder in $topLevelFolders) {
                $candidates += (Join-Path $topLevelFolder.FullName 'Codex Drive')
            }
        }
    }

    $candidates += (Join-Path $env:USERPROFILE 'Google Drive\Codex Drive')
    $candidates += (Join-Path $env:USERPROFILE 'My Drive\Codex Drive')
    $candidates += (Join-Path $env:USERPROFILE 'Мой диск\Codex Drive')

    $uniqueCandidates = @($candidates | Select-Object -Unique)
    foreach ($candidate in $uniqueCandidates) {
        if ($candidate -and (Test-Path -LiteralPath $candidate -PathType Container)) {
            return [System.IO.Path]::GetFullPath($candidate)
        }
    }

    throw 'Google Drive folder "Codex Drive" was not found. Start Google Drive Desktop or pass -GoogleDriveSitesPath with the local path to that folder.'
}

$projectPath = Split-Path -Parent $MyInvocation.MyCommand.Path
$envPath = Join-Path $projectPath '.env'
$composePath = Join-Path $projectPath 'compose.yaml'
$exportScript = Join-Path $projectPath 'script-local-export.ps1'
$backupsPath = Join-Path $projectPath 'backups'
$uploadsPath = Join-Path $projectPath 'wp-content\uploads'
$projectFolderName = Split-Path $projectPath -Leaf

if (-not (Test-Path -LiteralPath $composePath -PathType Leaf)) {
    throw 'compose.yaml was not found next to this script.'
}

$archiveFromGoogleDrive = -not [bool]$ArchivePath
if ($archiveFromGoogleDrive) {
    $sitesPath = Find-GoogleDriveSitesPath -ExplicitPath $GoogleDriveSitesPath
    if (-not (Test-Path -LiteralPath $sitesPath -PathType Container)) {
        throw "The Google Drive sites folder was not found: $sitesPath"
    }

    $latestArchive = Get-ChildItem -LiteralPath $sitesPath -Filter "$projectFolderName-transfer-*.zip" -File -ErrorAction SilentlyContinue |
        Sort-Object LastWriteTimeUtc -Descending |
        Select-Object -First 1
    if (-not $latestArchive) {
        throw "No $projectFolderName transfer ZIP was found in Google Drive: $sitesPath"
    }

    $incomingPath = Join-Path $backupsPath 'incoming'
    New-Item -ItemType Directory -Path $incomingPath -Force | Out-Null
    $localArchivePath = Join-Path $incomingPath $latestArchive.Name
    Copy-Item -LiteralPath $latestArchive.FullName -Destination $localArchivePath -Force
    $cloudChecksumPath = $latestArchive.FullName + '.sha256'
    if (Test-Path -LiteralPath $cloudChecksumPath -PathType Leaf) {
        Copy-Item -LiteralPath $cloudChecksumPath -Destination ($localArchivePath + '.sha256') -Force
    }
    $ArchivePath = $localArchivePath
    Write-Host "Latest Google Drive archive copied to the local incoming folder: $ArchivePath"
}
$ArchivePath = [System.IO.Path]::GetFullPath($ArchivePath)
if (-not (Test-Path -LiteralPath $ArchivePath -PathType Leaf)) {
    throw "The transfer archive was not found: $ArchivePath"
}

$checksumPath = $ArchivePath + '.sha256'
if (Test-Path -LiteralPath $checksumPath -PathType Leaf) {
    $checksumText = (Get-Content -LiteralPath $checksumPath -Raw -Encoding ASCII).Trim()
    $expectedHash = ($checksumText -split '\s+')[0]
    $actualHash = (Get-FileHash -LiteralPath $ArchivePath -Algorithm SHA256).Hash
    if (-not $expectedHash -or $expectedHash -ne $actualHash) {
        throw 'The transfer ZIP checksum does not match. Wait for Google Drive to finish syncing and try again.'
    }
    Write-Host 'Google Drive archive checksum verified.'
} elseif ($archiveFromGoogleDrive) {
    throw 'The SHA-256 file has not arrived from Google Drive yet. Wait for syncing to finish and run Start Work again.'
}

$temporaryRoot = Join-Path $projectPath 'backups'
New-Item -ItemType Directory -Path $temporaryRoot -Force | Out-Null
$temporaryPath = Join-Path $temporaryRoot ('.transfer-temp-import-' + [Guid]::NewGuid().ToString('N'))
New-Item -ItemType Directory -Path $temporaryPath | Out-Null
$containerImport = '/tmp/wordpress-site2-import-{0}.sql' -f [Guid]::NewGuid().ToString('N')
$containerImportCreated = $false

try {
    Add-Type -AssemblyName System.IO.Compression.FileSystem
    $archive = [System.IO.Compression.ZipFile]::OpenRead($ArchivePath)
    try {
        $safeRoot = [System.IO.Path]::GetFullPath($temporaryPath).TrimEnd([System.IO.Path]::DirectorySeparatorChar) + [System.IO.Path]::DirectorySeparatorChar
        foreach ($entry in $archive.Entries) {
            $entryPath = [System.IO.Path]::GetFullPath((Join-Path $temporaryPath $entry.FullName))
            if (-not $entryPath.StartsWith($safeRoot, [System.StringComparison]::OrdinalIgnoreCase)) {
                throw "The ZIP contains an unsafe path: $($entry.FullName)"
            }
        }
    } finally {
        $archive.Dispose()
    }

    Expand-Archive -LiteralPath $ArchivePath -DestinationPath $temporaryPath

    $manifestPath = Join-Path $temporaryPath 'manifest.json'
    $databasePath = Join-Path $temporaryPath 'database.sql'
    $incomingEnvPath = Join-Path $temporaryPath 'local.env'
    $incomingUploadsPath = Join-Path $temporaryPath 'uploads'
    $incomingWpConfigPath = Join-Path $temporaryPath 'wp-config.php'

    if (-not (Test-Path -LiteralPath $manifestPath -PathType Leaf)) {
        throw 'manifest.json is missing from the transfer archive.'
    }
    $manifest = Get-Content -LiteralPath $manifestPath -Raw -Encoding UTF8 | ConvertFrom-Json
    if ($manifest.format -ne 'wordpress-site2-transfer' -or [int]$manifest.formatVersion -ne 1) {
        throw 'This ZIP is not a supported wordpress_site2 transfer archive.'
    }
    if ($manifest.PSObject.Properties.Name -contains 'projectFolderName' -and $manifest.projectFolderName -and [string]$manifest.projectFolderName -ne $projectFolderName) {
        throw "This archive belongs to project '$($manifest.projectFolderName)', not '$projectFolderName'."
    }
    if (-not (Test-Path -LiteralPath $databasePath -PathType Leaf) -or (Get-Item -LiteralPath $databasePath).Length -eq 0) {
        throw 'database.sql is missing or empty in the transfer archive.'
    }

    if ($ValidateOnly) {
        Write-Host 'Transfer archive validation passed.' -ForegroundColor Green
        Write-Host "Archive: $ArchivePath"
        Write-Host "Created: $($manifest.createdAtUtc)"
        Write-Host "Upload files: $($manifest.uploadFileCount)"
        Write-Host 'No local WordPress data was changed.'
        return
    }

    if (-not (Get-Command docker -ErrorAction SilentlyContinue)) {
        throw 'Docker Desktop was not found. Install and start Docker Desktop, then run the import again.'
    }

    Write-Host ''
    Write-Host 'The import will replace the local WordPress database and uploads with the state from:' -ForegroundColor Yellow
    Write-Host $ArchivePath
    Write-Host 'The project code is not replaced; update it through GitHub before importing.'
    Write-Host 'A recovery archive of the current local state will be created first.'
    Write-Host ''

    if (-not $Force) {
        $confirmation = Read-Host 'Type IMPORT to continue'
        if ($confirmation -cne 'IMPORT') {
            Write-Host 'Import cancelled. No data was changed.'
            exit 3
        }
    }

    if (-not (Test-Path -LiteralPath $envPath -PathType Leaf)) {
        if (-not (Test-Path -LiteralPath $incomingEnvPath -PathType Leaf)) {
            throw 'The target project has no .env and the archive contains no local.env.'
        }
        Copy-Item -LiteralPath $incomingEnvPath -Destination $envPath
        Write-Host 'Local environment settings restored because this project had no .env.'
    } else {
        Write-Host 'The existing .env was kept so the current Docker database credentials remain valid.'
    }

    $settings = Read-DotEnv -Path $envPath
    $composeProjectName = if ($settings.ContainsKey('COMPOSE_PROJECT_NAME') -and $settings['COMPOSE_PROJECT_NAME']) {
        $settings['COMPOSE_PROJECT_NAME']
    } else {
        $projectFolderName
    }
    $script:composeBaseArguments = @(
        'compose',
        '--project-name', $composeProjectName,
        '--env-file', $envPath,
        '--file', $composePath
    )

    Write-Host 'Starting the local WordPress containers...'
    Invoke-Compose -Arguments @('up', '-d', 'db', 'wordpress')

    $databaseReady = $false
    $deadline = (Get-Date).AddMinutes(3)
    do {
        $previousErrorActionPreference = $ErrorActionPreference
        $ErrorActionPreference = 'SilentlyContinue'
        & docker @script:composeBaseArguments exec -T db sh -lc 'mariadb --skip-column-names --batch --user="$MARIADB_USER" --password="$MARIADB_PASSWORD" "$MARIADB_DATABASE" -e SELECT@@version >/dev/null 2>&1' *> $null
        $databaseCheckExitCode = $LASTEXITCODE
        $ErrorActionPreference = $previousErrorActionPreference
        if ($databaseCheckExitCode -eq 0) {
            $databaseReady = $true
            break
        }
        Start-Sleep -Seconds 2
    } while ((Get-Date) -lt $deadline)
    if (-not $databaseReady) {
        throw 'The WordPress database did not become ready within three minutes.'
    }

    $timestamp = Get-Date -Format 'yyyyMMdd-HHmmss'
    if (-not $SkipBackup) {
        if (-not (Test-Path -LiteralPath $exportScript -PathType Leaf)) {
            throw 'script-local-export.ps1 is missing, so the required pre-import backup cannot be created.'
        }
        New-Item -ItemType Directory -Path $backupsPath -Force | Out-Null
        Write-Host 'Creating a recovery archive of the current state...'
        & powershell.exe -NoProfile -ExecutionPolicy Bypass -File $exportScript `
            -OutputDirectory $backupsPath `
            -ArchiveName "$projectFolderName-before-import-$timestamp.zip"
        if ($LASTEXITCODE -ne 0) {
            throw 'The safety backup failed. Import was stopped before any data was replaced.'
        }
    }

    Write-Host 'Restoring WordPress uploads into the container...'
    Invoke-Compose -Arguments @('exec', '-T', 'wordpress', 'sh', '-lc', 'set -eu; mkdir -p /var/www/html/wp-content/uploads; find /var/www/html/wp-content/uploads -mindepth 1 -maxdepth 1 -exec rm -rf -- {} +')
    if (Test-Path -LiteralPath $incomingUploadsPath -PathType Container) {
        Invoke-Compose -Arguments @('cp', ($incomingUploadsPath + '\.'), 'wordpress:/var/www/html/wp-content/uploads')
        Invoke-Compose -Arguments @('exec', '-T', 'wordpress', 'sh', '-lc', 'chown -R www-data:www-data /var/www/html/wp-content/uploads 2>/dev/null || true')
    }

    if (-not (Test-Path -LiteralPath (Join-Path $projectPath 'wp-config.php') -PathType Leaf) -and (Test-Path -LiteralPath $incomingWpConfigPath -PathType Leaf)) {
        Copy-Item -LiteralPath $incomingWpConfigPath -Destination (Join-Path $projectPath 'wp-config.php')
    }

    Write-Host 'Restoring the WordPress database...'
    Invoke-Compose -Arguments @('cp', $databasePath, ('db:' + $containerImport))
    $containerImportCreated = $true
    $importCommand = 'mariadb --default-character-set=utf8mb4 --user="$MARIADB_USER" --password="$MARIADB_PASSWORD" "$MARIADB_DATABASE" < "' + $containerImport + '"'
    Invoke-Compose -Arguments @('exec', '-T', 'db', 'sh', '-lc', $importCommand)

    $targetSiteUrl = if ($settings.ContainsKey('WP_SITE_URL')) { $settings['WP_SITE_URL'] } else { '' }
    $sourceSiteUrl = if ($manifest.siteUrl) { [string]$manifest.siteUrl } else { '' }
    if ($sourceSiteUrl -and $targetSiteUrl -and $sourceSiteUrl -ne $targetSiteUrl) {
        Write-Host "Updating WordPress URLs from $sourceSiteUrl to $targetSiteUrl..."
        Invoke-Compose -Arguments @('run', '--rm', 'wpcli', 'search-replace', $sourceSiteUrl, $targetSiteUrl, '--all-tables-with-prefix', '--skip-columns=guid')
    }

    Invoke-Compose -Arguments @('run', '--rm', 'wpcli', 'rewrite', 'flush')
    Invoke-Compose -Arguments @('restart', 'wordpress')

    Write-Host ''
    Write-Host 'Import completed successfully.' -ForegroundColor Green
    Write-Host "Site: $targetSiteUrl"
    Write-Host "WordPress upload files restored from the archive: $([int]$manifest.uploadFileCount)"
    Write-Host 'WordPress database restored: yes'
    Write-Host 'The WordPress users, passwords, pages, settings and uploads now match the imported snapshot.'
} finally {
    if ($containerImportCreated -and $script:composeBaseArguments) {
        & docker @script:composeBaseArguments exec -T db rm -f $containerImport *> $null
    }
    if (Test-Path -LiteralPath $temporaryPath) {
        Remove-Item -LiteralPath $temporaryPath -Recurse -Force
    }
}






[CmdletBinding()]
param(
    [string]$OutputDirectory,
    [string]$ArchiveName,
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
$uploadsPath = Join-Path $projectPath 'wp-content\uploads'
$projectFolderName = Split-Path $projectPath -Leaf

if (-not (Get-Command docker -ErrorAction SilentlyContinue)) {
    throw 'Docker Desktop was not found. Install and start Docker Desktop, then run the export again.'
}
if (-not (Test-Path -LiteralPath $envPath -PathType Leaf)) {
    throw 'The local .env file was not found. Start the project once or create .env from .env.example.'
}
if (-not (Test-Path -LiteralPath $composePath -PathType Leaf)) {
    throw 'compose.yaml was not found next to this script.'
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

$usingGoogleDrive = -not [bool]$OutputDirectory
if ($usingGoogleDrive) {
    $sitesPath = Find-GoogleDriveSitesPath -ExplicitPath $GoogleDriveSitesPath
    $OutputDirectory = $sitesPath
}
$OutputDirectory = [System.IO.Path]::GetFullPath($OutputDirectory)
New-Item -ItemType Directory -Path $OutputDirectory -Force | Out-Null

if (-not $ArchiveName) {
    $ArchiveName = '{0}-transfer-{1}.zip' -f $projectFolderName, (Get-Date -Format 'dd-MM-yyyy_HH-mm')
}
if ($ArchiveName -ne [System.IO.Path]::GetFileName($ArchiveName) -or -not $ArchiveName.EndsWith('.zip', [System.StringComparison]::OrdinalIgnoreCase)) {
    throw 'ArchiveName must be a plain file name ending in .zip.'
}

$archivePath = Join-Path $OutputDirectory $ArchiveName
$uploadingArchivePath = $archivePath + '.uploading'
if (Test-Path -LiteralPath $archivePath) {
    throw "The archive already exists: $archivePath"
}

$temporaryRoot = Join-Path $projectPath 'backups'
New-Item -ItemType Directory -Path $temporaryRoot -Force | Out-Null
$temporaryPath = Join-Path $temporaryRoot ('.transfer-temp-export-' + [Guid]::NewGuid().ToString('N'))
$workingArchivePath = Join-Path $temporaryRoot ('.transfer-build-' + [Guid]::NewGuid().ToString('N') + '.zip')
$containerDump = '/tmp/wordpress-site2-export-{0}.sql' -f [Guid]::NewGuid().ToString('N')
$containerDumpCreated = $false
New-Item -ItemType Directory -Path $temporaryPath | Out-Null

try {
    Write-Host 'Starting the WordPress database...'
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

    Write-Host 'Creating a consistent database dump...'
    $dumpCommand = 'mariadb-dump --single-transaction --quick --skip-lock-tables --default-character-set=utf8mb4 --user="$MARIADB_USER" --password="$MARIADB_PASSWORD" "$MARIADB_DATABASE" > "' + $containerDump + '"'
    Invoke-Compose -Arguments @('exec', '-T', 'db', 'sh', '-lc', $dumpCommand)
    $containerDumpCreated = $true

    $databasePath = Join-Path $temporaryPath 'database.sql'
    Invoke-Compose -Arguments @('cp', ('db:' + $containerDump), $databasePath)
    if (-not (Test-Path -LiteralPath $databasePath -PathType Leaf) -or (Get-Item -LiteralPath $databasePath).Length -eq 0) {
        throw 'The database dump is empty. The transfer archive was not created.'
    }

    Copy-Item -LiteralPath $envPath -Destination (Join-Path $temporaryPath 'local.env')

    $wpConfigPath = Join-Path $projectPath 'wp-config.php'
    $wpConfigIncluded = Test-Path -LiteralPath $wpConfigPath -PathType Leaf
    if ($wpConfigIncluded) {
        Copy-Item -LiteralPath $wpConfigPath -Destination (Join-Path $temporaryPath 'wp-config.php')
    }

    $archiveUploadsPath = Join-Path $temporaryPath 'uploads'
    New-Item -ItemType Directory -Path $archiveUploadsPath | Out-Null
    Write-Host 'Copying WordPress uploads from the container...'
    Invoke-Compose -Arguments @('exec', '-T', 'wordpress', 'sh', '-lc', 'mkdir -p /var/www/html/wp-content/uploads')
    Invoke-Compose -Arguments @('cp', 'wordpress:/var/www/html/wp-content/uploads/.', $archiveUploadsPath)
    $uploadFiles = @(Get-ChildItem -LiteralPath $archiveUploadsPath -Force -Recurse -File -ErrorAction SilentlyContinue)

    $uploadBytes = 0L
    if ($uploadFiles.Count -gt 0) {
        $uploadBytes = [long](($uploadFiles | Measure-Object -Property Length -Sum).Sum)
    }

    $manifest = [ordered]@{
        format = 'wordpress-site2-transfer'
        formatVersion = 1
        createdAtUtc = (Get-Date).ToUniversalTime().ToString('o')
        sourcePlatform = 'Windows'
        projectFolderName = $projectFolderName
        composeProjectName = $composeProjectName
        siteUrl = if ($settings.ContainsKey('WP_SITE_URL')) { $settings['WP_SITE_URL'] } else { '' }
        databaseFile = 'database.sql'
        uploadsDirectory = 'uploads'
        uploadFileCount = $uploadFiles.Count
        uploadBytes = $uploadBytes
        includesEnvironment = $true
        includesWpConfig = $wpConfigIncluded
        privateArchive = $true
    }
    $manifest | ConvertTo-Json -Depth 4 | Set-Content -LiteralPath (Join-Path $temporaryPath 'manifest.json') -Encoding UTF8
    $transferFileCount = @(Get-ChildItem -LiteralPath $temporaryPath -Force -Recurse -File).Count

    Write-Host 'Packing the private cross-platform transfer archive...'
    Add-Type -AssemblyName System.IO.Compression
    Add-Type -AssemblyName System.IO.Compression.FileSystem
    $archiveStream = [System.IO.File]::Open(
        $workingArchivePath,
        [System.IO.FileMode]::CreateNew,
        [System.IO.FileAccess]::ReadWrite,
        [System.IO.FileShare]::None
    )
    try {
        $zipArchive = [System.IO.Compression.ZipArchive]::new(
            $archiveStream,
            [System.IO.Compression.ZipArchiveMode]::Create,
            $false
        )
        try {
            [void]$zipArchive.CreateEntry('uploads/')
            Get-ChildItem -LiteralPath $temporaryPath -Force -Recurse -File | ForEach-Object {
                $relativePath = $_.FullName.Substring($temporaryPath.Length).TrimStart('\', '/')
                $portableEntryName = $relativePath.Replace('\', '/')
                [void][System.IO.Compression.ZipFileExtensions]::CreateEntryFromFile(
                    $zipArchive,
                    $_.FullName,
                    $portableEntryName,
                    [System.IO.Compression.CompressionLevel]::Optimal
                )
            }
        } finally {
            $zipArchive.Dispose()
        }
    } finally {
        $archiveStream.Dispose()
    }

    $archiveHash = (Get-FileHash -LiteralPath $workingArchivePath -Algorithm SHA256).Hash
    Move-Item -LiteralPath $workingArchivePath -Destination $uploadingArchivePath
    Move-Item -LiteralPath $uploadingArchivePath -Destination $archivePath
    $checksumPath = $archivePath + '.sha256'
    $checksumLine = '{0}  {1}' -f $archiveHash.ToLowerInvariant(), $ArchiveName
    Set-Content -LiteralPath $checksumPath -Value $checksumLine -Encoding ASCII -NoNewline
    $archiveItem = Get-Item -LiteralPath $archivePath

    Write-Host ''
    Write-Host 'Transfer archive created successfully:' -ForegroundColor Green
    Write-Host $archiveItem.FullName
    Write-Host ('Size: {0:N1} MB' -f ($archiveItem.Length / 1MB))
    Write-Host "Files packed into the archive: $transferFileCount"
    Write-Host "SHA-256: $archiveHash"
    if ($usingGoogleDrive) {
        Write-Host "Google Drive folder: $OutputDirectory"
        Write-Host 'Wait until Google Drive shows that syncing is complete before importing on the other computer.'
    } else {
        Write-Host "Output folder: $OutputDirectory"
    }
    Write-Warning 'This ZIP contains the database, WordPress accounts and local secrets. Keep it private and never upload it to GitHub.'
} finally {
    if ($containerDumpCreated) {
        & docker @script:composeBaseArguments exec -T db rm -f $containerDump *> $null
    }
    if (Test-Path -LiteralPath $temporaryPath) {
        Remove-Item -LiteralPath $temporaryPath -Recurse -Force
    }
    if (Test-Path -LiteralPath $workingArchivePath) {
        Remove-Item -LiteralPath $workingArchivePath -Force
    }
    if (Test-Path -LiteralPath $uploadingArchivePath) {
        Remove-Item -LiteralPath $uploadingArchivePath -Force
    }
}






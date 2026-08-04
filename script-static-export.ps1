$ErrorActionPreference = 'Stop'

$projectPath = Split-Path -Parent $MyInvocation.MyCommand.Path
$envPath = Join-Path $projectPath '.env'
$composePath = Join-Path $projectPath 'compose.yaml'

if (-not (Test-Path -LiteralPath $envPath -PathType Leaf)) {
    throw 'The local .env file was not found. Run local-setup.sh first.'
}
if (-not (Test-Path -LiteralPath $composePath -PathType Leaf)) {
    throw 'compose.yaml was not found next to this script.'
}

$settings = @{}
foreach ($line in Get-Content -LiteralPath $envPath) {
    if ($line -match '^([^#=]+)=(.*)$') {
        $settings[$matches[1].Trim()] = $matches[2].Trim().Trim("'", '"')
    }
}

$composeProjectName = if ($settings.ContainsKey('COMPOSE_PROJECT_NAME')) { $settings['COMPOSE_PROJECT_NAME'] } else { 'wordpress_site3' }
$wordpressPort = if ($settings.ContainsKey('WORDPRESS_PORT')) { $settings['WORDPRESS_PORT'] } else { '8082' }
$compose = @('compose', '--project-name', $composeProjectName, '--env-file', $envPath, '--file', $composePath)

Write-Host 'Starting WordPress for the static export...'
& docker @compose up -d db wordpress
if ($LASTEXITCODE -ne 0) {
    throw 'Docker Compose could not start WordPress.'
}

& docker @compose exec -T wordpress php /var/www/html/script-static-export.php `
    "--source-url=http://localhost:$wordpressPort" `
    '--output=/var/www/html/index.html'
if ($LASTEXITCODE -ne 0) {
    throw 'The static page export failed.'
}

Write-Host 'GitHub Pages snapshots are ready in index.html and static-pages/.' -ForegroundColor Green

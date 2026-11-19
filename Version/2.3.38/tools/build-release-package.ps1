param(
    [Parameter(Mandatory = $true)]
    [string]$Version,
    [string]$OutputDirectory = 'release'
)

$root = Resolve-Path (Join-Path $PSScriptRoot '..')
$outputRoot = Join-Path $root $OutputDirectory
$stageRoot = Join-Path $env:TEMP ("feuer-mannschaft-release-" + $Version)
$packageRoot = Join-Path $stageRoot 'feuer-mannschaft'
$versionRoot = Join-Path $outputRoot $Version
$latestRoot = Join-Path $outputRoot 'latest'
$versionedZip = Join-Path $versionRoot 'feuer-mannschaft.zip'
$latestZip = Join-Path $latestRoot 'feuer-mannschaft.zip'

$includeItems = @(
    'assets',
    'includes',
    'templates',
    'tools',
    'ANLEITUNG.md',
    'DEPLOYMENT.md',
    'DOKUMENTATION.md',
    'README.md',
    'HISTORIE.md',
    'feuer-mannschaft.php',
    'uninstall.php',
    'update.json'
)

if (Test-Path -LiteralPath $stageRoot) {
    Remove-Item -LiteralPath $stageRoot -Recurse -Force
}

if (Test-Path -LiteralPath $outputRoot) {
    New-Item -ItemType Directory -Path $outputRoot -Force | Out-Null
} else {
    New-Item -ItemType Directory -Path $outputRoot -Force | Out-Null
}

New-Item -ItemType Directory -Path $versionRoot -Force | Out-Null
New-Item -ItemType Directory -Path $latestRoot -Force | Out-Null

New-Item -ItemType Directory -Path $packageRoot -Force | Out-Null

foreach ($item in $includeItems) {
    $source = Join-Path $root $item
    if (-not (Test-Path -LiteralPath $source)) {
        continue
    }

    Copy-Item -LiteralPath $source -Destination $packageRoot -Recurse -Force
}

if (Test-Path -LiteralPath $versionedZip) {
    Remove-Item -LiteralPath $versionedZip -Force
}

if (Test-Path -LiteralPath $latestZip) {
    Remove-Item -LiteralPath $latestZip -Force
}

Compress-Archive -Path (Join-Path $stageRoot 'feuer-mannschaft') -DestinationPath $versionedZip -Force
Copy-Item -LiteralPath $versionedZip -Destination $latestZip -Force
Remove-Item -LiteralPath $stageRoot -Recurse -Force

Write-Output "Release-ZIP erstellt: $versionedZip"
Write-Output "Aktuelles ZIP erstellt: $latestZip"

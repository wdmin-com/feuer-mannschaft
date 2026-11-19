param(
    [string]$Version = ''
)

$ErrorActionPreference = 'Stop'

$scriptDirectory = Split-Path -Parent $MyInvocation.MyCommand.Path
$pluginRoot      = Split-Path -Parent $scriptDirectory
$pluginFile      = Join-Path $pluginRoot 'feuer-mannschaft.php'

if (-not (Test-Path -LiteralPath $pluginFile)) {
    throw 'Plugin-Datei feuer-mannschaft.php wurde nicht gefunden.'
}

if ([string]::IsNullOrWhiteSpace($Version)) {
    $pluginHeader = Get-Content -LiteralPath $pluginFile -TotalCount 20
    $versionMatch = $pluginHeader | Select-String -Pattern '^\s*\*\s*Version:\s*(.+)\s*$' | Select-Object -First 1

    if (-not $versionMatch) {
        throw 'Plugin-Version konnte nicht automatisch ermittelt werden.'
    }

    $Version = $versionMatch.Matches[0].Groups[1].Value.Trim()
}

$Version = $Version.Trim()
if ($Version -notmatch '^\d+(?:\.\d+)+$') {
    throw "Ungueltige Versionsnummer: $Version"
}

$versionsRoot = Join-Path $pluginRoot 'Version'
$snapshotName = $Version
$destination  = Join-Path $versionsRoot $snapshotName

if (Test-Path -LiteralPath $destination) {
    throw "Der Versionsordner '$snapshotName' existiert bereits."
}

New-Item -ItemType Directory -Path $versionsRoot -Force | Out-Null
New-Item -ItemType Directory -Path $destination -Force | Out-Null

$itemsToCopy = Get-ChildItem -LiteralPath $pluginRoot -Force | Where-Object {
    $_.Name -notin @('.git', '.agents', '.codex', 'Version', 'versions', 'release', 'demo')
}

foreach ($item in $itemsToCopy) {
    Copy-Item -LiteralPath $item.FullName -Destination (Join-Path $destination $item.Name) -Recurse -Force
}

# Snapshots contain editable source only. Release archives are created by the
# dedicated build script and must not be recursively embedded in a snapshot.
Get-ChildItem -LiteralPath $destination -Recurse -File -Filter '*.zip' | Remove-Item -Force

Write-Output "Snapshot erstellt: $destination"

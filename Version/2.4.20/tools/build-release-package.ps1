param(
    [Parameter(Mandatory = $true)]
    [string]$Version,
    [string]$OutputDirectory = 'release'
)

Set-StrictMode -Version Latest

$root = Resolve-Path (Join-Path $PSScriptRoot '..')
$outputRoot = Join-Path $root $OutputDirectory
$versionRoot = Join-Path $outputRoot $Version
$latestRoot = Join-Path $outputRoot 'latest'
$legacyRoot = Join-Path $outputRoot 'legacy'
$versionedZip = Join-Path $versionRoot 'feuer-mannschaft.zip'
$latestZip = Join-Path $latestRoot 'feuer-mannschaft.zip'
$legacyZip = Join-Path $legacyRoot 'feuer-mannschaft.zip'
$legacyMetadataPath = Join-Path $legacyRoot 'update.json'
$metadataPath = Join-Path $root 'update.json'

$includeItems = @(
    'assets',
    'includes',
    'snippets',
    'templates',
    'tools',
    'ANLEITUNG.md',
    'DEPLOYMENT.md',
    'DOKUMENTATION.md',
    'README.md',
    'ROADMAP.md',
    'HISTORIE.md',
    'index.php',
    'feuer-mannschaft.php',
    'uninstall.php'
)

function Get-NormalizedRelativePath {
    param(
        [Parameter(Mandatory = $true)]
        [string]$BasePath,
        [Parameter(Mandatory = $true)]
        [string]$TargetPath
    )

    $baseUri = [System.Uri]((Resolve-Path -LiteralPath $BasePath).Path.TrimEnd('\') + '\')
    $targetUri = [System.Uri](Resolve-Path -LiteralPath $TargetPath).Path

    return [System.Uri]::UnescapeDataString(
        $baseUri.MakeRelativeUri($targetUri).ToString()
    )
}

function Get-ReleaseFiles {
    param(
        [Parameter(Mandatory = $true)]
        [string]$ProjectRoot,
        [Parameter(Mandatory = $true)]
        [string[]]$Items
    )

    $files = New-Object System.Collections.Generic.List[object]

    foreach ($item in $Items) {
        $source = Join-Path $ProjectRoot $item
        if (-not (Test-Path -LiteralPath $source)) {
            continue
        }

        $entry = Get-Item -LiteralPath $source
        if ($entry.PSIsContainer) {
            Get-ChildItem -LiteralPath $source -Recurse -File | Where-Object {
                $_.Extension -ne '.zip'
            } | ForEach-Object {
                $files.Add($_)
            }
            continue
        }

        if ($entry.Extension -ne '.zip') {
            $files.Add($entry)
        }
    }

    return $files
}

function New-ReleaseZip {
    param(
        [Parameter(Mandatory = $true)]
        [string]$ProjectRoot,
        [Parameter(Mandatory = $true)]
        [System.Collections.Generic.List[object]]$Files,
        [Parameter(Mandatory = $true)]
        [string]$DestinationZip
    )

    Add-Type -AssemblyName System.IO.Compression
    Add-Type -AssemblyName System.IO.Compression.FileSystem

    $zipArchive = [System.IO.Compression.ZipFile]::Open($DestinationZip, [System.IO.Compression.ZipArchiveMode]::Create)

    try {
        foreach ($file in $Files) {
            $relativePath = Get-NormalizedRelativePath -BasePath $ProjectRoot -TargetPath $file.FullName
            $entryName = ('feuer-mannschaft/' + $relativePath.Replace('\', '/')).TrimStart('/')
            $entry = $zipArchive.CreateEntry($entryName, [System.IO.Compression.CompressionLevel]::Optimal)

            $entryStream = $entry.Open()
            $fileStream = [System.IO.File]::OpenRead($file.FullName)

            try {
                $fileStream.CopyTo($entryStream)
            } finally {
                $fileStream.Dispose()
                $entryStream.Dispose()
            }
        }
    } finally {
        $zipArchive.Dispose()
    }
}

function Update-ReleaseMetadata {
    param(
        [Parameter(Mandatory = $true)]
        [string]$MetadataPath,
        [Parameter(Mandatory = $true)]
        [string]$Version,
        [string]$PackageSha256 = ''
    )

    if (-not (Test-Path -LiteralPath $MetadataPath)) {
        throw "update.json nicht gefunden: $MetadataPath"
    }

    $metadata = Get-Content -LiteralPath $MetadataPath -Raw | ConvertFrom-Json
    $metadata.version = $Version
    $metadata.download_url = "https://wdmin.com/plugins/feuer-mannschaft/release/$Version/feuer-mannschaft.zip"
    $metadata.last_updated = (Get-Date).ToString('yyyy-MM-dd HH:mm:ss')

    if (-not ($metadata.PSObject.Properties.Name -contains 'package_sha256')) {
        $metadata | Add-Member -NotePropertyName 'package_sha256' -NotePropertyValue ''
    }

    $metadata.package_sha256 = $PackageSha256

    if ($metadata.sections -and $metadata.sections.installation) {
        $metadata.sections.installation = $metadata.sections.installation `
            -replace 'release/[0-9]+\.[0-9]+\.[0-9]+/', "release/$Version/" `
            -replace 'release\\/[0-9]+\.[0-9]+\.[0-9]+\\/', "release/$Version/"
    }

    $json = $metadata | ConvertTo-Json -Depth 10
    $utf8NoBom = New-Object System.Text.UTF8Encoding($false)
    [System.IO.File]::WriteAllText($MetadataPath, $json + [Environment]::NewLine, $utf8NoBom)
}

function Test-ReleaseInputs {
    param(
        [Parameter(Mandatory = $true)]
        [string]$ProjectRoot,
        [Parameter(Mandatory = $true)]
        [string]$Version
    )

    $pluginFile = Join-Path $ProjectRoot 'feuer-mannschaft.php'
    $pluginContent = Get-Content -LiteralPath $pluginFile -Raw

    if ($pluginContent -notmatch "Version:\s*$([regex]::Escape($Version))") {
        throw "Plugin-Header-Version stimmt nicht mit $Version ueberein."
    }

    if ($pluginContent -notmatch "FEUER_MANNSCHAFT_VERSION',\s*'$([regex]::Escape($Version))'") {
        throw "FEUER_MANNSCHAFT_VERSION stimmt nicht mit $Version ueberein."
    }

    $zipFiles = @(Get-ChildItem -LiteralPath (Join-Path $ProjectRoot 'assets') -Recurse -File -Filter '*.zip')
    if ($zipFiles.Count -gt 0) {
        Write-Warning "ZIP-Dateien in assets werden nicht in das Release uebernommen."
    }
}

if (Test-Path -LiteralPath $outputRoot) {
    New-Item -ItemType Directory -Path $outputRoot -Force | Out-Null
} else {
    New-Item -ItemType Directory -Path $outputRoot -Force | Out-Null
}

New-Item -ItemType Directory -Path $versionRoot -Force | Out-Null
New-Item -ItemType Directory -Path $latestRoot -Force | Out-Null
New-Item -ItemType Directory -Path $legacyRoot -Force | Out-Null

if (Test-Path -LiteralPath $versionedZip) {
    Remove-Item -LiteralPath $versionedZip -Force
}

if (Test-Path -LiteralPath $latestZip) {
    Remove-Item -LiteralPath $latestZip -Force
}

if (Test-Path -LiteralPath $legacyZip) {
    Remove-Item -LiteralPath $legacyZip -Force
}

if (Test-Path -LiteralPath $legacyMetadataPath) {
    Remove-Item -LiteralPath $legacyMetadataPath -Force
}

Test-ReleaseInputs -ProjectRoot $root -Version $Version
Update-ReleaseMetadata -MetadataPath $metadataPath -Version $Version -PackageSha256 ''

$releaseFiles = Get-ReleaseFiles -ProjectRoot $root -Items $includeItems
New-ReleaseZip -ProjectRoot $root -Files $releaseFiles -DestinationZip $versionedZip
Copy-Item -LiteralPath $versionedZip -Destination $latestZip -Force

$packageHash = (Get-FileHash -LiteralPath $versionedZip -Algorithm SHA256).Hash.ToLowerInvariant()
Update-ReleaseMetadata -MetadataPath $metadataPath -Version $Version -PackageSha256 $packageHash
Copy-Item -LiteralPath $versionedZip -Destination $legacyZip -Force
Copy-Item -LiteralPath $metadataPath -Destination $legacyMetadataPath -Force

Write-Output "Release-ZIP erstellt: $versionedZip"
Write-Output "Aktuelles ZIP erstellt: $latestZip"
Write-Output "Legacy-ZIP erstellt: $legacyZip"
Write-Output "Legacy-update.json erstellt: $legacyMetadataPath"
Write-Output "SHA-256: $packageHash"

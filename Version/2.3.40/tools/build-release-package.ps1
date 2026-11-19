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

if (Test-Path -LiteralPath $outputRoot) {
    New-Item -ItemType Directory -Path $outputRoot -Force | Out-Null
} else {
    New-Item -ItemType Directory -Path $outputRoot -Force | Out-Null
}

New-Item -ItemType Directory -Path $versionRoot -Force | Out-Null
New-Item -ItemType Directory -Path $latestRoot -Force | Out-Null

if (Test-Path -LiteralPath $versionedZip) {
    Remove-Item -LiteralPath $versionedZip -Force
}

if (Test-Path -LiteralPath $latestZip) {
    Remove-Item -LiteralPath $latestZip -Force
}

$releaseFiles = Get-ReleaseFiles -ProjectRoot $root -Items $includeItems
New-ReleaseZip -ProjectRoot $root -Files $releaseFiles -DestinationZip $versionedZip
Copy-Item -LiteralPath $versionedZip -Destination $latestZip -Force

Write-Output "Release-ZIP erstellt: $versionedZip"
Write-Output "Aktuelles ZIP erstellt: $latestZip"

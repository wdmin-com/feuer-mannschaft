[CmdletBinding()]
param(
    [string]$OutputPath = ''
)

$ErrorActionPreference = 'Stop'

if ([string]::IsNullOrWhiteSpace($OutputPath)) {
    $OutputPath = Join-Path $PSScriptRoot '..\demo\feuer-mannschaft-demo-backup.zip'
}

$outputFullPath = [System.IO.Path]::GetFullPath($OutputPath)
$outputDirectory = Split-Path -Parent $outputFullPath

if (-not (Test-Path -LiteralPath $outputDirectory)) {
    New-Item -ItemType Directory -Path $outputDirectory -Force | Out-Null
}

$temporaryDirectory = Join-Path ([System.IO.Path]::GetTempPath()) ("feuer-mannschaft-demo-" + [guid]::NewGuid().ToString('N'))

try {
    New-Item -ItemType Directory -Path $temporaryDirectory -Force | Out-Null

    $settings = [ordered]@{
        watermark_text              = ''
        allowed_categories          = @()
        archive_display             = 'cards'
        archive_template            = 'team-grid'
        single_template             = 'standard'
        default_profile_image_id    = 0
        card_image_width            = 360
        card_image_height           = 460
        card_grayscale_strength     = 0
        excluded_title_color        = '#b22222'
        enable_cache_purge          = 1
        enable_automatic_updates    = 0
        single_image_height         = 620
        slider_autoplay             = 5000
        enable_gallery              = 1
        enable_mentions             = 1
        mention_post_types          = @('post')
    }

    $members = @(
        [ordered]@{
            old_id       = 1001
            post         = [ordered]@{
                post_author   = 1
                post_date     = '2026-01-10 09:00:00'
                post_date_gmt = '2026-01-10 08:00:00'
                post_title    = 'Anna Beispiel'
                post_name     = 'anna-beispiel'
                post_excerpt  = 'Gruppenfuehrerin der Einsatzabteilung.'
                post_content  = '<p>Dies ist ein fiktives Demo-Profil. Die Angaben koennen nach dem Import direkt im Profil-Editor angepasst werden.</p>'
                post_status   = 'publish'
            }
            meta         = [ordered]@{
                _feuer_mannschaft_first_name = 'Anna'
                _feuer_mannschaft_last_name  = 'Beispiel'
                _feuer_mannschaft_job_title  = 'Gruppenfuehrerin'
                _feuer_mannschaft_entry_date = '2016'
                _feuer_mannschaft_rank       = 'Brandmeister'
                _feuer_mannschaft_function   = @('Wehrfuehrer-Vertreter')
                _feuer_mannschaft_education  = @('Gruppenfuehrer', 'Atemschutzgeraetetraeger')
                _feuer_mannschaft_sort_order = 10
                _feuer_mannschaft_description = '<p>Fiktives Demo-Profil fuer die Vorschau von Mannschaftskarten und Einzelprofilen.</p>'
            }
            category_ids = @()
            categories   = @()
            gallery      = @(
                [ordered]@{
                    old_attachment_id = 2001
                    file              = 'anna-beispiel.png'
                    title             = 'Anna Beispiel'
                    caption           = ''
                    description       = ''
                    alt               = 'Fiktives Portrait von Anna Beispiel in Feuerwehrschutzkleidung'
                    mime_type         = 'image/png'
                }
            )
        },
        [ordered]@{
            old_id       = 1002
            post         = [ordered]@{
                post_author   = 1
                post_date     = '2026-01-11 09:00:00'
                post_date_gmt = '2026-01-11 08:00:00'
                post_title    = 'Leon Muster'
                post_name     = 'leon-muster'
                post_excerpt  = 'Atemschutzgeraetetraeger.'
                post_content  = '<p>Dies ist ein fiktives Demo-Profil ohne personenbezogene Kontaktdaten.</p>'
                post_status   = 'publish'
            }
            meta         = [ordered]@{
                _feuer_mannschaft_first_name = 'Leon'
                _feuer_mannschaft_last_name  = 'Muster'
                _feuer_mannschaft_job_title  = 'Atemschutzgeraetetraeger'
                _feuer_mannschaft_entry_date = '2020'
                _feuer_mannschaft_rank       = 'Oberfeuerwehrmann'
                _feuer_mannschaft_education  = @('Atemschutzgeraetetraeger', 'Maschinist')
                _feuer_mannschaft_sort_order = 20
                _feuer_mannschaft_description = '<p>Fiktives Demo-Profil fuer die Kartenansicht.</p>'
            }
            category_ids = @()
            categories   = @()
            gallery      = @(
                [ordered]@{
                    old_attachment_id = 2002
                    file              = 'leon-muster.png'
                    title             = 'Leon Muster'
                    caption           = ''
                    description       = ''
                    alt               = 'Fiktives Portrait von Leon Muster in Feuerwehrschutzkleidung'
                    mime_type         = 'image/png'
                }
            )
        },
        [ordered]@{
            old_id       = 1003
            post         = [ordered]@{
                post_author   = 1
                post_date     = '2026-01-12 09:00:00'
                post_date_gmt = '2026-01-12 08:00:00'
                post_title    = 'Sofia Demo'
                post_name     = 'sofia-demo'
                post_excerpt  = 'Betreuung der Jugendfeuerwehr.'
                post_content  = '<p>Dieses Profil dient ausschliesslich als Beispieldatensatz fuer die Demo-Installation.</p>'
                post_status   = 'publish'
            }
            meta         = [ordered]@{
                _feuer_mannschaft_first_name = 'Sofia'
                _feuer_mannschaft_last_name  = 'Demo'
                _feuer_mannschaft_job_title  = 'Jugendfeuerwehr-Betreuung'
                _feuer_mannschaft_entry_date = '2022'
                _feuer_mannschaft_rank       = 'Feuerwehrfrau'
                _feuer_mannschaft_function   = @('Jugendfeuerwehrwart')
                _feuer_mannschaft_education  = @('Truppfuehrer')
                _feuer_mannschaft_sort_order = 30
                _feuer_mannschaft_description = '<p>Fiktives Demo-Profil, das die Funktion und Ausbildung im Einzelprofil zeigt.</p>'
            }
            category_ids = @()
            categories   = @()
            gallery      = @(
                [ordered]@{
                    old_attachment_id = 2003
                    file              = 'sofia-demo.png'
                    title             = 'Sofia Demo'
                    caption           = ''
                    description       = ''
                    alt               = 'Fiktives Portrait von Sofia Demo in Feuerwehrschutzkleidung'
                    mime_type         = 'image/png'
                }
            )
        },
        [ordered]@{
            old_id       = 1004
            post         = [ordered]@{
                post_author   = 1
                post_date     = '2026-01-13 09:00:00'
                post_date_gmt = '2026-01-13 08:00:00'
                post_title    = 'Erik Platzhalter'
                post_name     = 'erik-platzhalter'
                post_excerpt  = 'Maschinist der Einsatzabteilung.'
                post_content  = '<p>Fiktiver Beispieldatensatz. Er kann nach dem Import geloescht oder durch ein echtes Profil ersetzt werden.</p>'
                post_status   = 'publish'
            }
            meta         = [ordered]@{
                _feuer_mannschaft_first_name = 'Erik'
                _feuer_mannschaft_last_name  = 'Platzhalter'
                _feuer_mannschaft_job_title  = 'Maschinist'
                _feuer_mannschaft_entry_date = '2018'
                _feuer_mannschaft_rank       = 'Feuerwehrmann'
                _feuer_mannschaft_education  = @('Maschinist')
                _feuer_mannschaft_sort_order = 40
                _feuer_mannschaft_description = '<p>Fiktives Demo-Profil fuer die Darstellung weiterer Karten.</p>'
            }
            category_ids = @()
            categories   = @()
            gallery      = @(
                [ordered]@{
                    old_attachment_id = 2004
                    file              = 'erik-platzhalter.png'
                    title             = 'Erik Platzhalter'
                    caption           = ''
                    description       = ''
                    alt               = 'Fiktives Portrait von Erik Platzhalter in Feuerwehrschutzkleidung'
                    mime_type         = 'image/png'
                }
            )
        }
    )

    $additionalProfiles = @(
        'Mara Fischer', 'David Klein', 'Nina Hoffmann', 'Jonas Berger', 'Lena Wagner', 'Tobias Schmitt', 'Amira Braun',
        'Felix Richter', 'Carla Neumann', 'Omar Wolf', 'Sarah Becker', 'Martin Krueger', 'Elena Vogt', 'Noah Werner',
        'Julia Franke', 'Daniel Sommer', 'Paula Brandt', 'Milan Krause', 'Laura Seidel', 'Kevin Winter', 'Yasmin Lorenz'
    )

    for ($index = 0; $index -lt $additionalProfiles.Count; $index++) {
        $memberNumber = $index + 5
        $nameParts    = $additionalProfiles[$index] -split ' ', 2
        $firstName    = $nameParts[0]
        $lastName     = $nameParts[1]
        $fullName     = $additionalProfiles[$index]
        $imageFile    = 'team-member-{0:D2}.png' -f $memberNumber

        $members += [ordered]@{
            old_id       = 1000 + $memberNumber
            post         = [ordered]@{
                post_author   = 1
                post_date     = '2026-02-01 09:00:00'
                post_date_gmt = '2026-02-01 08:00:00'
                post_title    = $fullName
                post_name     = 'demo-member-{0:D2}' -f $memberNumber
                post_excerpt  = 'Fiktives Mitglied der Demo-Mannschaft.'
                post_content  = '<p>Fiktives Demo-Profil fuer die Vorschau der Mannschaftskarten.</p>'
                post_status   = 'publish'
            }
            meta         = [ordered]@{
                _feuer_mannschaft_first_name = $firstName
                _feuer_mannschaft_last_name  = $lastName
                _feuer_mannschaft_job_title  = 'Einsatzabteilung'
                _feuer_mannschaft_entry_date = (2015 + ($index % 10)).ToString()
                _feuer_mannschaft_rank       = if ($index % 3 -eq 0) { 'Feuerwehrfrau' } elseif ($index % 3 -eq 1) { 'Feuerwehrmann' } else { 'Oberfeuerwehrmann' }
                _feuer_mannschaft_education  = @('Truppfuehrer')
                _feuer_mannschaft_sort_order = $memberNumber * 10
                _feuer_mannschaft_description = '<p>Fiktives Profil mit einem KI-generierten Demo-Portrait.</p>'
            }
            category_ids = @()
            categories   = @()
            gallery      = @(
                [ordered]@{
                    old_attachment_id = 2000 + $memberNumber
                    file              = $imageFile
                    title             = $fullName
                    caption           = ''
                    description       = ''
                    alt               = 'Fiktives Feuerwehr-Portrait fuer die Demo-Mannschaft'
                    mime_type         = 'image/png'
                }
            )
        }
    }

    $manifest = [ordered]@{
        generated_at = (Get-Date).ToUniversalTime().ToString('yyyy-MM-ddTHH:mm:ssZ')
        version      = '2.3.66'
        site_url     = 'https://example.invalid/'
        settings     = $settings
        members      = $members
        mentions     = @()
        logs         = @()
    }

    $filesDirectory     = Join-Path $temporaryDirectory 'files'
    $demoImageDirectory = Join-Path $PSScriptRoot '..\demo\images'
    $demoImageFiles     = @(Get-ChildItem -LiteralPath $demoImageDirectory -Filter '*.png' -File | Sort-Object Name | Select-Object -ExpandProperty Name)

    New-Item -ItemType Directory -Path $filesDirectory -Force | Out-Null

    foreach ($demoImageFile in $demoImageFiles) {
        $sourceImagePath = Join-Path $demoImageDirectory $demoImageFile

        if (-not (Test-Path -LiteralPath $sourceImagePath -PathType Leaf)) {
            throw "Demo image is missing: $sourceImagePath"
        }

        Copy-Item -LiteralPath $sourceImagePath -Destination (Join-Path $filesDirectory $demoImageFile)
    }

    $manifestPath = Join-Path $temporaryDirectory 'manifest.json'
    $manifestJson = $manifest | ConvertTo-Json -Depth 12
    [System.IO.File]::WriteAllText(
        $manifestPath,
        $manifestJson,
        [System.Text.UTF8Encoding]::new($false)
    )

    if (Test-Path -LiteralPath $outputFullPath) {
        Remove-Item -LiteralPath $outputFullPath -Force
    }

    Add-Type -AssemblyName System.IO.Compression
    Add-Type -AssemblyName System.IO.Compression.FileSystem
    $archive = [System.IO.Compression.ZipFile]::Open(
        $outputFullPath,
        [System.IO.Compression.ZipArchiveMode]::Create
    )

    try {
        [System.IO.Compression.ZipFileExtensions]::CreateEntryFromFile(
            $archive,
            $manifestPath,
            'manifest.json',
            [System.IO.Compression.CompressionLevel]::Optimal
        ) | Out-Null

        foreach ($demoImageFile in $demoImageFiles) {
            [System.IO.Compression.ZipFileExtensions]::CreateEntryFromFile(
                $archive,
                (Join-Path $filesDirectory $demoImageFile),
                ('files/' + $demoImageFile),
                [System.IO.Compression.CompressionLevel]::Optimal
            ) | Out-Null
        }
    }
    finally {
        $archive.Dispose()
    }

    Write-Host "Demo backup created: $outputFullPath"
}
finally {
    if (Test-Path -LiteralPath $temporaryDirectory) {
        Remove-Item -LiteralPath $temporaryDirectory -Recurse -Force
    }
}

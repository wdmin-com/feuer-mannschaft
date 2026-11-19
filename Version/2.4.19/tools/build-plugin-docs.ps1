param(
    [string]$OutputPath = 'DOKUMENTATION.md'
)

$root = Resolve-Path (Join-Path $PSScriptRoot '..')
$outputFile = Join-Path $root $OutputPath

function Get-RelativeProjectPath {
    param(
        [string]$BasePath,
        [string]$FullPath
    )

    $relative = $FullPath.Substring($BasePath.Length).TrimStart('\')
    return $relative.Replace('\', '/')
}

function Format-MarkdownCell {
    param(
        [string]$Value
    )

    return (($Value -replace '\|', '\|') -replace '\r?\n', '<br>')
}

function Convert-FunctionNameToDescription {
    param(
        [string]$Name
    )

    switch ($Name) {
        'instance' { return 'Liefert die Singleton-Instanz der Klasse.' }
        'activate' { return 'Fuehrt Aktivierungsroutinen des Plugins aus.' }
        'deactivate' { return 'Fuehrt Deaktivierungsroutinen des Plugins aus.' }
        '__construct' { return 'Initialisiert Abhaengigkeiten und registriert Hooks.' }
    }

    $parts = $Name -split '_'
    if ($parts.Count -eq 0) {
        return 'Interne Hilfsfunktion des Plugins.'
    }

    $prefix = $parts[0]
    $subject = (($parts | Select-Object -Skip 1) -join ' ').Trim()
    if ([string]::IsNullOrWhiteSpace($subject)) {
        $subject = $Name
    }

    switch ($prefix) {
        'add' { return "Fuegt $subject hinzu." }
        'apply' { return "Wendet $subject an." }
        'archive' { return "Archiviert $subject." }
        'build' { return "Erzeugt $subject." }
        'create' { return "Erstellt $subject." }
        'deactivate' { return "Deaktiviert $subject." }
        'delete' { return "Entfernt $subject." }
        'disable' { return "Deaktiviert $subject." }
        'display' { return "Zeigt $subject an." }
        'enable' { return "Aktiviert $subject." }
        'ensure' { return "Stellt sicher, dass $subject vorhanden ist." }
        'enqueue' { return "Laedt Assets oder Ressourcen fuer $subject." }
        'export' { return "Exportiert $subject." }
        'filter' { return "Filtert oder passt $subject an." }
        'format' { return "Formatiert $subject." }
        'get' { return "Liest oder ermittelt $subject." }
        'handle' { return "Verarbeitet $subject." }
        'has' { return "Prueft, ob $subject vorhanden ist." }
        'import' { return "Importiert $subject." }
        'is' { return "Prueft, ob $subject zutrifft." }
        'load' { return "Laedt $subject." }
        'log' { return "Protokolliert $subject." }
        'map' { return "Ordnet $subject zu." }
        'maybe' { return "Fuehrt $subject nur bei Bedarf aus." }
        'migrate' { return "Migriert $subject." }
        'normalize' { return "Normalisiert $subject." }
        'persist' { return "Schreibt $subject dauerhaft in den Speicher." }
        'prepare' { return "Bereitet $subject vor." }
        'purge' { return "Leert oder bereinigt $subject." }
        'register' { return "Registriert $subject." }
        'render' { return "Rendert $subject." }
        'replace' { return "Ersetzt $subject." }
        'resolve' { return "Loest $subject auf." }
        'restore' { return "Stellt $subject wieder her." }
        'sanitize' { return "Bereinigt und validiert $subject." }
        'save' { return "Speichert $subject." }
        'shortcode' { return "Rendert den Shortcode fuer $subject." }
        'sync' { return "Synchronisiert $subject." }
        'tune' { return "Optimiert $subject." }
        'unarchive' { return "Entarchiviert $subject." }
        'update' { return "Aktualisiert $subject." }
        default { return "Interne Funktion fuer $subject." }
    }
}

function Get-FileDescription {
    param(
        [string]$RelativePath
    )

    switch -Regex ($RelativePath) {
        '^feuer-mannschaft\.php$' { return 'Plugin-Bootstrap mit Konstanten, Aktivierungs-/Deaktivierungslogik und Laden der Kernklassen.' }
        '^uninstall\.php$' { return 'Entfernt Plugin-Daten und bereinigt Inhalte waehrend der Deinstallation.' }
        '^ANLEITUNG\.md$' { return 'Anwenderdokumentation fuer Installation, Bedienung und Einsatz im WordPress-Backend.' }
        '^HISTORIE\.md$' { return 'Chronologische Aenderungshistorie aller Plugin-Versionen.' }
        '^DOKUMENTATION\.md$' { return 'Automatisch erzeugte Dokumentation mit Dateikarte und Funktionsindex.' }
        '^DEPLOYMENT\.md$' { return 'Anleitung fuer Release-Builds, Update-Bereitstellung und Serverablage.' }
        '^README\.md$' { return 'GitHub-Startdokument mit Projektueberblick, Entwicklungshinweisen und Release-Ablauf.' }
        '^update\.json$' { return 'Remote-Manifest fuer die benutzerdefinierte WordPress-Updatepruefung.' }
        '^\.gitignore$' { return 'Git-Regeln fuer lokale und generierte Dateien, die nicht versioniert werden sollen.' }
        '^\.gitattributes$' { return 'Git-Attribute fuer Zeilenenden und Export-Verhalten bei GitHub-Archiven.' }
        '^assets/css/admin\.css$' { return 'Styles fuer die Plugin-Administration, Einstellungen und Metaboxen.' }
        '^assets/css/feuer-mannschaft\.css$' { return 'Styles fuer Archiv-, Karten- und Profilansichten im Frontend.' }
        '^assets/js/admin\.js$' { return 'Admin-JavaScript fuer wiederholbare Felder, Uploads und Einstellungsoberflaechen.' }
        '^assets/js/feuer-mannschaft\.js$' { return 'Frontend-JavaScript fuer Slider, Modalansichten und Interaktionen.' }
        '^includes/class-feuer-mannschaft-plugin\.php$' { return 'Zentrale Plugin-Klasse fuer Backend, Frontend, Settings, Templates und Mitgliederdaten.' }
        '^includes/class-feuer-mannschaft-backup-manager\.php$' { return 'Sichert und stellt Plugin-Daten, Medien und Einstellungen aus Archiven wieder her.' }
        '^includes/class-feuer-mannschaft-logger\.php$' { return 'Kapselt Log-Tabelle, Schema-Updates und Protokollschreibvorgaenge.' }
        '^includes/class-feuer-mannschaft-mentions\.php$' { return 'Verwaltet Erwahnungen, Verknuepfungen und Frontend-Ausgabe dazu.' }
        '^includes/class-feuer-mannschaft-updater\.php$' { return 'Prueft remote auf neue Versionen und bindet Custom-Updates in WordPress ein.' }
        '^templates/archive-feuer-mannschaft-member\.php$' { return 'WordPress-Archiv-Wrapper fuer Mannschaftslisten.' }
        '^templates/page-feuer-mannschaft-profile\.php$' { return 'Spezielle Seitenvorlage fuer Profilseiten oder Shortcode-Ausgaben.' }
        '^templates/single-feuer-mannschaft-member\.php$' { return 'WordPress-Single-Wrapper fuer Einzelprofile.' }
        '^templates/admin/settings/.*\.php$' { return 'Teilvorlage fuer einzelne Bereiche der Einstellungsseite.' }
        '^templates/admin/.*\.php$' { return 'Admin-Teilvorlage fuer Metaboxen, Hinweise und Einstellungen.' }
        '^templates/frontend/.*\.php$' { return 'Wiederverwendbare Frontend-Teilvorlage fuer Detailwerte, Karten oder Platzhalter.' }
        '^templates/profil/.*\.php$' { return 'Frontend-Template fuer die Einzelansicht eines Mitglieds.' }
        '^templates/uebersicht/.*\.php$' { return 'Frontend-Template fuer Listen-, Karten- und Rasteransichten der Mannschaft.' }
        '^tools/create-version-snapshot\.ps1$' { return 'Erzeugt einen Versions-Snapshot unter Version/<version>/ fuer den aktuellen Stand.' }
        '^tools/build-plugin-docs\.ps1$' { return 'Erzeugt die Funktions- und Dateidokumentation automatisch aus dem Projektstand.' }
        '^tools/build-release-package\.ps1$' { return 'Erstellt ein auslieferbares ZIP-Paket fuer Server oder GitHub-Releases.' }
        '^Version/.*$' { return 'Versions-Snapshot eines frueheren Plugin-Stands.' }
        '^feuer-mannschaft/README\.md$' { return 'Zusatz-README in einem Unterordner, aktuell ohne inhaltliche Dokumentation.' }
        default { return 'Projektdatei des Plugins.' }
    }
}

function Get-FunctionIndex {
    param(
        [string]$RootPath
    )

    $phpFiles = Get-ChildItem -Path $RootPath -File -Recurse -Filter '*.php' |
        Where-Object { $_.FullName -notmatch '\\(Version|release|\.git)\\' } |
        Sort-Object FullName

    $index = @()

    foreach ($file in $phpFiles) {
        $relativePath = Get-RelativeProjectPath -BasePath $RootPath -FullPath $file.FullName
        $lines = Get-Content -Path $file.FullName
        $functions = @()

        for ($i = 0; $i -lt $lines.Count; $i++) {
            $line = $lines[$i]
            if ($line -match '^\s*(public|protected|private)?\s*(static\s+)?function\s+([A-Za-z_][A-Za-z0-9_]*)\s*\(') {
                $visibility = if ([string]::IsNullOrWhiteSpace($matches[1])) { 'global' } else { $matches[1] }
                $static = if ([string]::IsNullOrWhiteSpace($matches[2])) { '' } else { ' static' }

                $functions += [PSCustomObject]@{
                    Line        = $i + 1
                    Visibility  = ($visibility + $static).Trim()
                    Name        = $matches[3]
                    Description = Convert-FunctionNameToDescription -Name $matches[3]
                }
            }
        }

        $index += [PSCustomObject]@{
            Path        = $relativePath
            Description = Get-FileDescription -RelativePath $relativePath
            Functions   = $functions
        }
    }

    return $index
}

$projectFiles = Get-ChildItem -Path $root -File -Recurse |
    Where-Object { $_.FullName -notmatch '\\(Version|release|\.git)\\' } |
    Sort-Object FullName

$functionIndex = Get-FunctionIndex -RootPath $root
$generatedAt = Get-Date -Format 'yyyy-MM-dd HH:mm:ss'

$content = [System.Collections.Generic.List[string]]::new()

$content.Add('# Feuer-Mannschaft Dokumentation')
$content.Add('')
$content.Add("Automatisch erzeugt am $generatedAt.")
$content.Add('')
$content.Add('## Zweck')
$content.Add('')
$content.Add('Diese Datei dokumentiert die aktuelle Projektstruktur, beschreibt die wichtigsten Dateien und listet alle im Quellcode gefundenen PHP-Funktionen mit Dateipfad, Zeile und Kurzbeschreibung auf.')
$content.Add('')
$content.Add('## Dateikarte')
$content.Add('')
$content.Add('| Pfad | Beschreibung |')
$content.Add('| --- | --- |')

foreach ($file in $projectFiles) {
    $relativePath = Get-RelativeProjectPath -BasePath $root -FullPath $file.FullName
    $content.Add("| ``$relativePath`` | $(Format-MarkdownCell (Get-FileDescription -RelativePath $relativePath)) |")
}

$content.Add('')
$content.Add('## PHP-Funktionsindex')
$content.Add('')
$content.Add('Die folgenden Tabellen werden direkt aus den PHP-Dateien erzeugt. Dadurch bleiben Dateipfade und Zeilennummern fuer Wartung, Reviews und GitHub-Navigation nachvollziehbar.')

foreach ($fileEntry in $functionIndex) {
    $content.Add('')
    $content.Add("### ``$($fileEntry.Path)``")
    $content.Add('')
    $content.Add($fileEntry.Description)
    $content.Add('')

    if ($fileEntry.Functions.Count -eq 0) {
        $content.Add('Keine benannten PHP-Funktionen in dieser Datei gefunden.')
        continue
    }

    $content.Add('| Zeile | Sichtbarkeit | Funktion | Kurzbeschreibung |')
    $content.Add('| --- | --- | --- | --- |')

    foreach ($function in $fileEntry.Functions) {
        $content.Add("| $($function.Line) | $(Format-MarkdownCell $function.Visibility) | ``$($function.Name)`` | $(Format-MarkdownCell $function.Description) |")
    }
}

[System.IO.File]::WriteAllLines($outputFile, $content)
Write-Output "Dokumentation erstellt: $outputFile"

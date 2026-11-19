<?php
declare(strict_types=1);

header('Content-Type: text/html; charset=UTF-8');

function fm_index_read_text(string $file): string
{
    if (! is_file($file) || ! is_readable($file)) {
        return '';
    }

    $content = file_get_contents($file);

    return is_string($content) ? $content : '';
}

function fm_index_escape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function fm_index_plugin_field(string $contents, string $field): string
{
    $pattern = '/^[ \t\/*#@]*' . preg_quote($field, '/') . ':\s*(.+)$/mi';

    if (preg_match($pattern, $contents, $matches)) {
        return trim((string) $matches[1]);
    }

    return '';
}

function fm_index_load_update_metadata(string $file): array
{
    $raw = fm_index_read_text($file);
    if ($raw === '') {
        return array();
    }

    $data = json_decode($raw, true);

    return is_array($data) ? $data : array();
}

function fm_index_collect_templates(string $directory): array
{
    if (! is_dir($directory)) {
        return array();
    }

    $templates = array();

    foreach (glob($directory . DIRECTORY_SEPARATOR . '*.php') ?: array() as $file) {
        $contents = fm_index_read_text($file);
        if ($contents === '') {
            continue;
        }

        $name = fm_index_plugin_field($contents, 'Template Name');
        if ($name === '') {
            continue;
        }

        $description = fm_index_plugin_field($contents, 'Beschreibung');
        if ($description === '') {
            $description = fm_index_plugin_field($contents, 'Description');
        }

        $templates[] = array(
            'name' => $name,
            'description' => $description,
        );
    }

    usort(
        $templates,
        static function (array $left, array $right): int {
            return strnatcasecmp($left['name'], $right['name']);
        }
    );

    return $templates;
}

function fm_index_parse_history(string $file): array
{
    $contents = fm_index_read_text($file);
    if ($contents === '') {
        return array();
    }

    $entries = array();
    $current = null;

    foreach (preg_split("/\r\n|\n|\r/", $contents) as $line) {
        $trimmed = trim((string) $line);

        if (preg_match('/^##\s+(.+)$/', $trimmed, $matches)) {
            if (is_array($current)) {
                $entries[] = $current;
            }

            $current = array(
                'title' => trim((string) $matches[1]),
                'summary' => array(),
                'changes' => array(),
            );
            continue;
        }

        if (! is_array($current) || $trimmed === '' || 0 === stripos($trimmed, 'Geaendert:')) {
            continue;
        }

        if (preg_match('/^-\s+(.+)$/', $trimmed, $matches)) {
            $current['changes'][] = trim((string) $matches[1]);
            continue;
        }

        $current['summary'][] = $trimmed;
    }

    if (is_array($current)) {
        $entries[] = $current;
    }

    return $entries;
}

function fm_index_char_count(string $content): int
{
    return function_exists('mb_strlen') ? mb_strlen($content, 'UTF-8') : strlen($content);
}

function fm_index_collect_code_metrics(string $root): array
{
    $metrics = array(
        'files' => 0,
        'lines' => 0,
        'characters' => 0,
    );

    if (! is_dir($root)) {
        return $metrics;
    }

    $extensions = array('php', 'css', 'js', 'json', 'md', 'ps1');
    $skipDirs = array('Version', 'release', '.git', 'node_modules', 'vendor');

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)
    );

    foreach ($iterator as $file) {
        if (! $file instanceof SplFileInfo || ! $file->isFile()) {
            continue;
        }

        $path = str_replace('\\', '/', $file->getPathname());
        $skip = false;

        foreach ($skipDirs as $dir) {
            if (false !== strpos($path, '/' . $dir . '/')) {
                $skip = true;
                break;
            }
        }

        if ($skip) {
            continue;
        }

        $extension = strtolower((string) $file->getExtension());
        if (! in_array($extension, $extensions, true)) {
            continue;
        }

        $content = fm_index_read_text($file->getPathname());

        $metrics['files']++;
        $metrics['characters'] += fm_index_char_count($content);
        $metrics['lines'] += $content === '' ? 0 : substr_count($content, "\n") + 1;
    }

    return $metrics;
}

function fm_index_format_number(int $value): string
{
    return number_format($value, 0, ',', '.');
}

$root = __DIR__;
$pluginContents = fm_index_read_text($root . DIRECTORY_SEPARATOR . 'feuer-mannschaft.php');
$updateMetadata = fm_index_load_update_metadata($root . DIRECTORY_SEPARATOR . 'update.json');
$historyEntries = fm_index_parse_history($root . DIRECTORY_SEPARATOR . 'HISTORIE.md');
$archiveViews = fm_index_collect_templates($root . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'uebersicht');
$profileViews = fm_index_collect_templates($root . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'profil');
$codeMetrics = fm_index_collect_code_metrics($root);
$indexContent = fm_index_read_text(__FILE__);
$indexMetrics = array(
    'lines' => $indexContent === '' ? 0 : substr_count($indexContent, "\n") + 1,
    'characters' => fm_index_char_count($indexContent),
);

$pluginName = fm_index_plugin_field($pluginContents, 'Plugin Name');
$pluginUri = fm_index_plugin_field($pluginContents, 'Plugin URI');
$pluginVersion = fm_index_plugin_field($pluginContents, 'Version');
$pluginAuthor = fm_index_plugin_field($pluginContents, 'Author');
$pluginAuthorUri = fm_index_plugin_field($pluginContents, 'Author URI');
$pluginRequires = fm_index_plugin_field($pluginContents, 'Requires PHP');
$pluginText = fm_index_plugin_field($pluginContents, 'Text Domain');
$pluginSummary = fm_index_plugin_field($pluginContents, 'Description');

$latestVersion = isset($updateMetadata['version']) ? (string) $updateMetadata['version'] : $pluginVersion;
$downloadUrl = isset($updateMetadata['download_url']) ? (string) $updateMetadata['download_url'] : '';
$lastUpdated = isset($updateMetadata['last_updated']) ? (string) $updateMetadata['last_updated'] : '';
$updateUrl = $pluginUri !== '' ? rtrim($pluginUri, '/') . '/update.json' : 'update.json';

$advantages = array(
    'Entwickelt fuer Freiwillige Feuerwehr und Mannschaftsseiten, nicht fuer generische Teamseiten.',
    'Nur die Daten, die fuer eine oeffentliche Feuerwehr-Mannschaft wirklich gebraucht werden.',
    'Profile, Fotos, Dienstgrade, Funktionen und Ausbildungen werden direkt im WordPress-Backend gepflegt.',
    'Datenschutzfreundliche Ausgabe: keine externen Cloud-Dienste, keine fremden Profilplattformen, keine unnoetigen Tracking-Funktionen.',
    'Eigener Update-Kanal ueber wdmin.com mit klarer Versionshistorie und kontrollierten Release-Paketen.',
    'Mehrere Frontend-Designs ohne Abhaengigkeit von einem bestimmten Theme.',
);

$featureSections = array(
    'Mitgliederverwaltung' => array(
        'Mitglieder als WordPress-Beitraege mit eigenem Feuer-Mannschaft-Kontext.',
        'Aktive und archivierte Profile.',
        'Sortierposition fuer die Reihenfolge in Listen.',
        'Kategorieauswahl aus freigegebenen WordPress-Kategorien.',
        'Eigene Profil-Links fuer Sonderfaelle.',
        'Ausschluss einzelner Profile aus dem Gesamtzaehler.',
    ),
    'Fotos und Medien' => array(
        'Mehrere Fotos pro Profil.',
        'Galerie-Slider in Profilansichten.',
        'Standard-Profilfoto fuer Mitglieder ohne eigenes Foto.',
        'Watermark-Text fuer Bilder.',
        'Konfigurierbare Bildhoehen und Kartenformate.',
        'Graustufen-Teamkarten mit Prozentwert und Hover-Effekt zur Farbdarstellung.',
    ),
    'Listenfelder' => array(
        'Dienstgrad als zentrale Liste.',
        'Funktion als zentrale Liste.',
        'Ausbildung als zentrale Liste.',
        'Zusaetzliche eigene Listenfelder.',
        'Einzelauswahl oder Mehrfachauswahl.',
        'Optionen koennen Text und Linkziel enthalten.',
    ),
    'Frontend-Ausgabe' => array(
        'Shortcode fuer komplette Mannschaftslisten.',
        'Shortcode fuer einzelne Profile.',
        'Shortcode fuer Kartenbloecke.',
        'Shortcode fuer Mitgliederzaehler.',
        'Shortcode fuer verlinkte Mitgliedsnamen.',
        'Mehrere Listen- und Profilvorlagen.',
    ),
    'Administration und Sicherheit' => array(
        'Berechtigungen orientieren sich an WordPress-Rollen.',
        'Sichere Nonce-Pruefungen fuer Admin-Aktionen.',
        'Geschuetzte Archiv- und Upload-Ordner.',
        'Kontrolliertes Loeschen von Medien nur aus der eigenen Galerie.',
        'Backup- und Restore-Mechanismus fuer Plugin-Daten.',
        'Logbereich fuer wichtige Aenderungen.',
    ),
    'Updates und Betrieb' => array(
        'Eigener Update-Check gegen update.json.',
        'Versionierte Release-ZIP-Dateien.',
        'Manuelle Update-Pruefung in den Plugin-Einstellungen.',
        'Technische Dokumentation im Projekt.',
        'Release-Historie direkt aus HISTORIE.md.',
    ),
);

$settingsSections = array(
    'Allgemein' => array(
        'Watermark-Text: Text, der in Bildern verwendet werden kann.',
        'Standard-Profilfoto: Bild fuer Profile ohne eigenes Foto.',
        'Freigegebene Kategorien: Kategorien, die fuer Mitgliederprofile erlaubt sind.',
        'Standardvorlage fuer Listen: Layout fuer die Mannschaftsuebersicht.',
        'Standardvorlage fuer Profile: Layout fuer Einzelprofile.',
        'Kartenbild Breite: Basisbreite fuer Kartenbilder.',
        'Kartenbild Hoehe: Basishoehe fuer Kartenbilder.',
        'Graustufen fuer Karten: Prozentwert fuer schwarz-weisse Fotos in passenden Vorlagen.',
        'Farbe fuer ausgeschlossene Profilnamen: Markierung von Profilen, die nicht in den Gesamtzaehler gehoeren.',
        'Profilbild Hoehe: Hoehe der grossen Profilbilder.',
        'Slider Autoplay: Intervall fuer automatische Galeriewechsel.',
        'Galerie aktivieren: Aktiviert oder deaktiviert Frontend-Slider.',
    ),
    'Erwaehnungen' => array(
        'Erwaehnungen aktivieren oder deaktivieren.',
        'Beitragstypen waehlen, in denen Mitglieder erwaehnt werden duerfen.',
        'Mitglieder als Textlink in Beitragen ausgeben.',
    ),
    'Listenfelder' => array(
        'Dienstgrad verwalten.',
        'Funktion verwalten.',
        'Ausbildung verwalten.',
        'Eigene Feldgruppen anlegen.',
        'Beschreibung pro Feldgruppe erfassen.',
        'Optionen mit Label und optionaler URL pflegen.',
    ),
    'Archive' => array(
        'Archiv erstellen.',
        'Archiv hochladen.',
        'Archiv herunterladen.',
        'Archiv wiederherstellen.',
        'Archiv loeschen.',
    ),
    'Updates' => array(
        'Installierte Version anzeigen.',
        'Remote-Version vom Update-Server anzeigen.',
        'Download-Paket anzeigen.',
        'Update-Pruefung manuell starten.',
    ),
    'Logs' => array(
        'Einstellungs-Aenderungen ansehen.',
        'Mitglieder-Aenderungen ansehen.',
        'Archivaktionen nachvollziehen.',
    ),
);

$profileFields = array(
    'Vorname' => 'Oeffentlicher Vorname des Mitglieds.',
    'Nachname' => 'Oeffentlicher Nachname des Mitglieds.',
    'Eintrittsjahr' => 'Jahr des Eintritts oder der Zugehoerigkeit.',
    'Dienstgrad' => 'Auswahl aus der zentralen Dienstgrad-Liste.',
    'Funktion' => 'Eine oder mehrere Funktionen, zum Beispiel Wehrfuehrer oder Jugendfeuerwehrwart.',
    'Ausbildung' => 'Eine oder mehrere Ausbildungen, zum Beispiel Maschinist oder Atemschutzgeraetetraeger.',
    'Zusatzfelder' => 'Eigene Felder fuer weitere Informationen der jeweiligen Feuerwehr.',
    'Kategorie' => 'Zuordnung zu einer freigegebenen WordPress-Kategorie.',
    'Eigener Link' => 'Optionaler Link, der statt der Standard-Profilseite verwendet wird.',
    'Im Gesamtzaehler nicht mitzaehlen' => 'Markiert Profile, die angezeigt werden, aber nicht in den Gesamtzaehler einfliessen.',
    'Beschreibung' => 'Freier Profiltext fuer oeffentliche Informationen.',
    'Fotos' => 'Galerie mit einem oder mehreren Bildern pro Mitglied.',
);

$shortcodes = array(
    '[feuer_mannschaft]' => 'Komplette Mannschaftsliste mit Standardvorlage.',
    '[feuer_mannschaft template="team-grayscale"]' => 'Team Raster mit schwarz-weissen Fotos und Hover-Farbeffekt.',
    '[feuer_mannschaft_profile slug="max-mustermann" template="magazin"]' => 'Ein einzelnes Profil direkt in eine Seite einbetten.',
    '[feuer_mannschaft_cards random="1" count="4"]' => 'Mehrere Mitglieder als Kartenblock anzeigen.',
    '[feuer_mannschaft_count text="Aktive Mitglieder: %d"]' => 'Anzahl aktiver, zaehlbarer Mitglieder ausgeben.',
    '[feuer_mannschaft_person_link id="123"]' => 'Namen eines Mitglieds als Link ausgeben.',
);
?><!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo fm_index_escape($pluginName !== '' ? $pluginName : 'Feuer-Mannschaft'); ?></title>
    <style>
        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            color: #1f2937;
            background: #f5f5f5;
            line-height: 1.6;
        }

        .page {
            max-width: 1120px;
            margin: 0 auto;
            padding: 24px 16px 48px;
        }

        .section {
            margin-top: 18px;
            padding: 22px;
            border: 1px solid #d8d8d8;
            background: #ffffff;
        }

        .hero {
            border-top: 5px solid #b22222;
        }

        h1, h2, h3 {
            margin: 0 0 12px;
            line-height: 1.25;
        }

        h1 {
            font-size: 34px;
        }

        h2 {
            font-size: 24px;
        }

        h3 {
            font-size: 18px;
        }

        p {
            margin: 0 0 12px;
        }

        a {
            color: #0b5cab;
        }

        .lead {
            font-size: 18px;
        }

        .muted {
            color: #666666;
        }

        .grid {
            display: grid;
            gap: 14px;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        }

        .box {
            padding: 14px;
            border: 1px solid #e3e3e3;
            background: #fafafa;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            padding: 10px 12px;
            border-top: 1px solid #e5e5e5;
            vertical-align: top;
            text-align: left;
        }

        .table tr:first-child th,
        .table tr:first-child td {
            border-top: 0;
        }

        .table th {
            width: 240px;
            color: #555555;
        }

        ul {
            margin: 0;
            padding-left: 20px;
        }

        li {
            margin: 5px 0;
        }

        code {
            padding: 2px 6px;
            border: 1px solid #dcdcdc;
            background: #f0f0f0;
            font-family: Consolas, "Courier New", monospace;
            font-size: 14px;
        }

        @media (max-width: 760px) {
            .page {
                padding: 14px 10px 32px;
            }

            .section {
                padding: 16px;
            }

            h1 {
                font-size: 28px;
            }

            .table,
            .table tbody,
            .table tr,
            .table th,
            .table td {
                display: block;
                width: 100%;
            }

            .table th,
            .table td {
                padding: 7px 0;
            }

            .table th {
                border-top: 1px solid #e5e5e5;
            }
        }
    </style>
</head>
<body>
    <main class="page">
        <section class="section hero">
            <h1><?php echo fm_index_escape($pluginName !== '' ? $pluginName : 'Feuer-Mannschaft'); ?></h1>
            <p class="lead">Das WordPress-Plugin fuer Freiwillige Feuerwehren, die ihre Mannschaft modern, uebersichtlich und datenschutzbewusst auf der Website praesentieren wollen.</p>
            <p><?php echo fm_index_escape($pluginSummary); ?></p>
            <p><strong>Positionierung:</strong> Feuer-Mannschaft ist kein allgemeines Team-Plugin. Es ist auf Dienstgrade, Funktionen, Ausbildungen, Feuerwehr-Strukturen, oeffentliche Profile und einfache Pflege im WordPress-Backend ausgerichtet. Genau diese Kombination ist auf dem Plugin-Markt selten, weil sie speziell fuer Freiwillige Feuerwehr gedacht ist.</p>
        </section>

        <section class="section">
            <h2>Plugin-Informationen</h2>
            <table class="table">
                <tbody>
                    <tr><th>Name</th><td><?php echo fm_index_escape($pluginName); ?></td></tr>
                    <tr><th>Version im Projekt</th><td><?php echo fm_index_escape($pluginVersion); ?></td></tr>
                    <tr><th>Version im Update-System</th><td><?php echo fm_index_escape($latestVersion); ?></td></tr>
                    <tr><th>Autor</th><td><a href="<?php echo fm_index_escape($pluginAuthorUri); ?>"><?php echo fm_index_escape($pluginAuthor); ?></a></td></tr>
                    <tr><th>Plugin-URL</th><td><a href="<?php echo fm_index_escape($pluginUri); ?>"><?php echo fm_index_escape($pluginUri); ?></a></td></tr>
                    <tr><th>Update-Datei</th><td><a href="<?php echo fm_index_escape($updateUrl); ?>"><?php echo fm_index_escape($updateUrl); ?></a></td></tr>
                    <tr><th>Download</th><td><?php if ($downloadUrl !== '') : ?><a href="<?php echo fm_index_escape($downloadUrl); ?>"><?php echo fm_index_escape($downloadUrl); ?></a><?php else : ?>Nicht gesetzt<?php endif; ?></td></tr>
                    <tr><th>Requires PHP</th><td><?php echo fm_index_escape($pluginRequires); ?></td></tr>
                    <tr><th>Text Domain</th><td><?php echo fm_index_escape($pluginText); ?></td></tr>
                    <tr><th>Letzte Manifest-Aktualisierung</th><td><?php echo fm_index_escape($lastUpdated !== '' ? $lastUpdated : 'Nicht gesetzt'); ?></td></tr>
                </tbody>
            </table>
        </section>

        <section class="section">
            <h2>Massstab des Plugins</h2>
            <p>Der Umfang zeigt, dass es sich nicht um ein kleines Snippet handelt, sondern um ein vollwertiges WordPress-Plugin mit Adminbereich, Frontend-Ausgabe, Backup-System, Update-System, Templates, Assets und Dokumentation.</p>
            <table class="table">
                <tbody>
                    <tr><th>Projektdateien im Codeumfang</th><td><?php echo fm_index_format_number((int) $codeMetrics['files']); ?></td></tr>
                    <tr><th>Code- und Dokumentationszeilen</th><td><?php echo fm_index_format_number((int) $codeMetrics['lines']); ?></td></tr>
                    <tr><th>Zeichen im Projektcode</th><td><?php echo fm_index_format_number((int) $codeMetrics['characters']); ?></td></tr>
                    <tr><th>Zeilen in dieser index.php</th><td><?php echo fm_index_format_number((int) $indexMetrics['lines']); ?></td></tr>
                    <tr><th>Zeichen in dieser index.php</th><td><?php echo fm_index_format_number((int) $indexMetrics['characters']); ?></td></tr>
                </tbody>
            </table>
        </section>

        <section class="section">
            <h2>Warum fuer Freiwillige Feuerwehr?</h2>
            <div class="grid">
                <?php foreach ($advantages as $advantage) : ?>
                    <div class="box"><?php echo fm_index_escape($advantage); ?></div>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="section">
            <h2>Funktionen</h2>
            <div class="grid">
                <?php foreach ($featureSections as $sectionTitle => $items) : ?>
                    <div class="box">
                        <h3><?php echo fm_index_escape($sectionTitle); ?></h3>
                        <ul>
                            <?php foreach ($items as $item) : ?>
                                <li><?php echo fm_index_escape($item); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="section">
            <h2>Einstellungen und Bereiche</h2>
            <?php foreach ($settingsSections as $sectionTitle => $items) : ?>
                <div class="box">
                    <h3><?php echo fm_index_escape($sectionTitle); ?></h3>
                    <ul>
                        <?php foreach ($items as $item) : ?>
                            <li><?php echo fm_index_escape($item); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endforeach; ?>
        </section>

        <section class="section">
            <h2>Felder der Teilnehmerakte</h2>
            <table class="table">
                <tbody>
                    <?php foreach ($profileFields as $field => $description) : ?>
                        <tr>
                            <th><?php echo fm_index_escape($field); ?></th>
                            <td><?php echo fm_index_escape($description); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <section class="section">
            <h2>Shortcodes</h2>
            <table class="table">
                <tbody>
                    <?php foreach ($shortcodes as $code => $description) : ?>
                        <tr>
                            <th><code><?php echo fm_index_escape($code); ?></code></th>
                            <td><?php echo fm_index_escape($description); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <section class="section">
            <h2>Listenvorlagen</h2>
            <?php if (! empty($archiveViews)) : ?>
                <ul>
                    <?php foreach ($archiveViews as $template) : ?>
                        <li><strong><?php echo fm_index_escape($template['name']); ?></strong><?php echo $template['description'] !== '' ? ' - ' . fm_index_escape($template['description']) : ''; ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else : ?>
                <p class="muted">Keine Listenvorlagen gefunden.</p>
            <?php endif; ?>
        </section>

        <section class="section">
            <h2>Profilvorlagen</h2>
            <?php if (! empty($profileViews)) : ?>
                <ul>
                    <?php foreach ($profileViews as $template) : ?>
                        <li><strong><?php echo fm_index_escape($template['name']); ?></strong><?php echo $template['description'] !== '' ? ' - ' . fm_index_escape($template['description']) : ''; ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else : ?>
                <p class="muted">Keine Profilvorlagen gefunden.</p>
            <?php endif; ?>
        </section>

        <section class="section">
            <h2>Datenschutz und Kontrolle</h2>
            <p>Das Plugin speichert die Daten lokal in WordPress. Es ist nicht an eine externe Mitgliederplattform gebunden und vermeidet unnoetige externe Abhaengigkeiten. Administratoren entscheiden selbst, welche Felder gepflegt und welche Informationen oeffentlich gezeigt werden.</p>
            <p>Fuer Feuerwehren ist das wichtig: Mitgliederinformationen sollen sichtbar, aber kontrollierbar sein. Feuer-Mannschaft konzentriert sich deshalb auf oeffentliche Profildaten, klare Einstellungen und einfache Pflege.</p>
        </section>

        <section class="section">
            <h2>Projektdateien</h2>
            <ul>
                <li><a href="README.md">README.md</a></li>
                <li><a href="ANLEITUNG.md">ANLEITUNG.md</a></li>
                <li><a href="DOKUMENTATION.md">DOKUMENTATION.md</a></li>
                <li><a href="DEPLOYMENT.md">DEPLOYMENT.md</a></li>
                <li><a href="HISTORIE.md">HISTORIE.md</a></li>
                <li><a href="update.json">update.json</a></li>
            </ul>
        </section>

        <section class="section">
            <h2>Versionshistorie</h2>
            <?php if (! empty($historyEntries)) : ?>
                <?php foreach ($historyEntries as $entry) : ?>
                    <div class="box">
                        <h3><?php echo fm_index_escape($entry['title']); ?></h3>
                        <?php foreach ($entry['summary'] as $summary) : ?>
                            <p><?php echo fm_index_escape($summary); ?></p>
                        <?php endforeach; ?>
                        <?php if (! empty($entry['changes'])) : ?>
                            <ul>
                                <?php foreach ($entry['changes'] as $change) : ?>
                                    <li><?php echo fm_index_escape($change); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <p class="muted">Keine Versionshistorie gefunden.</p>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>

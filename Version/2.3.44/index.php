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

        if (! is_array($current) || $trimmed === '' || $trimmed === 'Geaendert:' || $trimmed === 'GeГ¤ndert:') {
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

$root           = __DIR__;
$pluginContents = fm_index_read_text($root . DIRECTORY_SEPARATOR . 'feuer-mannschaft.php');
$updateMetadata = fm_index_load_update_metadata($root . DIRECTORY_SEPARATOR . 'update.json');
$historyEntries = fm_index_parse_history($root . DIRECTORY_SEPARATOR . 'HISTORIE.md');
$archiveViews   = fm_index_collect_templates($root . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'uebersicht');
$profileViews   = fm_index_collect_templates($root . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'profil');

$pluginName      = fm_index_plugin_field($pluginContents, 'Plugin Name');
$pluginUri       = fm_index_plugin_field($pluginContents, 'Plugin URI');
$pluginVersion   = fm_index_plugin_field($pluginContents, 'Version');
$pluginAuthor    = fm_index_plugin_field($pluginContents, 'Author');
$pluginAuthorUri = fm_index_plugin_field($pluginContents, 'Author URI');
$pluginRequires  = fm_index_plugin_field($pluginContents, 'Requires PHP');
$pluginText      = fm_index_plugin_field($pluginContents, 'Text Domain');
$pluginSummary   = fm_index_plugin_field($pluginContents, 'Description');

$latestVersion = isset($updateMetadata['version']) ? (string) $updateMetadata['version'] : $pluginVersion;
$downloadUrl   = isset($updateMetadata['download_url']) ? (string) $updateMetadata['download_url'] : '';
$lastUpdated   = isset($updateMetadata['last_updated']) ? (string) $updateMetadata['last_updated'] : '';
$updateUrl     = $pluginUri !== '' ? rtrim($pluginUri, '/') . '/update.json' : 'update.json';

$functionSections = array(
    array(
        'title' => 'Mitgliederverwaltung',
        'items' => array(
            'Mitgliederprofile werden als normale WordPress-Beitraege gespeichert.',
            'Profile koennen kategorisiert, sortiert, archiviert und wieder aktiviert werden.',
            'Eigene Profil-Links und Zaehler-Ausschluss pro Mitglied sind moeglich.',
        ),
    ),
    array(
        'title' => 'Bilder und Galerie',
        'items' => array(
            'Mehrere Fotos pro Mitglied inklusive Slider in den Profilansichten.',
            'Standard-Profilfoto fuer Eintraege ohne eigenes Bild.',
            'Watermark-Text und zentrale Bildgroessen fuer Karten und Profile.',
        ),
    ),
    array(
        'title' => 'Listenfelder',
        'items' => array(
            'Dienstgrad, Funktion und Ausbildung sind zentral pflegbar.',
            'Zusaetzliche Listenfelder koennen mit Namen, Beschreibung und Optionen angelegt werden.',
            'Optionen koennen als reiner Text oder mit aktivem Link ausgegeben werden.',
        ),
    ),
    array(
        'title' => 'Frontend',
        'items' => array(
            'Mehrere Listen- und Profilvorlagen fuer verschiedene Darstellungen.',
            'Ausgabe ueber Shortcodes fuer Listen, Profile, Karten und Zaehler.',
            'Verlinkungen, Schnellansichten und Teamdarstellungen sind integriert.',
        ),
    ),
    array(
        'title' => 'Archive und Backups',
        'items' => array(
            'Archive lassen sich erstellen, hochladen, herunterladen und wiederherstellen.',
            'Gespeicherte Bilder und Profilinformationen werden in die Sicherung einbezogen.',
            'Wiederherstellung verarbeitet vorhandene Mitgliederdaten und Galerien.',
        ),
    ),
    array(
        'title' => 'Betrieb und Updates',
        'items' => array(
            'Eigenes Update-System ueber wdmin.com mit versionierten ZIP-Dateien.',
            'Versionshistorie und Dokumentation liegen direkt im Projekt.',
            'Entwicklerordner kann separat ueber diese index.php dokumentiert werden.',
        ),
    ),
);

$settingsSections = array(
    array(
        'title' => 'Allgemein',
        'items' => array(
            'Watermark-Text',
            'Standard-Profilfoto',
            'Freigegebene Kategorien',
            'Standardvorlage fuer Listen',
            'Standardvorlage fuer Profile',
            'Kartenbild Breite',
            'Kartenbild Hoehe',
            'Graustufen fuer Karten in Prozent',
            'Profilbild Hoehe',
            'Slider Autoplay',
            'Galerie aktivieren',
        ),
    ),
    array(
        'title' => 'Erwaehnungen',
        'items' => array(
            'Erwaehnungen aktivieren',
            'Freigegebene Beitragstypen fuer Verlinkungen',
            'Shortcodes fuer reine Mitgliedslinks',
        ),
    ),
    array(
        'title' => 'Listenfelder',
        'items' => array(
            'Dienstgrad-Liste',
            'Funktions-Liste',
            'Ausbildungs-Liste',
            'Zusatzfelder mit Einzelauswahl oder Mehrfachauswahl',
            'Optionen mit Text und Linkziel',
        ),
    ),
    array(
        'title' => 'Archive',
        'items' => array(
            'Archiv erstellen',
            'Archiv hochladen',
            'Archiv herunterladen',
            'Archiv wiederherstellen',
            'Archiv loeschen',
        ),
    ),
    array(
        'title' => 'Logs',
        'items' => array(
            'Protokoll fuer Einstellungen',
            'Protokoll fuer Mitglieder aenderungen',
            'Protokoll fuer Archivaktionen',
        ),
    ),
);

$shortcodes = array(
    array(
        'title' => 'Komplette Mannschaftsliste',
        'code' => '[feuer_mannschaft]',
        'description' => 'Zeigt die Standardliste aller aktiven Mitglieder.',
    ),
    array(
        'title' => 'Liste mit bestimmter Vorlage',
        'code' => '[feuer_mannschaft template="grayscale-cards"]',
        'description' => 'Zeigt die Listenansicht als Graustufen-Karten.',
    ),
    array(
        'title' => 'Ein einzelnes Profil',
        'code' => '[feuer_mannschaft_profile slug="max-mustermann" template="magazin"]',
        'description' => 'Bindet ein bestimmtes Profil direkt auf einer Seite ein.',
    ),
    array(
        'title' => 'Kartenblock',
        'code' => '[feuer_mannschaft_cards random="1" count="4"]',
        'description' => 'Zeigt mehrere Mitglieder als Kartenblock.',
    ),
    array(
        'title' => 'Mitgliederzahl',
        'code' => '[feuer_mannschaft_count text="Aktive Mitglieder: %d"]',
        'description' => 'Gibt die Anzahl aktiver Mitglieder aus.',
    ),
    array(
        'title' => 'Mitglied als Link',
        'code' => '[feuer_mannschaft_person_link id="123"]',
        'description' => 'Gibt nur den Namen eines Mitglieds als Link aus.',
    ),
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
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 16px;
            line-height: 1.6;
            color: #222222;
            background: #f4f4f4;
        }

        .page {
            max-width: 1080px;
            margin: 0 auto;
            padding: 24px 16px 40px;
        }

        section {
            margin-top: 16px;
            padding: 18px 20px;
            border: 1px solid #d9d9d9;
            background: #ffffff;
        }

        h1,
        h2,
        h3 {
            margin: 0 0 12px;
            line-height: 1.3;
        }

        h1 {
            font-size: 30px;
        }

        h2 {
            font-size: 22px;
        }

        h3 {
            font-size: 18px;
        }

        p {
            margin: 0 0 12px;
        }

        .simple-table {
            width: 100%;
            border-collapse: collapse;
        }

        .simple-table th,
        .simple-table td {
            padding: 10px 12px;
            border-top: 1px solid #e6e6e6;
            vertical-align: top;
            text-align: left;
        }

        .simple-table tr:first-child th,
        .simple-table tr:first-child td {
            border-top: 0;
        }

        .simple-table th {
            width: 220px;
            color: #555555;
            font-weight: 700;
        }

        .plain-list {
            margin: 0;
            padding-left: 20px;
        }

        .plain-list li {
            margin: 6px 0;
        }

        .block-list {
            display: grid;
            gap: 14px;
        }

        .block-item {
            padding-top: 14px;
            border-top: 1px solid #e6e6e6;
        }

        .block-item:first-child {
            padding-top: 0;
            border-top: 0;
        }

        .tag {
            display: inline-block;
            padding: 2px 8px;
            margin-right: 8px;
            border: 1px solid #d0d0d0;
            background: #fafafa;
            font-size: 13px;
        }

        code {
            padding: 2px 6px;
            background: #f0f0f0;
            border: 1px solid #dddddd;
            font-family: Consolas, "Courier New", monospace;
            font-size: 14px;
        }

        a {
            color: #0056b3;
        }

        .muted {
            color: #666666;
        }

        @media (max-width: 760px) {
            .page {
                padding: 16px 12px 28px;
            }

            section {
                padding: 14px 14px;
            }

            .simple-table,
            .simple-table tbody,
            .simple-table tr,
            .simple-table th,
            .simple-table td {
                display: block;
                width: 100%;
            }

            .simple-table th,
            .simple-table td {
                padding: 8px 0;
            }

            .simple-table th {
                border-top: 1px solid #e6e6e6;
            }

            .simple-table tr:first-child th {
                border-top: 0;
            }
        }
    </style>
</head>
<body>
    <main class="page">
        <section>
            <h1><?php echo fm_index_escape($pluginName !== '' ? $pluginName : 'Feuer-Mannschaft'); ?></h1>
            <?php if ($pluginSummary !== '') : ?>
                <p><?php echo fm_index_escape($pluginSummary); ?></p>
            <?php endif; ?>
            <p class="muted">Diese Seite beschreibt das Plugin, die wichtigsten Funktionen, die verfuegbaren Einstellungen, Shortcodes, Vorlagen und die Versionshistorie.</p>
        </section>

        <section>
            <h2>Plugin-Informationen</h2>
            <table class="simple-table">
                <tbody>
                    <tr>
                        <th>Name</th>
                        <td><?php echo fm_index_escape($pluginName); ?></td>
                    </tr>
                    <tr>
                        <th>Installierte Projektversion</th>
                        <td><?php echo fm_index_escape($pluginVersion); ?></td>
                    </tr>
                    <tr>
                        <th>Aktuelle Update-Version</th>
                        <td><?php echo fm_index_escape($latestVersion); ?></td>
                    </tr>
                    <tr>
                        <th>Autor</th>
                        <td>
                            <?php if ($pluginAuthorUri !== '') : ?>
                                <a href="<?php echo fm_index_escape($pluginAuthorUri); ?>"><?php echo fm_index_escape($pluginAuthor); ?></a>
                            <?php else : ?>
                                <?php echo fm_index_escape($pluginAuthor); ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Plugin-URL</th>
                        <td><a href="<?php echo fm_index_escape($pluginUri); ?>"><?php echo fm_index_escape($pluginUri); ?></a></td>
                    </tr>
                    <tr>
                        <th>Update-Datei</th>
                        <td><a href="<?php echo fm_index_escape($updateUrl); ?>"><?php echo fm_index_escape($updateUrl); ?></a></td>
                    </tr>
                    <tr>
                        <th>Download</th>
                        <td>
                            <?php if ($downloadUrl !== '') : ?>
                                <a href="<?php echo fm_index_escape($downloadUrl); ?>"><?php echo fm_index_escape($downloadUrl); ?></a>
                            <?php else : ?>
                                <span class="muted">Noch kein Download-Link vorhanden.</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Requires PHP</th>
                        <td><?php echo fm_index_escape($pluginRequires); ?></td>
                    </tr>
                    <tr>
                        <th>Text Domain</th>
                        <td><?php echo fm_index_escape($pluginText); ?></td>
                    </tr>
                    <tr>
                        <th>Zuletzt aktualisiert</th>
                        <td><?php echo fm_index_escape($lastUpdated !== '' ? $lastUpdated : 'Nicht gesetzt'); ?></td>
                    </tr>
                </tbody>
            </table>
        </section>

        <section>
            <h2>Beschreibung</h2>
            <p>Feuer-Mannschaft ist ein WordPress-Plugin fuer die Verwaltung von Mitgliederprofilen. Es ist fuer Vereins-, Feuerwehr- oder Teamseiten gedacht, auf denen Mitglieder strukturiert gepflegt, mit Bildern versehen, kategorisiert, archiviert und im Frontend dargestellt werden sollen.</p>
            <p>Das Plugin nutzt normale WordPress-Beitraege als technische Basis. Dadurch bleiben Inhalte, Kategorien, Permalinks und viele WordPress-Standardfunktionen kompatibel.</p>
        </section>

        <section>
            <h2>Funktionen des Plugins</h2>
            <div class="block-list">
                <?php foreach ($functionSections as $section) : ?>
                    <div class="block-item">
                        <h3><?php echo fm_index_escape($section['title']); ?></h3>
                        <ul class="plain-list">
                            <?php foreach ($section['items'] as $item) : ?>
                                <li><?php echo fm_index_escape($item); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section>
            <h2>Einstellungen des Plugins</h2>
            <div class="block-list">
                <?php foreach ($settingsSections as $section) : ?>
                    <div class="block-item">
                        <h3><?php echo fm_index_escape($section['title']); ?></h3>
                        <ul class="plain-list">
                            <?php foreach ($section['items'] as $item) : ?>
                                <li><?php echo fm_index_escape($item); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section>
            <h2>Shortcodes</h2>
            <div class="block-list">
                <?php foreach ($shortcodes as $shortcode) : ?>
                    <div class="block-item">
                        <h3><?php echo fm_index_escape($shortcode['title']); ?></h3>
                        <p><code><?php echo fm_index_escape($shortcode['code']); ?></code></p>
                        <p class="muted"><?php echo fm_index_escape($shortcode['description']); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section>
            <h2>Listenvorlagen</h2>
            <?php if (! empty($archiveViews)) : ?>
                <ul class="plain-list">
                    <?php foreach ($archiveViews as $template) : ?>
                        <li>
                            <strong><?php echo fm_index_escape($template['name']); ?></strong>
                            <?php if ($template['description'] !== '') : ?>
                                <span class="muted">- <?php echo fm_index_escape($template['description']); ?></span>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else : ?>
                <p class="muted">Keine Listenvorlagen gefunden.</p>
            <?php endif; ?>
        </section>

        <section>
            <h2>Profilvorlagen</h2>
            <?php if (! empty($profileViews)) : ?>
                <ul class="plain-list">
                    <?php foreach ($profileViews as $template) : ?>
                        <li>
                            <strong><?php echo fm_index_escape($template['name']); ?></strong>
                            <?php if ($template['description'] !== '') : ?>
                                <span class="muted">- <?php echo fm_index_escape($template['description']); ?></span>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else : ?>
                <p class="muted">Keine Profilvorlagen gefunden.</p>
            <?php endif; ?>
        </section>

        <section>
            <h2>Projektdateien</h2>
            <ul class="plain-list">
                <li><a href="README.md">README.md</a></li>
                <li><a href="ANLEITUNG.md">ANLEITUNG.md</a></li>
                <li><a href="DOKUMENTATION.md">DOKUMENTATION.md</a></li>
                <li><a href="DEPLOYMENT.md">DEPLOYMENT.md</a></li>
                <li><a href="HISTORIE.md">HISTORIE.md</a></li>
                <li><a href="update.json">update.json</a></li>
            </ul>
        </section>

        <section>
            <h2>Versionshistorie</h2>
            <?php if (! empty($historyEntries)) : ?>
                <div class="block-list">
                    <?php foreach ($historyEntries as $entry) : ?>
                        <div class="block-item">
                            <h3><?php echo fm_index_escape($entry['title']); ?></h3>
                            <?php foreach ($entry['summary'] as $summaryLine) : ?>
                                <p><?php echo fm_index_escape($summaryLine); ?></p>
                            <?php endforeach; ?>
                            <?php if (! empty($entry['changes'])) : ?>
                                <ul class="plain-list">
                                    <?php foreach ($entry['changes'] as $change) : ?>
                                        <li><?php echo fm_index_escape($change); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else : ?>
                <p class="muted">Keine Versionshistorie gefunden.</p>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>

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

$root = __DIR__;
$pluginContents = fm_index_read_text($root . DIRECTORY_SEPARATOR . 'feuer-mannschaft.php');
$updateMetadata = fm_index_load_update_metadata($root . DIRECTORY_SEPARATOR . 'update.json');
$historyEntries = fm_index_parse_history($root . DIRECTORY_SEPARATOR . 'HISTORIE.md');
$archiveViews = fm_index_collect_templates($root . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'uebersicht');
$profileViews = fm_index_collect_templates($root . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'profil');

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
    'Konsequent fuer Freiwillige Feuerwehren entwickelt und nicht als allgemeines Team-Plugin angelegt.',
    'Strukturierte Mitgliederverwaltung mit Dienstgrad, Funktion, Ausbildung, Zusatzfeldern und Kontaktfeldern.',
    'Mehrere Frontend-Ansichten fuer Mannschaftslisten und Einzelprofile ohne aufwendige Theme-Anpassung.',
    'Datensparsame Architektur mit lokaler Speicherung im eigenen WordPress-System.',
    'Eigener Update-Kanal ueber wdmin.com mit kontrollierter Release-Verteilung.',
    'Praxisorientierte Pflege im WordPress-Backend auch fuer Verwaltungen ohne technische Spezialkenntnisse.',
);

$featureSections = array(
    'Mitgliederverwaltung' => array(
        'Mitgliederprofile als regulaere WordPress-Beitraege mit eigener Plugin-Logik.',
        'Aktive und archivierte Profile mit sauberer Trennung.',
        'Sortierung, Kategoriezuordnung und individuelle Profilverlinkung.',
        'Markierung von Profilen, die im Gesamtzaehler nicht beruecksichtigt werden.',
    ),
    'Standardfelder und Zusatzfelder' => array(
        'Verwaltung von Dienstgrad, Funktion und Ausbildung als zentrale Listen.',
        'Eigene Listenfelder mit Beschreibung, Eintraegen und optionalen Links.',
        'Kontaktfelder fuer E-Mail, Instagram, Facebook, WhatsApp, Telegram und freie Links.',
        'Flexible Erweiterung fuer lokale Anforderungen einzelner Wehren.',
    ),
    'Frontend und Darstellung' => array(
        'Mehrere Listen- und Profilvorlagen fuer unterschiedliche Darstellungsstile.',
        'Kartenansichten, Rasteransichten und profilbezogene Einzeldarstellungen.',
        'Graustufen-Karten mit Prozentwert und Hover-Effekt fuer moderne Teamansichten.',
        'Shortcodes fuer Listen, Einzelprofile, Zaehler, Karten und Personenlinks.',
    ),
    'Medien und Inhalte' => array(
        'Profilfoto und zusaetzliche Galerie je Mitglied.',
        'Slider fuer mehrere Bilder im Frontend.',
        'Standardbild fuer fehlende Profilfotos.',
        'Wasserzeichen und konfigurierbare Bildgroessen.',
    ),
    'Betrieb und Sicherheit' => array(
        'Backup- und Restore-Funktionen fuer Plugin-Daten.',
        'Nachvollziehbare Aenderungen ueber integrierte Protokollierung.',
        'Kontrollierte Update-Pruefung gegen den eigenen Update-Server.',
        'WordPress-konforme Rollen-, Nonce- und Berechtigungspruefungen.',
    ),
);

$requirementsGermany = array(
    'Ausgelegt fuer Websites deutscher Freiwilliger Feuerwehren mit Fokus auf Mannschaftsdarstellung und oeffentliche Informationsbereitstellung.',
    'Unterstuetzt datensparsame Pflege, weil nur die wirklich benoetigten Profildaten veroeffentlicht werden muessen.',
    'Passt zu organisatorischen Anforderungen in Deutschland, bei denen Vereine, Gemeinden oder Feuerwehren ihre Inhalte selbst auf dem eigenen WordPress-System verwalten moechten.',
    'Hilft bei einer klaren Trennung zwischen oeffentlichen Angaben und internen Administrator-Hinweisen.',
    'Unterstuetzt einen kontrollierten Betrieb ohne externe Profilplattformen und ohne unnoetige Fremddienste.',
    'Kann so eingesetzt werden, dass lokale Datenschutz- und Impressumsprozesse der jeweiligen Wehr oder Traegereinrichtung eingehalten werden.',
);

$settingsSections = array(
    'Allgemein' => array(
        'Standard-Profilfoto, Wasserzeichen, Bildgroessen und Profilbildhoehe.',
        'Standardvorlagen fuer Mannschaftslisten und Einzelprofile.',
        'Farbliche Markierung ausgeschlossener Profilnamen.',
        'Autoplay-Zeit und Aktivierung der Galerie.',
    ),
    'Listenfelder' => array(
        'Pflege von Dienstgrad, Funktion und Ausbildung.',
        'Anlegen zusaetzlicher Feldgruppen mit Beschreibung und auswählbaren Eintraegen.',
        'Sortierung vorhandener Eintraege direkt in den Einstellungen.',
    ),
    'Kontaktfelder' => array(
        'Aktivierbare Kontaktarten fuer oeffentliche Profile.',
        'Validierung nach Feldtyp, etwa E-Mail-Adresse oder Social-Login.',
        'Ausgabe im Profil als Button-Elemente statt reine Textlinks.',
    ),
    'Erwaehnungen, Updates und Cache' => array(
        'Mitglieder-Erwaehnungen in freigegebenen Beitragstypen.',
        'Manuelle Pruefung der Plugin-Aktualisierung.',
        'Automatisches Leeren relevanter Caches nach Profil- und Einstellungs-Aenderungen.',
    ),
    'Archive und Logs' => array(
        'Archiv erstellen, herunterladen, importieren und wiederherstellen.',
        'Protokolle fuer Profilbearbeitungen, Einstellungen und Archivvorgaenge.',
    ),
);

$profileFields = array(
    'Stammdaten' => 'Vorname, Nachname, Eintrittsjahr, Kategorie, Sortierung und oeffentliche Beschreibung.',
    'Feuerwehrbezogene Listenfelder' => 'Dienstgrad, Funktion, Ausbildung und weitere zusaetzliche Listenfelder.',
    'Kontaktfelder' => 'Je nach Aktivierung E-Mail, Instagram, Facebook, WhatsApp, Telegram und freie Links.',
    'Medien' => 'Profilfoto und weitere Galerie-Bilder fuer die oeffentliche Darstellung.',
    'Zaehlersteuerung' => 'Ausschluss vom Gesamtzaehler mit eigener optischer Kennzeichnung im Frontend.',
    'Administrator-Hinweis' => 'Interner, einklappbarer Bereich nur fuer Administratoren im Backend und Frontend.',
);

$shortcodes = array(
    '[feuer_mannschaft]' => 'Gibt die komplette Mannschaftsliste mit Standardvorlage aus.',
    '[feuer_mannschaft template="team-grayscale"]' => 'Zeigt die Mannschaft mit Graustufen-Karten und Hover-Farbeffekt.',
    '[feuer_mannschaft_profile slug="mitglied-slug"]' => 'Bindet ein einzelnes Mitgliederprofil direkt in eine Seite ein.',
    '[feuer_mannschaft_cards count="4"]' => 'Zeigt eine definierte Anzahl Mitglieder als Kartenblock.',
    '[feuer_mannschaft_count text="Aktive Mitglieder: %d"]' => 'Gibt die Anzahl aktiver, zaehlbarer Mitglieder aus.',
    '[feuer_mannschaft_person_link id="123"]' => 'Erzeugt einen verlinkten Mitgliedsnamen fuer andere Inhalte.',
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
            background: #f7f7f7;
            color: #1f2937;
            line-height: 1.65;
        }

        .page {
            max-width: 1100px;
            margin: 0 auto;
            padding: 24px 16px 40px;
        }

        .section {
            margin-top: 18px;
            padding: 22px;
            background: #ffffff;
            border: 1px solid #d9d9d9;
        }

        .hero {
            border-top: 4px solid #b22222;
        }

        h1,
        h2,
        h3 {
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
            text-align: left;
            vertical-align: top;
        }

        .table tr:first-child th,
        .table tr:first-child td {
            border-top: 0;
        }

        .table th {
            width: 250px;
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
                padding: 14px 10px 28px;
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
            <p class="lead">Professionelle Mitgliederverwaltung fuer Freiwillige Feuerwehren auf WordPress-Basis.</p>
            <p><?php echo fm_index_escape($pluginSummary); ?></p>
            <p>Feuer-Mannschaft wurde dafuer entwickelt, Mannschaften, Funktionen und oeffentliche Profilinformationen sauber, modern und nachvollziehbar auf Feuerwehr-Websites abzubilden. Der Schwerpunkt liegt nicht auf allgemeinem Team-Management, sondern auf den Anforderungen von Freiwilligen Feuerwehren im deutschsprachigen Raum.</p>
        </section>

        <section class="section">
            <h2>Plugin-Informationen</h2>
            <table class="table">
                <tbody>
                    <tr><th>Name</th><td><?php echo fm_index_escape($pluginName); ?></td></tr>
                    <tr><th>Projektversion</th><td><?php echo fm_index_escape($pluginVersion); ?></td></tr>
                    <tr><th>Aktuelle Update-Version</th><td><?php echo fm_index_escape($latestVersion); ?></td></tr>
                    <tr><th>Autor</th><td><a href="<?php echo fm_index_escape($pluginAuthorUri); ?>"><?php echo fm_index_escape($pluginAuthor); ?></a></td></tr>
                    <tr><th>Entwickler-Website</th><td><a href="https://wdmin.com/">https://wdmin.com/</a></td></tr>
                    <tr><th>Plugin-URL</th><td><a href="<?php echo fm_index_escape($pluginUri); ?>"><?php echo fm_index_escape($pluginUri); ?></a></td></tr>
                    <tr><th>Update-Datei</th><td><a href="<?php echo fm_index_escape($updateUrl); ?>"><?php echo fm_index_escape($updateUrl); ?></a></td></tr>
                    <tr><th>Download-Paket</th><td><?php if ($downloadUrl !== '') : ?><a href="<?php echo fm_index_escape($downloadUrl); ?>"><?php echo fm_index_escape($downloadUrl); ?></a><?php else : ?>Nicht gesetzt<?php endif; ?></td></tr>
                    <tr><th>Requires PHP</th><td><?php echo fm_index_escape($pluginRequires); ?></td></tr>
                    <tr><th>Text Domain</th><td><?php echo fm_index_escape($pluginText); ?></td></tr>
                    <tr><th>Letzte Manifest-Aktualisierung</th><td><?php echo fm_index_escape($lastUpdated !== '' ? $lastUpdated : 'Nicht gesetzt'); ?></td></tr>
                </tbody>
            </table>
        </section>

        <section class="section">
            <h2>Was das Plugin leistet</h2>
            <div class="grid">
                <?php foreach ($advantages as $advantage) : ?>
                    <div class="box"><?php echo fm_index_escape($advantage); ?></div>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="section">
            <h2>Funktionsbereiche</h2>
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
            <h2>Anforderungen und Einsatz in Deutschland</h2>
            <p>Feuer-Mannschaft ist fuer den Einsatz auf Websites deutscher Feuerwehren ausgelegt, bei denen oeffentliche Mannschaftsdarstellungen, nachvollziehbare Pflege und ein datensparsamer Betrieb wichtig sind.</p>
            <ul>
                <?php foreach ($requirementsGermany as $item) : ?>
                    <li><?php echo fm_index_escape($item); ?></li>
                <?php endforeach; ?>
            </ul>
            <p>Das Plugin ersetzt keine individuelle Rechtsberatung. Es unterstuetzt jedoch einen Aufbau, bei dem die jeweilige Feuerwehr oder Traegerorganisation Inhalte selbst kontrolliert, nur benoetigte Angaben veroeffentlicht und interne Informationen sauber trennt.</p>
        </section>

        <section class="section">
            <h2>Bereiche in den Einstellungen</h2>
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
            <h2>Was in einer Mitgliederakte gepflegt werden kann</h2>
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
            <h2>Shortcodes fuer die Ausgabe</h2>
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
            <h2>Verfuegbare Listenansichten</h2>
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
            <h2>Verfuegbare Profilansichten</h2>
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
            <h2>Autor und Entwicklung</h2>
            <p>Entwickelt von <strong><?php echo fm_index_escape($pluginAuthor !== '' ? $pluginAuthor : 'Walter Faerber'); ?></strong>. Die technische Bereitstellung, Release-Verteilung und Update-Kommunikation erfolgen ueber <a href="https://wdmin.com/">wdmin.com</a>.</p>
            <p>Das Plugin ist auf langfristige Pflege ausgelegt: versionierte Releases, eigene Update-Infrastruktur, Dokumentation, Historie und klare Weiterentwicklung fuer Feuerwehr-Websites.</p>
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

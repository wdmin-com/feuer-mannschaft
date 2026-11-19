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

function fm_index_collect_template_names(string $directory): array
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

        $label = fm_index_plugin_field($contents, 'Template Name');
        if ($label === '') {
            continue;
        }

        $templates[] = $label;
    }

    sort($templates, SORT_NATURAL | SORT_FLAG_CASE);

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
                'title'   => trim((string) $matches[1]),
                'summary' => array(),
                'changes' => array(),
            );
            continue;
        }

        if (! is_array($current) || $trimmed === '' || $trimmed === 'Geaendert:' || $trimmed === 'Geändert:') {
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

$root            = __DIR__;
$pluginContents  = fm_index_read_text($root . DIRECTORY_SEPARATOR . 'feuer-mannschaft.php');
$updateMetadata  = fm_index_load_update_metadata($root . DIRECTORY_SEPARATOR . 'update.json');
$historyEntries  = fm_index_parse_history($root . DIRECTORY_SEPARATOR . 'HISTORIE.md');
$archiveViews    = fm_index_collect_template_names($root . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'uebersicht');
$profileViews    = fm_index_collect_template_names($root . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'profil');

$pluginName      = fm_index_plugin_field($pluginContents, 'Plugin Name');
$pluginUri       = fm_index_plugin_field($pluginContents, 'Plugin URI');
$pluginVersion   = fm_index_plugin_field($pluginContents, 'Version');
$pluginAuthor    = fm_index_plugin_field($pluginContents, 'Author');
$pluginAuthorUri = fm_index_plugin_field($pluginContents, 'Author URI');
$pluginRequires  = fm_index_plugin_field($pluginContents, 'Requires PHP');

$latestVersion   = isset($updateMetadata['version']) ? (string) $updateMetadata['version'] : $pluginVersion;
$downloadUrl     = isset($updateMetadata['download_url']) ? (string) $updateMetadata['download_url'] : '';
$updateUrl       = $pluginUri !== '' ? rtrim($pluginUri, '/') . '/update.json' : 'update.json';
$lastUpdated     = isset($updateMetadata['last_updated']) ? (string) $updateMetadata['last_updated'] : '';
$currentYear     = gmdate('Y');

$featureCards = array(
    array(
        'title' => 'Profile als WordPress-Beitraege',
        'copy'  => 'Mitglieder werden als normale Beitraege gepflegt, koennen kategorisiert, archiviert und sauber ueber Listen ausgegeben werden.',
    ),
    array(
        'title' => 'Fotos, Galerie und Standardbild',
        'copy'  => 'Eigene Fotos, Default-Profilbild, Wasserzeichen und flexible Einzelansichten fuer jedes Profil.',
    ),
    array(
        'title' => 'Listenfelder mit Links',
        'copy'  => 'Dienstgrad, Funktion, Ausbildung und zusaetzliche Gruppen lassen sich zentral pflegen und bei Bedarf mit Linkzielen versehen.',
    ),
    array(
        'title' => 'Frontend-Vorlagen',
        'copy'  => 'Mehrere Listen- und Profil-Templates fuer klassische Listen, Karten, Spotlight, Verzeichnis oder dossierartige Detailseiten.',
    ),
    array(
        'title' => 'Archive und Backups',
        'copy'  => 'Profile koennen archiviert, Backups erstellt, hochgeladen und bei Bedarf wiederhergestellt werden.',
    ),
    array(
        'title' => 'Eigener Update-Endpunkt',
        'copy'  => 'Das Plugin kann ueber wdmin.com mit versionierten ZIP-Paketen und eigenem update.json ausgeliefert werden.',
    ),
);

$shortcodeExamples = array(
    array(
        'label' => 'Komplette Mannschaftsliste',
        'code'  => '[feuer_mannschaft template="list"]',
        'copy'  => 'Rendert die Standardliste aller aktiven Profile.',
    ),
    array(
        'label' => 'Graustufen-Karten',
        'code'  => '[feuer_mannschaft template="grayscale-cards"]',
        'copy'  => 'Zeigt die Mannschaft als ausgebleichte Portraitkarten mit dem eingestellten Graustufenwert.',
    ),
    array(
        'label' => 'Verzeichnis mit eigener Profilvorlage',
        'code'  => '[feuer_mannschaft_list category="aktive-mitglieder" template="directory" single_template="dossier"]',
        'copy'  => 'Zeigt eine gefilterte Liste mit Verzeichnislayout und oeffnet Profile im Dossier-Stil.',
    ),
    array(
        'label' => 'Einzelprofil direkt einbetten',
        'code'  => '[feuer_mannschaft_profile slug="max-muster" template="magazin"]',
        'copy'  => 'Bindet genau ein Profil ueber Slug oder alternativ ueber ID, Name oder random ein.',
    ),
    array(
        'label' => 'Kartenblock fuer Highlights',
        'code'  => '[feuer_mannschaft_cards count="4" random="1" template="cards-modal" title="Unser Team"]',
        'copy'  => 'Waehlt mehrere Profile aus und zeigt sie als Kartenblock mit eigenem Titel.',
    ),
    array(
        'label' => 'Zaehler',
        'code'  => '[feuer_mannschaft_count text="%d aktive Mitglieder"]',
        'copy'  => 'Gibt die Anzahl der aktuell zaehlbaren Profile aus.',
    ),
);
?><!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo fm_index_escape($pluginName !== '' ? $pluginName : 'Feuer-Mannschaft'); ?></title>
    <style>
        :root {
            --bg: #f4f1ea;
            --ink: #111827;
            --muted: #5b6472;
            --line: rgba(17, 24, 39, 0.12);
            --card: rgba(255, 255, 255, 0.94);
            --accent: #b22222;
            --accent-dark: #7e1717;
            --accent-soft: #fff3ee;
            --shadow: 0 18px 45px rgba(15, 23, 42, 0.08);
            --radius: 24px;
            --radius-sm: 18px;
        }

        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: "Segoe UI", "Trebuchet MS", sans-serif;
            color: var(--ink);
            background:
                radial-gradient(circle at top left, rgba(178, 34, 34, 0.14), transparent 28%),
                radial-gradient(circle at top right, rgba(14, 116, 144, 0.10), transparent 24%),
                linear-gradient(180deg, #fcfbf7 0%, var(--bg) 100%);
        }

        a { color: inherit; }

        .shell {
            width: min(1180px, calc(100% - 32px));
            margin: 0 auto;
            padding: 28px 0 64px;
        }

        .hero,
        .section {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }

        .hero {
            padding: 36px;
            position: relative;
            overflow: hidden;
        }

        .hero::after {
            content: "";
            position: absolute;
            inset: auto -60px -60px auto;
            width: 240px;
            height: 240px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(178, 34, 34, 0.16), transparent 70%);
            pointer-events: none;
        }

        .eyebrow {
            display: inline-flex;
            padding: 7px 12px;
            border-radius: 999px;
            background: var(--accent-soft);
            color: var(--accent-dark);
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        h1 {
            margin: 16px 0 14px;
            font-size: clamp(2.3rem, 5vw, 4.25rem);
            line-height: 0.98;
        }

        .lead {
            max-width: 760px;
            margin: 0;
            color: var(--muted);
            font-size: 1.08rem;
            line-height: 1.7;
        }

        .hero-stats,
        .feature-grid,
        .example-grid,
        .template-grid,
        .history-grid {
            display: grid;
            gap: 16px;
        }

        .hero-stats {
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            margin-top: 28px;
        }

        .stat,
        .feature-card,
        .example-card,
        .template-card,
        .history-card {
            padding: 18px 20px;
            border-radius: var(--radius-sm);
            background: #fff;
            border: 1px solid rgba(17, 24, 39, 0.08);
        }

        .stat strong,
        .template-card strong {
            display: block;
            margin-top: 8px;
            font-size: 1.08rem;
        }

        .stat span,
        .feature-card p,
        .example-card p,
        .template-card p,
        .history-card p,
        .history-card li {
            color: var(--muted);
            line-height: 1.65;
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 28px;
        }

        .button,
        .button-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 46px;
            padding: 0 18px;
            border-radius: 14px;
            font-weight: 700;
            text-decoration: none;
            transition: transform 0.18s ease, background-color 0.18s ease, color 0.18s ease;
        }

        .button {
            color: #fff;
            background: linear-gradient(135deg, var(--accent) 0%, #d34a2f 100%);
        }

        .button-secondary {
            color: var(--ink);
            background: #fff;
            border: 1px solid var(--line);
        }

        .button:hover,
        .button-secondary:hover {
            transform: translateY(-1px);
        }

        .section {
            padding: 28px;
            margin-top: 22px;
        }

        .section-header {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 12px 18px;
            align-items: end;
            margin-bottom: 20px;
        }

        .section-header h2 {
            margin: 0;
            font-size: clamp(1.5rem, 3vw, 2.3rem);
        }

        .section-header p {
            margin: 0;
            max-width: 720px;
            color: var(--muted);
            line-height: 1.7;
        }

        .feature-grid { grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); }
        .example-grid { grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); }
        .template-grid { grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); }
        .history-grid { grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); }

        .feature-card h3,
        .example-card h3,
        .template-card h3,
        .history-card h3 {
            margin: 0 0 10px;
            font-size: 1.08rem;
        }

        code,
        pre {
            font-family: Consolas, "Courier New", monospace;
        }

        pre {
            margin: 0 0 12px;
            padding: 14px 16px;
            border-radius: 16px;
            background: #161b22;
            color: #f8fafc;
            overflow-x: auto;
        }

        .template-list {
            margin: 14px 0 0;
            padding-left: 18px;
            color: var(--muted);
        }

        .template-list li + li {
            margin-top: 6px;
        }

        .meta-links {
            display: flex;
            flex-wrap: wrap;
            gap: 10px 14px;
            margin-top: 10px;
            font-size: 0.95rem;
            color: var(--muted);
        }

        .meta-links a {
            color: var(--accent-dark);
            text-decoration: none;
            font-weight: 600;
        }

        .meta-links a:hover {
            text-decoration: underline;
        }

        .history-card ul {
            margin: 12px 0 0;
            padding-left: 18px;
        }

        .footer {
            margin-top: 18px;
            padding: 0 6px;
            color: var(--muted);
            font-size: 0.92rem;
        }

        @media (max-width: 720px) {
            .shell {
                width: min(100% - 20px, 1180px);
                padding-top: 16px;
            }

            .hero,
            .section {
                padding: 20px;
                border-radius: 22px;
            }

            .hero-actions {
                flex-direction: column;
            }

            .button,
            .button-secondary {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <main class="shell">
        <section class="hero">
            <span class="eyebrow">WordPress Plugin fuer Mannschaftsprofile</span>
            <h1><?php echo fm_index_escape($pluginName !== '' ? $pluginName : 'Feuer-Mannschaft'); ?></h1>
            <p class="lead">
                Feuer-Mannschaft verwaltet Mitgliederprofile, Fotos, Archivstatus, Listenfelder und
                Frontend-Ausgaben in einem eigenstaendigen Plugin. Diese Seite dient als Entwickler-
                und Release-Uebersicht fuer das Paket auf wdmin.com.
            </p>

            <div class="hero-stats">
                <article class="stat">
                    <span>Aktuelle Version</span>
                    <strong><?php echo fm_index_escape($latestVersion !== '' ? $latestVersion : $pluginVersion); ?></strong>
                </article>
                <article class="stat">
                    <span>Autor</span>
                    <strong><?php echo fm_index_escape($pluginAuthor !== '' ? $pluginAuthor : 'Walter Faerber'); ?></strong>
                </article>
                <article class="stat">
                    <span>PHP-Anforderung</span>
                    <strong><?php echo fm_index_escape($pluginRequires !== '' ? $pluginRequires : '8.0'); ?></strong>
                </article>
                <article class="stat">
                    <span>Letztes Update</span>
                    <strong><?php echo fm_index_escape($lastUpdated !== '' ? $lastUpdated : 'n/a'); ?></strong>
                </article>
            </div>

            <div class="hero-actions">
                <?php if ($downloadUrl !== '') : ?>
                    <a class="button" href="<?php echo fm_index_escape($downloadUrl); ?>">Aktuelles ZIP laden</a>
                <?php endif; ?>
                <a class="button-secondary" href="<?php echo fm_index_escape($updateUrl); ?>">update.json oeffnen</a>
                <a class="button-secondary" href="HISTORIE.md">Historie als Datei</a>
            </div>

            <div class="meta-links">
                <?php if ($pluginUri !== '') : ?>
                    <a href="<?php echo fm_index_escape($pluginUri); ?>">Plugin-URL</a>
                <?php endif; ?>
                <?php if ($pluginAuthorUri !== '') : ?>
                    <a href="<?php echo fm_index_escape($pluginAuthorUri); ?>">Autorenseite</a>
                <?php endif; ?>
                <a href="README.md">README</a>
                <a href="DOKUMENTATION.md">Dokumentation</a>
                <a href="DEPLOYMENT.md">Deployment</a>
            </div>
        </section>

        <section class="section">
            <div class="section-header">
                <div>
                    <h2>Funktionsueberblick</h2>
                    <p>Die wichtigsten Bausteine des Plugins fuer Redakteure, Admins und die Frontend-Ausgabe.</p>
                </div>
            </div>
            <div class="feature-grid">
                <?php foreach ($featureCards as $card) : ?>
                    <article class="feature-card">
                        <h3><?php echo fm_index_escape($card['title']); ?></h3>
                        <p><?php echo fm_index_escape($card['copy']); ?></p>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="section">
            <div class="section-header">
                <div>
                    <h2>Shortcodes und Beispiele</h2>
                    <p>Die folgenden Beispiele basieren auf den aktuell hinterlegten Shortcodes des Plugins und koennen direkt in Seiten oder Beitraegen verwendet werden.</p>
                </div>
            </div>
            <div class="example-grid">
                <?php foreach ($shortcodeExamples as $example) : ?>
                    <article class="example-card">
                        <h3><?php echo fm_index_escape($example['label']); ?></h3>
                        <pre><code><?php echo fm_index_escape($example['code']); ?></code></pre>
                        <p><?php echo fm_index_escape($example['copy']); ?></p>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="section">
            <div class="section-header">
                <div>
                    <h2>Mitgelieferte Vorlagen</h2>
                    <p>Die Template-Namen werden direkt aus den PHP-Vorlagen des Plugins gelesen und spiegeln damit den aktuellen Stand des Pakets wider.</p>
                </div>
            </div>
            <div class="template-grid">
                <article class="template-card">
                    <span class="eyebrow">Archive / Listen</span>
                    <strong><?php echo fm_index_escape((string) count($archiveViews)); ?> Vorlagen</strong>
                    <ul class="template-list">
                        <?php foreach ($archiveViews as $template) : ?>
                            <li><?php echo fm_index_escape($template); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </article>
                <article class="template-card">
                    <span class="eyebrow">Profile / Einzelansicht</span>
                    <strong><?php echo fm_index_escape((string) count($profileViews)); ?> Vorlagen</strong>
                    <ul class="template-list">
                        <?php foreach ($profileViews as $template) : ?>
                            <li><?php echo fm_index_escape($template); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </article>
            </div>
        </section>

        <section class="section">
            <div class="section-header">
                <div>
                    <h2>Update-Ablauf</h2>
                    <p>Das Plugin nutzt einen eigenen Update-Endpunkt unter wdmin.com mit versionierten ZIP-Dateien und einem stabilen Manifest.</p>
                </div>
            </div>
            <div class="feature-grid">
                <article class="feature-card">
                    <h3>1. Version anheben</h3>
                    <p>Version in <code>feuer-mannschaft.php</code>, <code>update.json</code> und <code>HISTORIE.md</code> aktualisieren.</p>
                </article>
                <article class="feature-card">
                    <h3>2. ZIP bauen</h3>
                    <p>Release mit <code>tools/build-release-package.ps1</code> erzeugen und versioniert unter <code>release/&lt;version&gt;/</code> bereitstellen.</p>
                </article>
                <article class="feature-card">
                    <h3>3. Manifest hochladen</h3>
                    <p><code>update.json</code> im Hauptordner aktualisieren, damit Installationen die neue Version erkennen.</p>
                </article>
                <article class="feature-card">
                    <h3>4. Historie anzeigen</h3>
                    <p>Die folgende Release-Historie wird automatisch aus <code>HISTORIE.md</code> geladen und hier angezeigt.</p>
                </article>
            </div>
        </section>

        <section class="section">
            <div class="section-header">
                <div>
                    <h2>Versionshistorie</h2>
                    <p>Die Eintraege unten werden direkt aus <code>HISTORIE.md</code> gelesen. Aenderungen an der Historie erscheinen dadurch automatisch auf dieser Seite.</p>
                </div>
            </div>
            <div class="history-grid">
                <?php if (! empty($historyEntries)) : ?>
                    <?php foreach (array_slice($historyEntries, 0, 10) as $entry) : ?>
                        <article class="history-card">
                            <h3><?php echo fm_index_escape(isset($entry['title']) ? (string) $entry['title'] : 'Release'); ?></h3>
                            <?php if (! empty($entry['summary'])) : ?>
                                <p><?php echo fm_index_escape(implode(' ', (array) $entry['summary'])); ?></p>
                            <?php endif; ?>
                            <?php if (! empty($entry['changes'])) : ?>
                                <ul>
                                    <?php foreach ((array) $entry['changes'] as $change) : ?>
                                        <li><?php echo fm_index_escape((string) $change); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                <?php else : ?>
                    <article class="history-card">
                        <h3>Keine Historie gefunden</h3>
                        <p>Die Datei <code>HISTORIE.md</code> konnte nicht gelesen werden.</p>
                    </article>
                <?php endif; ?>
            </div>
        </section>

        <p class="footer">
            Entwicklerseite fuer <?php echo fm_index_escape($pluginName !== '' ? $pluginName : 'Feuer-Mannschaft'); ?>
            | Stand <?php echo fm_index_escape($latestVersion !== '' ? $latestVersion : $pluginVersion); ?>
            | &copy; <?php echo fm_index_escape($currentYear); ?> <?php echo fm_index_escape($pluginAuthor !== '' ? $pluginAuthor : 'Walter Faerber'); ?>
        </p>
    </main>
</body>
</html>

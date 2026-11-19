<?php

if (! defined('ABSPATH')) {
    exit;
}

$dashboard_summary = isset($dashboard_summary) && is_array($dashboard_summary) ? $dashboard_summary : array();
$last_archive      = isset($dashboard_summary['last_archive']) && is_array($dashboard_summary['last_archive']) ? $dashboard_summary['last_archive'] : array();
$has_archive       = ! empty($last_archive['name']);
$has_update        = ! empty($dashboard_summary['has_update']);
$automatic_updates = ! empty($dashboard_summary['automatic_updates']);
?>
<section class="feuer-mannschaft-admin-hero" aria-label="Mannschaft Status">
    <div class="feuer-mannschaft-admin-hero-copy">
        <span class="feuer-mannschaft-admin-kicker">Mannschaft Cockpit</span>
        <h2>Profile, Archive und Updates auf einen Blick</h2>
        <p>Die wichtigsten Signale fuer Pflege, Sicherung und Veroeffentlichung der Mannschaftsdaten.</p>
    </div>

    <div class="feuer-mannschaft-admin-hero-actions">
        <a class="button button-primary" href="<?php echo esc_url(isset($dashboard_summary['add_new_url']) ? $dashboard_summary['add_new_url'] : '#'); ?>">Neues Profil</a>
        <a class="button button-secondary" href="<?php echo esc_url(isset($dashboard_summary['list_url']) ? $dashboard_summary['list_url'] : '#'); ?>">Alle Profile</a>
    </div>
</section>

<section class="feuer-mannschaft-dashboard-grid" aria-label="Mannschaft Kennzahlen">
    <article class="feuer-mannschaft-stat-card is-strong">
        <span class="feuer-mannschaft-stat-label">Aktive Profile</span>
        <strong><?php echo esc_html((string) (isset($dashboard_summary['active_count']) ? $dashboard_summary['active_count'] : 0)); ?></strong>
        <span class="feuer-mannschaft-stat-note">sichtbar und einsatzbereit</span>
    </article>

    <article class="feuer-mannschaft-stat-card">
        <span class="feuer-mannschaft-stat-label">Archiviert</span>
        <strong><?php echo esc_html((string) (isset($dashboard_summary['archived_count']) ? $dashboard_summary['archived_count'] : 0)); ?></strong>
        <span class="feuer-mannschaft-stat-note"><?php echo esc_html((string) (isset($dashboard_summary['total_count']) ? $dashboard_summary['total_count'] : 0)); ?> Profile gesamt</span>
    </article>

    <article class="feuer-mannschaft-stat-card <?php echo empty($dashboard_summary['missing_photo']) ? 'is-ok' : 'is-warning'; ?>">
        <span class="feuer-mannschaft-stat-label">Ohne eigenes Foto</span>
        <strong><?php echo esc_html((string) (isset($dashboard_summary['missing_photo']) ? $dashboard_summary['missing_photo'] : 0)); ?></strong>
        <span class="feuer-mannschaft-stat-note"><?php echo empty($dashboard_summary['missing_photo']) ? 'alles gepflegt' : 'Standardfoto pruefen'; ?></span>
    </article>

    <article class="feuer-mannschaft-stat-card <?php echo empty($dashboard_summary['missing_category']) ? 'is-ok' : 'is-warning'; ?>">
        <span class="feuer-mannschaft-stat-label">Ohne Kategorie</span>
        <strong><?php echo esc_html((string) (isset($dashboard_summary['missing_category']) ? $dashboard_summary['missing_category'] : 0)); ?></strong>
        <span class="feuer-mannschaft-stat-note"><?php echo empty($dashboard_summary['missing_category']) ? 'Zuordnung vollstaendig' : 'Zuordnung offen'; ?></span>
    </article>
</section>

<section class="feuer-mannschaft-status-strip" aria-label="Systemstatus">
    <a class="feuer-mannschaft-status-pill <?php echo $has_archive ? 'is-ok' : 'is-warning'; ?>" href="<?php echo esc_url(isset($dashboard_summary['archives_url']) ? $dashboard_summary['archives_url'] : '#'); ?>">
        <span>Backup</span>
        <strong><?php echo $has_archive ? esc_html(isset($last_archive['modified_human']) ? $last_archive['modified_human'] : $last_archive['name']) : 'Noch kein Archiv'; ?></strong>
    </a>

    <a class="feuer-mannschaft-status-pill <?php echo $has_update ? 'is-warning' : 'is-ok'; ?>" href="<?php echo esc_url(isset($dashboard_summary['updates_url']) ? $dashboard_summary['updates_url'] : '#'); ?>">
        <span>Updates</span>
        <strong><?php echo $has_update ? 'Update verfuegbar' : 'Aktuell'; ?></strong>
    </a>

    <span class="feuer-mannschaft-status-pill <?php echo $automatic_updates ? 'is-ok' : 'is-muted'; ?>">
        <span>Auto-Updates</span>
        <strong><?php echo $automatic_updates ? 'Aktiv' : 'Manuell'; ?></strong>
    </span>
</section>

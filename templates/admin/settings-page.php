<?php

if (! defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap feuer-mannschaft-settings">
    <?php
    $settings_help = array(
        'general' => array('title' => 'Listen und Profile', 'text' => 'Lege die Standardvorlagen fest und prüfe die Vorschau. Änderungen werden erst nach dem Speichern auf der Website verwendet.', 'link' => home_url('/mannschaft/'), 'link_label' => 'Mannschaftsseite ansehen'),
        'mentions' => array('title' => 'Erwähnungen', 'text' => 'Wähle, in welchen Beitragsarten Erwähnungen erkannt und auf Profilseiten angezeigt werden.', 'link' => 'https://wdmin.com/plugins/feuer-mannschaft/', 'link_label' => 'Dokumentation öffnen'),
        'fields' => array('title' => 'Profilfelder', 'text' => 'Listenwerte erscheinen in der Profilbearbeitung. Entfernte Werte, die noch verwendet werden, benötigen eine Ersetzung.', 'link' => admin_url('post-new.php?post_type=' . Feuer_Mannschaft_Plugin::POST_TYPE), 'link_label' => 'Neues Profil anlegen'),
        'archives' => array('title' => 'Datensicherung', 'text' => 'Erstelle regelmäßig ein Archiv. Beim Wiederherstellen kannst du die zu importierenden Bereiche auswählen.', 'link' => 'https://wdmin.com/plugins/feuer-mannschaft/', 'link_label' => 'Hinweise zur Sicherung'),
        'updates' => array('title' => 'Updates', 'text' => 'Automatische Updates werden von WordPress installiert. Die manuelle Prüfung aktualisiert den Update-Status dieses Plugins.', 'link' => 'https://wdmin.com/plugins/feuer-mannschaft/', 'link_label' => 'Versionshinweise ansehen'),
        'logs' => array('title' => 'Aktivitätsprotokoll', 'text' => 'Filtere die Ereignisse nach Administrator, Aktion und Zeitraum. Der CSV-Export übernimmt die aktuellen Filter.', 'link' => 'https://wdmin.com/plugins/feuer-mannschaft/', 'link_label' => 'Protokoll-Dokumentation'),
        'health' => array('title' => 'Systemdiagnose', 'text' => 'Die Prüfungen lesen den aktuellen Zustand aus und ändern keine Einstellungen oder Dateien.', 'link' => 'https://wdmin.com/plugins/feuer-mannschaft/', 'link_label' => 'Fehlerbehebung öffnen'),
        'media' => array('title' => 'Mitgliederfotos', 'text' => 'Jedes zugeordnete Foto öffnet direkt das Profil des jeweiligen Mitglieds. Nicht verwendete Dateien werden getrennt angezeigt.', 'link' => 'https://wdmin.com/plugins/feuer-mannschaft/', 'link_label' => 'Medienhinweise ansehen'),
    );
    $settings_help = isset($settings_help[$current_tab]) ? $settings_help[$current_tab] : $settings_help['general'];
    $tab_icons = array(
        'general'  => 'dashicons-admin-appearance',
        'mentions' => 'dashicons-format-chat',
        'fields'   => 'dashicons-list-view',
        'archives' => 'dashicons-archive',
        'updates'  => 'dashicons-update',
        'logs'     => 'dashicons-media-text',
        'health'   => 'dashicons-heart',
        'media'    => 'dashicons-format-gallery',
    );
    $partial_context = get_defined_vars();
    unset($partial_context['this']);
    ?>
    <header class="feuer-settings-header">
        <div class="feuer-settings-header-copy">
            <h1><span class="dashicons dashicons-admin-settings feuer-admin-heading-icon" aria-hidden="true"></span>Mannschaft-Einstellungen</h1>
            <p>Profile, Listen und Darstellung der Mannschaft verwalten.</p>
        </div>
        <span class="feuer-settings-status"><span aria-hidden="true"></span> Plugin aktiv</span>
    </header>
    <?php settings_errors(Feuer_Mannschaft_Plugin::OPTION_KEY); ?>
    <?php settings_errors('general'); ?>

    <?php if (! empty($notice)) : ?>
        <div class="notice notice-<?php echo esc_attr($notice['type']); ?> is-dismissible">
            <p><?php echo esc_html($notice['message']); ?></p>
        </div>
    <?php endif; ?>

    <nav class="nav-tab-wrapper feuer-mannschaft-admin-tabs" aria-label="Einstellungsbereiche">
        <?php foreach ($tabs as $tab_key => $tab_label) : ?>
            <a class="nav-tab <?php echo $tab_key === $current_tab ? 'nav-tab-active' : ''; ?>" data-admin-tab="<?php echo esc_attr($tab_key); ?>" href="<?php echo esc_url(add_query_arg(array('page' => 'feuer-mannschaft-settings', 'tab' => $tab_key), admin_url('admin.php'))); ?>">
                <span class="dashicons <?php echo esc_attr(isset($tab_icons[$tab_key]) ? $tab_icons[$tab_key] : 'dashicons-admin-generic'); ?>" aria-hidden="true"></span>
                <span><?php echo esc_html($tab_label); ?></span>
            </a>
        <?php endforeach; ?>
    </nav>

    <div class="feuer-settings-layout">
        <main class="feuer-mannschaft-settings-panel" data-settings-panel>
            <?php if (in_array($current_tab, array('general', 'mentions', 'fields'), true)) : ?>
                <form method="post" action="options.php">
                    <?php settings_fields('feuer_mannschaft_settings_group'); ?>
                    <input type="hidden" name="feuer_mannschaft_settings_tab" value="<?php echo esc_attr($current_tab); ?>">
                    <?php echo $this->render_admin_template('settings/' . $current_tab, $partial_context); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    <div class="feuer-settings-savebar">
                        <?php submit_button('Änderungen speichern', 'primary', 'submit', false); ?>
                        <span class="description">Änderungen werden nach dem Speichern übernommen.</span>
                    </div>
                </form>
            <?php else : ?>
                <?php echo $this->render_admin_template('settings/' . $current_tab, $partial_context); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            <?php endif; ?>
        </main>

        <aside class="feuer-settings-sidebar" aria-label="Hilfe und Status">
            <section class="card feuer-settings-help-card">
                <h2><span class="dashicons dashicons-editor-help feuer-admin-heading-icon" aria-hidden="true"></span><?php echo esc_html($settings_help['title']); ?></h2>
                <p><?php echo esc_html($settings_help['text']); ?></p>
                <a href="<?php echo esc_url($settings_help['link']); ?>"><?php echo esc_html($settings_help['link_label']); ?> <span aria-hidden="true">&#8599;</span></a>
            </section>
            <section class="card feuer-settings-version-card">
                <h2><span class="dashicons dashicons-yes-alt feuer-admin-heading-icon" aria-hidden="true"></span>Plugin-Status</h2>
                <p><span class="feuer-settings-status"><span aria-hidden="true"></span> Aktiv</span></p>
                <dl>
                    <div><dt>Installierte Version</dt><dd><?php echo esc_html(FEUER_MANNSCHAFT_VERSION); ?></dd></div>
                    <div><dt>Update-Status</dt><dd><?php echo ! empty($update_status['has_update']) ? esc_html__('Update verfügbar', 'feuer-mannschaft') : (! empty($update_status['remote_version']) ? esc_html__('Aktuell', 'feuer-mannschaft') : esc_html__('Noch nicht geprüft', 'feuer-mannschaft')); ?></dd></div>
                </dl>
                <a href="<?php echo esc_url(add_query_arg(array('page' => 'feuer-mannschaft-settings', 'tab' => 'updates'), admin_url('admin.php'))); ?>">Update-Einstellungen <span aria-hidden="true">&#8594;</span></a>
            </section>
        </aside>
    </div>
</div>

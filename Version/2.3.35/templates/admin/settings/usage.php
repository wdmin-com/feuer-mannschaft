<?php

if (! defined('ABSPATH')) {
    exit;
}

$usage_status = isset($usage_status) && is_array($usage_status) ? $usage_status : array();
$usage_sites  = isset($usage_sites) && is_array($usage_sites) ? $usage_sites : array('sites' => array(), 'error' => '');

$format_datetime = static function ($value) {
    $timestamp = is_string($value) ? strtotime($value) : false;

    if (! $timestamp) {
        return 'Noch nicht verfuegbar';
    }

    return wp_date('d.m.Y H:i', $timestamp);
};
?>
<form method="post" action="options.php">
    <?php settings_fields('feuer_mannschaft_settings_group'); ?>
    <input type="hidden" name="feuer_mannschaft_settings_tab" value="usage">
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row">Site-Registry aktivieren</th>
                <td>
                    <label>
                        <input type="checkbox" name="<?php echo esc_attr(Feuer_Mannschaft_Plugin::OPTION_KEY); ?>[enable_usage_registry]" value="1" <?php checked(! empty($settings['enable_usage_registry'])); ?>>
                        Installationen an die zentrale Site-Registry melden
                    </label>
                    <p class="description">Wenn aktiviert und ein Write Key hinterlegt ist, sendet das Plugin taeglich einen Heartbeat an den Registry-Endpoint auf `wdmin.com`.</p>
                </td>
            </tr>

            <tr>
                <th scope="row"><label for="feuer_mannschaft_usage_registry_url">Registry Endpoint</label></th>
                <td>
                    <input type="url" class="regular-text code" id="feuer_mannschaft_usage_registry_url" name="<?php echo esc_attr(Feuer_Mannschaft_Plugin::OPTION_KEY); ?>[usage_registry_url]" value="<?php echo esc_attr(isset($settings['usage_registry_url']) ? $settings['usage_registry_url'] : ''); ?>">
                    <p class="description">Standard: <code><?php echo esc_html(defined('FEUER_MANNSCHAFT_USAGE_REGISTRY_URL') ? FEUER_MANNSCHAFT_USAGE_REGISTRY_URL : ''); ?></code></p>
                </td>
            </tr>

            <tr>
                <th scope="row"><label for="feuer_mannschaft_usage_registry_write_key">Write Key</label></th>
                <td>
                    <input type="text" class="regular-text code" id="feuer_mannschaft_usage_registry_write_key" name="<?php echo esc_attr(Feuer_Mannschaft_Plugin::OPTION_KEY); ?>[usage_registry_write_key]" value="<?php echo esc_attr(isset($settings['usage_registry_write_key']) ? $settings['usage_registry_write_key'] : ''); ?>" autocomplete="off">
                    <p class="description">Dieser Schluessel wird an allen WordPress-Installationen eingetragen, die ihre Nutzung an `usage-registry.php` melden sollen.</p>
                </td>
            </tr>

            <tr>
                <th scope="row"><label for="feuer_mannschaft_usage_registry_read_key">Read Key</label></th>
                <td>
                    <input type="text" class="regular-text code" id="feuer_mannschaft_usage_registry_read_key" name="<?php echo esc_attr(Feuer_Mannschaft_Plugin::OPTION_KEY); ?>[usage_registry_read_key]" value="<?php echo esc_attr(isset($settings['usage_registry_read_key']) ? $settings['usage_registry_read_key'] : ''); ?>" autocomplete="off">
                    <p class="description">Nur auf der zentralen Verwaltungsseite hinterlegen. Mit diesem Schluessel wird die komplette Liste aller registrierten Websites geladen.</p>
                </td>
            </tr>
        </tbody>
    </table>
    <?php submit_button(); ?>
</form>

<div class="feuer-mannschaft-usage-panels">
    <section class="feuer-mannschaft-usage-panel">
        <div class="feuer-mannschaft-usage-panel-header">
            <div>
                <h2 class="feuer-mannschaft-field-panel-title">Aktuelle Installation</h2>
                <p class="description">Status dieser WordPress-Installation fuer die zentrale Site-Registry.</p>
            </div>
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <?php wp_nonce_field('feuer_mannschaft_usage_sync'); ?>
                <input type="hidden" name="action" value="feuer_mannschaft_usage_sync">
                <button type="submit" class="button button-secondary">Jetzt synchronisieren</button>
            </form>
        </div>

        <div class="feuer-mannschaft-usage-grid">
            <div class="feuer-mannschaft-usage-stat">
                <strong>Status</strong>
                <span><?php echo esc_html(isset($usage_status['last_status']) ? $usage_status['last_status'] : 'never_synced'); ?></span>
            </div>
            <div class="feuer-mannschaft-usage-stat">
                <strong>Letzte Synchronisierung</strong>
                <span><?php echo esc_html($format_datetime(isset($usage_status['last_synced_at']) ? $usage_status['last_synced_at'] : '')); ?></span>
            </div>
            <div class="feuer-mannschaft-usage-stat">
                <strong>Naechster Cron-Lauf</strong>
                <span><?php echo esc_html($format_datetime(isset($usage_status['next_sync_at']) ? $usage_status['next_sync_at'] : '')); ?></span>
            </div>
            <div class="feuer-mannschaft-usage-stat">
                <strong>HTTP-Code</strong>
                <span><?php echo esc_html((string) (isset($usage_status['last_http_code']) ? (int) $usage_status['last_http_code'] : 0)); ?></span>
            </div>
        </div>

        <table class="widefat striped feuer-mannschaft-usage-meta">
            <tbody>
                <tr>
                    <th>Website</th>
                    <td><a href="<?php echo esc_url(isset($usage_status['site_url']) ? $usage_status['site_url'] : home_url('/')); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html(isset($usage_status['site_name']) ? $usage_status['site_name'] : get_bloginfo('name')); ?></a></td>
                </tr>
                <tr>
                    <th>Site-URL</th>
                    <td><code><?php echo esc_html(isset($usage_status['site_url']) ? $usage_status['site_url'] : home_url('/')); ?></code></td>
                </tr>
                <tr>
                    <th>Registry Endpoint</th>
                    <td><code><?php echo esc_html(isset($usage_status['endpoint_url']) ? $usage_status['endpoint_url'] : ''); ?></code></td>
                </tr>
                <tr>
                    <th>Letzte Meldung</th>
                    <td><?php echo esc_html(isset($usage_status['last_message']) && '' !== $usage_status['last_message'] ? $usage_status['last_message'] : 'Noch keine Rueckmeldung vorhanden.'); ?></td>
                </tr>
            </tbody>
        </table>
    </section>

    <section class="feuer-mannschaft-usage-panel">
        <div class="feuer-mannschaft-usage-panel-header">
            <div>
                <h2 class="feuer-mannschaft-field-panel-title">Registrierte Websites</h2>
                <p class="description">Die Tabelle wird nur geladen, wenn auf dieser Installation ein gueltiger Read Key hinterlegt ist.</p>
            </div>
        </div>

        <?php if (! empty($usage_sites['error'])) : ?>
            <div class="notice notice-warning inline">
                <p><?php echo esc_html($usage_sites['error']); ?></p>
            </div>
        <?php endif; ?>

        <?php if (! empty($usage_sites['sites'])) : ?>
            <table class="widefat striped">
                <thead>
                    <tr>
                        <th>Website</th>
                        <th>Status</th>
                        <th>Plugin-Version</th>
                        <th>WordPress</th>
                        <th>Sprache</th>
                        <th>Zuletzt gesehen</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usage_sites['sites'] as $site) : ?>
                        <tr>
                            <td>
                                <strong><?php echo esc_html(isset($site['site_name']) ? $site['site_name'] : 'Unbenannte Website'); ?></strong><br>
                                <a href="<?php echo esc_url(isset($site['site_url']) ? $site['site_url'] : '#'); ?>" target="_blank" rel="noopener noreferrer">
                                    <?php echo esc_html(isset($site['site_url']) ? $site['site_url'] : ''); ?>
                                </a>
                            </td>
                            <td><?php echo esc_html(isset($site['status']) ? $site['status'] : 'unknown'); ?></td>
                            <td><?php echo esc_html(isset($site['plugin_version']) ? $site['plugin_version'] : ''); ?></td>
                            <td><?php echo esc_html(isset($site['wp_version']) ? $site['wp_version'] : ''); ?></td>
                            <td><?php echo esc_html(isset($site['locale']) ? $site['locale'] : ''); ?></td>
                            <td><?php echo esc_html($format_datetime(isset($site['last_seen_at']) ? $site['last_seen_at'] : '')); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php elseif (empty($usage_sites['error'])) : ?>
            <p>Es wurden noch keine Websites in der zentralen Registry gefunden.</p>
        <?php endif; ?>
    </section>
</div>

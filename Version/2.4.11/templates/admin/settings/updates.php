<?php

if (! defined('ABSPATH')) {
    exit;
}

$update_status = isset($update_status) && is_array($update_status) ? $update_status : array();
$remote_version = isset($update_status['remote_version']) ? (string) $update_status['remote_version'] : '';
$installed_version = isset($update_status['installed_version']) ? (string) $update_status['installed_version'] : FEUER_MANNSCHAFT_VERSION;
$package_url = isset($update_status['package_url']) ? (string) $update_status['package_url'] : '';
$has_update = ! empty($update_status['has_update']);
?>
<div class="feuer-mannschaft-settings-section">
    <h2>Plugin-Updates</h2>
    <p>Hier kann die Update-Pruefung manuell gestartet werden. Dabei wird der Cache geloescht und <code>update.json</code> auf <code>wdmin.com</code> neu abgefragt.</p>
    <p>Automatische Hintergrund-Updates koennen hier fuer dieses Plugin gezielt ein- oder ausgeschaltet werden. Bei Serverproblemen mit Dateibesitzern sollte diese Option deaktiviert bleiben.</p>

    <form method="post" action="options.php" class="feuer-mannschaft-update-settings-form">
        <?php settings_fields('feuer_mannschaft_settings_group'); ?>
        <input type="hidden" name="feuer_mannschaft_settings_tab" value="updates">
        <table class="form-table" role="presentation">
            <tbody>
                <tr>
                    <th scope="row">Automatische Updates</th>
                    <td>
                        <label class="feuer-mannschaft-inline-check">
                            <input type="checkbox" name="<?php echo esc_attr(Feuer_Mannschaft_Plugin::OPTION_KEY); ?>[enable_automatic_updates]" value="1" <?php checked(! empty($settings['enable_automatic_updates'])); ?>>
                            Automatische WordPress-Hintergrund-Updates fuer Mannschaft erlauben
                        </label>
                        <p class="description">Ausgeschaltet bedeutet: Updates werden angezeigt, aber nur manuell aus der Plugin-Uebersicht installiert.</p>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php submit_button('Update-Einstellung speichern', 'secondary', 'submit', false); ?>
    </form>

    <table class="widefat striped" role="presentation">
        <tbody>
            <tr>
                <th scope="row">Installierte Version</th>
                <td><?php echo esc_html($installed_version); ?></td>
            </tr>
            <tr>
                <th scope="row">Version auf dem Update-Server</th>
                <td><?php echo $remote_version !== '' ? esc_html($remote_version) : 'Nicht erreichbar'; ?></td>
            </tr>
            <tr>
                <th scope="row">Status</th>
                <td>
                    <?php if ($has_update) : ?>
                        <span class="feuer-mannschaft-badge is-warning">Update verfuegbar</span>
                    <?php elseif ($remote_version !== '') : ?>
                        <span class="feuer-mannschaft-badge is-ok">Aktuell</span>
                    <?php else : ?>
                        <span class="feuer-mannschaft-badge is-muted">Keine Serverantwort</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php if ($package_url !== '') : ?>
                <tr>
                    <th scope="row">Download-Paket</th>
                    <td><a href="<?php echo esc_url($package_url); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html($package_url); ?></a></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" class="feuer-mannschaft-update-check-form">
        <input type="hidden" name="action" value="feuer_mannschaft_check_updates">
        <?php wp_nonce_field('feuer_mannschaft_check_updates'); ?>
        <?php submit_button('Update jetzt pruefen', 'primary', 'submit', false); ?>
    </form>
</div>

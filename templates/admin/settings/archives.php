<?php

if (! defined('ABSPATH')) {
    exit;
}
?>
<div class="feuer-mannschaft-settings-section">
    <h2 class="feuer-admin-icon-heading"><span class="dashicons dashicons-archive feuer-admin-section-icon" aria-hidden="true"></span>Datenarchiv</h2>
    <p>Hier können Sie pluginbezogene Daten sichern und wiederherstellen. Gesichert werden Einstellungen, Teilnehmer, Galerie-Dateien, Erwähnungs-Verknüpfungen und Protokolle.</p>

    <div class="feuer-mannschaft-settings-actions">
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <input type="hidden" name="action" value="feuer_mannschaft_create_backup">
            <?php wp_nonce_field('feuer_mannschaft_create_backup'); ?>
            <?php submit_button('Archiv erstellen', 'primary', 'submit', false); ?>
        </form>

        <form method="post" enctype="multipart/form-data" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <input type="hidden" name="action" value="feuer_mannschaft_upload_backup">
            <?php wp_nonce_field('feuer_mannschaft_upload_backup'); ?>
            <input type="file" name="feuer_mannschaft_backup_upload" accept=".zip" required>
            <?php submit_button('Archiv hochladen', 'secondary', 'submit', false); ?>
        </form>
    </div>

    <?php if (! empty($preview_archive)) : ?>
        <div class="notice <?php echo empty($preview_archive['error']) ? 'notice-info' : 'notice-error'; ?>">
            <?php if (! empty($preview_archive['error'])) : ?>
                <p><?php echo esc_html($preview_archive['error']); ?></p>
            <?php else : ?>
                <p><strong><?php echo esc_html(sprintf('Vorschau: %s', $preview_archive['name'])); ?></strong></p>
                <p><?php echo esc_html(sprintf('%1$d Profile, %2$d Fotos, %3$d interne Nachweise, %4$d Erwähnungs-Datensätze, %5$d Logeinträge; Einstellungen: %6$s', (int) $preview_archive['members'], (int) $preview_archive['photos'], (int) ($preview_archive['private_documents'] ?? 0), (int) $preview_archive['mentions'], (int) $preview_archive['logs'], ! empty($preview_archive['settings']) ? 'enthalten' : 'nicht enthalten')); ?></p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if (empty($archives)) : ?>
        <div class="feuer-mannschaft-empty-state">
            <strong>Noch kein Archiv vorhanden</strong>
            <p>Erstellen Sie vor groesseren Aenderungen ein Backup, damit Profile, Bilder, Einstellungen und Logs wiederhergestellt werden koennen.</p>
        </div>
    <?php else : ?>
        <table class="widefat striped">
            <thead>
                <tr>
                    <th>Datei</th>
                    <th>Geändert</th>
                    <th>Größe</th>
                    <th>Aktionen</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($archives as $archive) : ?>
                    <tr>
                        <td><code><?php echo esc_html($archive['name']); ?></code></td>
                        <td><?php echo esc_html($archive['modified_human']); ?></td>
                        <td><span class="feuer-mannschaft-badge is-muted"><?php echo esc_html($archive['size']); ?></span></td>
                        <td class="feuer-mannschaft-archive-actions">
                            <a class="button button-secondary" href="<?php echo esc_url(wp_nonce_url(add_query_arg(array('action' => 'feuer_mannschaft_download_backup', 'archive' => $archive['name']), admin_url('admin-post.php')), 'feuer_mannschaft_download_backup')); ?>">
                                Download
                            </a>
                            <a class="button button-secondary" href="<?php echo esc_url(add_query_arg(array('page' => 'feuer-mannschaft-settings', 'tab' => 'archives', 'preview_archive' => $archive['name']), admin_url('admin.php'))); ?>">
                                Vorschau / prüfen
                            </a>
                            <details class="feuer-mannschaft-restore-options">
                                <summary><?php echo esc_html__('Wiederherstellen', 'feuer-mannschaft'); ?></summary>
                                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" onsubmit="return confirm('<?php echo esc_js(__('Ausgewählte Archivdaten wiederherstellen? Vorher wird ein Sicherheitsarchiv erstellt.', 'feuer-mannschaft')); ?>');">
                                    <input type="hidden" name="action" value="feuer_mannschaft_restore_backup">
                                    <input type="hidden" name="archive" value="<?php echo esc_attr($archive['name']); ?>">
                                    <?php wp_nonce_field('feuer_mannschaft_restore_backup'); ?>
                                    <fieldset>
                                        <legend><?php echo esc_html__('Zu importierende Bereiche', 'feuer-mannschaft'); ?></legend>
                                        <label><input type="checkbox" name="components[]" value="members" checked> <?php echo esc_html__('Profile', 'feuer-mannschaft'); ?></label><br>
                                        <label><input type="checkbox" name="components[]" value="gallery" checked> <?php echo esc_html__('Fotos', 'feuer-mannschaft'); ?></label><br>
                                        <label><input type="checkbox" name="components[]" value="private_documents" checked> <?php echo esc_html__('Interne Nachweise', 'feuer-mannschaft'); ?></label><br>
                                        <label><input type="checkbox" name="components[]" value="mentions" checked> <?php echo esc_html__('Erwähnungen', 'feuer-mannschaft'); ?></label><br>
                                        <label><input type="checkbox" name="components[]" value="settings" checked> <?php echo esc_html__('Einstellungen', 'feuer-mannschaft'); ?></label><br>
                                        <label><input type="checkbox" name="components[]" value="logs" checked> <?php echo esc_html__('Protokoll', 'feuer-mannschaft'); ?></label>
                                    </fieldset>
                                    <?php submit_button(__('Auswahl wiederherstellen', 'feuer-mannschaft'), 'secondary', 'submit', false); ?>
                                </form>
                            </details>
                            <a class="button button-link-delete" href="<?php echo esc_url(wp_nonce_url(add_query_arg(array('action' => 'feuer_mannschaft_delete_backup', 'archive' => $archive['name']), admin_url('admin-post.php')), 'feuer_mannschaft_delete_backup')); ?>" onclick="return confirm('<?php echo esc_js(__('Archiv wirklich löschen?', 'feuer-mannschaft')); ?>');">
                                Löschen
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

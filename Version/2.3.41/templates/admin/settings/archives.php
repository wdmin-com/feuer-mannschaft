<?php

if (! defined('ABSPATH')) {
    exit;
}
?>
<div class="feuer-mannschaft-settings-section">
    <h2>Datenarchiv</h2>
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

    <?php if (empty($archives)) : ?>
        <p>Es sind noch keine Archive vorhanden.</p>
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
                        <td><?php echo esc_html($archive['size']); ?></td>
                        <td class="feuer-mannschaft-archive-actions">
                            <a class="button button-secondary" href="<?php echo esc_url(wp_nonce_url(add_query_arg(array('action' => 'feuer_mannschaft_download_backup', 'archive' => $archive['name']), admin_url('admin-post.php')), 'feuer_mannschaft_download_backup')); ?>">
                                Download
                            </a>
                            <a class="button button-secondary" href="<?php echo esc_url(wp_nonce_url(add_query_arg(array('action' => 'feuer_mannschaft_restore_backup', 'archive' => $archive['name']), admin_url('admin-post.php')), 'feuer_mannschaft_restore_backup')); ?>" onclick="return confirm('<?php echo esc_js(__('Archiv wirklich wiederherstellen?', 'feuer-mannschaft')); ?>');">
                                Wiederherstellen
                            </a>
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

<?php

if (! defined('ABSPATH')) {
    exit;
}
?>
<section class="feuer-mannschaft-settings-section" aria-labelledby="feuer-mannschaft-media-title">
    <h2 id="feuer-mannschaft-media-title">Nicht verwendete Mannschaftsfotos</h2>
    <p>Aufgeführt werden nur Dateien im plugin-eigenen Upload-Ordner, die nicht mit einem Profil, Beitragsbild oder gefundenen Beitragsinhalt verknüpft sind. Das Löschen ist endgültig.</p>

    <?php if (empty($orphan_media)) : ?>
        <p><?php echo esc_html__('Keine ungenutzten Mannschaftsfotos gefunden.', 'feuer-mannschaft'); ?></p>
    <?php else : ?>
        <table class="widefat striped">
            <thead><tr><th scope="col">Vorschau</th><th scope="col">Datei</th><th scope="col">Aktion</th></tr></thead>
            <tbody>
                <?php foreach ($orphan_media as $attachment) : ?>
                    <tr>
                        <td><?php echo wp_kses_post(wp_get_attachment_image($attachment->ID, array(72, 72), false)); ?></td>
                        <td><?php echo esc_html(get_the_title($attachment)); ?><br><code><?php echo esc_html(get_post_meta($attachment->ID, '_wp_attached_file', true)); ?></code></td>
                        <td>
                            <a class="button button-link-delete" href="<?php echo esc_url(wp_nonce_url(add_query_arg(array('action' => 'feuer_mannschaft_cleanup_orphan_media', 'attachment' => $attachment->ID), admin_url('admin-post.php')), 'feuer_mannschaft_cleanup_orphan_media_' . $attachment->ID)); ?>" onclick="return confirm('<?php echo esc_js(__('Datei endgültig aus der Mediathek löschen?', 'feuer-mannschaft')); ?>');">
                                <?php echo esc_html__('Endgültig löschen', 'feuer-mannschaft'); ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>

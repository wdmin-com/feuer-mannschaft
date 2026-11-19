<?php

if (! defined('ABSPATH')) {
    exit;
}
?>
<section class="feuer-mannschaft-settings-section" aria-labelledby="feuer-mannschaft-media-title">
    <div class="feuer-mannschaft-media-heading">
        <div>
            <h2 id="feuer-mannschaft-media-title" class="feuer-admin-icon-heading"><span class="dashicons dashicons-format-gallery feuer-admin-section-icon" aria-hidden="true"></span>Mitgliederfotos</h2>
            <p>Jedes Foto öffnet die Bearbeitung des zugehörigen Teilnehmerprofils.</p>
        </div>
        <span class="feuer-mannschaft-media-count"><?php echo esc_html(sprintf('%d Fotos', count($member_media))); ?></span>
    </div>

    <?php if (empty($member_media)) : ?>
        <p class="feuer-mannschaft-media-empty">Es sind noch keine eigenen Teilnehmerfotos hinterlegt.</p>
    <?php else : ?>
        <div class="feuer-mannschaft-media-grid">
            <?php foreach ($member_media as $item) : ?>
                <?php
                $alt_text = $item['attachment_title'] ? $item['attachment_title'] : sprintf('Foto von %s', $item['member_name']);
                $edit_url = $item['edit_url'] ? $item['edit_url'] : admin_url('post.php?post=' . absint($item['member_id']) . '&action=edit');
                ?>
                <article class="feuer-mannschaft-media-card">
                    <a class="feuer-mannschaft-media-card-image" href="<?php echo esc_url($edit_url); ?>" aria-label="<?php echo esc_attr(sprintf('Profil von %s bearbeiten', $item['member_name'])); ?>">
                        <?php echo wp_get_attachment_image($item['attachment_id'], 'medium', false, array('alt' => $alt_text, 'loading' => 'lazy')); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    </a>
                    <div class="feuer-mannschaft-media-card-caption">
                        <a href="<?php echo esc_url($edit_url); ?>" class="feuer-mannschaft-media-card-name"><?php echo esc_html($item['member_name']); ?></a>
                        <span><?php echo esc_html($item['attachment_title'] ? $item['attachment_title'] : 'Teilnehmerfoto'); ?></span>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<section class="feuer-mannschaft-settings-section feuer-mannschaft-orphan-media-section" aria-labelledby="feuer-mannschaft-orphan-media-title">
    <h2 id="feuer-mannschaft-orphan-media-title" class="feuer-admin-icon-heading"><span class="dashicons dashicons-trash feuer-admin-section-icon" aria-hidden="true"></span>Nicht verwendete Mannschaftsfotos</h2>
    <p>Hier erscheinen nur Dateien im Plugin-Upload-Ordner, die keinem Profil oder gefundenen Inhalt zugeordnet sind. Das Löschen ist endgültig.</p>

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

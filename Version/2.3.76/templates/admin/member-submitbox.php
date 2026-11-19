<?php

if (! defined('ABSPATH')) {
    exit;
}
?>
<div class="misc-pub-section feuer-mannschaft-status-box">
    <strong><?php echo esc_html($label); ?></strong><br>
    <a class="button button-secondary" href="<?php echo esc_url($button_url); ?>">
        <?php echo esc_html($button_text); ?>
    </a>

    <?php if ($can_remove) : ?>
        <p class="description">
            Nur Hauptadministration darf Profile endgültig entfernen.
        </p>
        <a class="button-link-delete" href="<?php echo esc_url($delete_url); ?>" onclick="return confirm('<?php echo esc_js(__('Dieses Profil endgültig entfernen?', 'feuer-mannschaft')); ?>');">
            <?php echo esc_html__('Profil endgültig entfernen', 'feuer-mannschaft'); ?>
        </a>
    <?php else : ?>
        <p class="description">
            Profile werden standardmäßig archiviert und nicht sofort gelöscht.
        </p>
    <?php endif; ?>
</div>

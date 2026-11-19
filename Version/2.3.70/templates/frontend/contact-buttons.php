<?php

if (! defined('ABSPATH')) {
    exit;
}

$buttons = isset($buttons) && is_array($buttons) ? $buttons : array();

if (empty($buttons)) {
    return;
}
?>
<div class="feuer-mannschaft-contact-actions">
    <div class="feuer-mannschaft-contact-actions-label">Kontakt</div>
    <div class="feuer-mannschaft-contact-actions-buttons">
        <?php foreach ($buttons as $button) : ?>
            <?php
            $url = isset($button['url']) ? (string) $button['url'] : '';
            if ('' === $url) {
                continue;
            }
            ?>
            <a
                class="btn btn-primary btn-sm feuer-mannschaft-contact-button feuer-mannschaft-contact-button-<?php echo esc_attr(isset($button['type']) ? $button['type'] : 'contact'); ?>"
                href="<?php echo esc_url($url); ?>"
                <?php if (! empty($button['target'])) : ?>
                    target="<?php echo esc_attr($button['target']); ?>"
                <?php endif; ?>
                <?php if (! empty($button['rel'])) : ?>
                    rel="<?php echo esc_attr($button['rel']); ?>"
                <?php endif; ?>
            >
                <?php echo esc_html(isset($button['label']) ? $button['label'] : 'Kontakt'); ?>
            </a>
        <?php endforeach; ?>
    </div>
</div>

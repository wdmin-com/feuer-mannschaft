<?php

if (! defined('ABSPATH')) {
    exit;
}
?>
<div class="feuer-mannschaft-mention-helper" data-feuer-mannschaft-mention-helper>
    <p class="description">Zu jeder ausgewählten Person wird hier ein Kurzcode erzeugt. Der Kurzcode zeigt den Namen an und verlinkt direkt auf die Teilnehmerakte.</p>

    <?php if (empty($items)) : ?>
        <p class="feuer-mannschaft-mention-helper-empty"><?php echo esc_html($empty_message); ?></p>
        <div class="feuer-mannschaft-mention-shortcode-list" hidden></div>
    <?php else : ?>
        <p class="feuer-mannschaft-mention-helper-empty" hidden><?php echo esc_html($empty_message); ?></p>
        <div class="feuer-mannschaft-mention-shortcode-list">
            <?php foreach ($items as $item) : ?>
                <article class="feuer-mannschaft-mention-shortcode-item">
                    <div class="feuer-mannschaft-mention-shortcode-top">
                        <strong><?php echo esc_html($item['name']); ?></strong>
                        <a href="<?php echo esc_url($item['url']); ?>" target="_blank" rel="noopener noreferrer">
                            <?php echo esc_html__('Profil öffnen', 'feuer-mannschaft'); ?>
                        </a>
                    </div>

                    <div class="feuer-mannschaft-mention-shortcode-preview">
                        <span class="feuer-mannschaft-mention-shortcode-label"><?php echo esc_html__('Vorschau', 'feuer-mannschaft'); ?>:</span>
                        <a href="<?php echo esc_url($item['url']); ?>" target="_blank" rel="noopener noreferrer">
                            <?php echo esc_html($item['name']); ?>
                        </a>
                    </div>

                    <label class="feuer-mannschaft-mention-shortcode-label" for="<?php echo esc_attr('feuer-mannschaft-mention-shortcode-' . $item['id']); ?>">
                        <?php echo esc_html__('Kurzcode', 'feuer-mannschaft'); ?>
                    </label>

                    <div class="feuer-mannschaft-mention-shortcode-actions">
                        <input
                            type="text"
                            readonly
                            id="<?php echo esc_attr('feuer-mannschaft-mention-shortcode-' . $item['id']); ?>"
                            class="widefat"
                            value="<?php echo esc_attr($item['shortcode']); ?>"
                        >
                        <button type="button" class="button button-secondary" data-feuer-mannschaft-copy-shortcode>
                            <?php echo esc_html__('Kopieren', 'feuer-mannschaft'); ?>
                        </button>
                        <button type="button" class="button button-secondary" data-feuer-mannschaft-insert-shortcode>
                            <?php echo esc_html__('Einfügen', 'feuer-mannschaft'); ?>
                        </button>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

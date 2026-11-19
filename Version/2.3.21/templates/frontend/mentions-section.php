<?php

if (! defined('ABSPATH')) {
    exit;
}
?>
<section class="feuer-mannschaft-mentions-section mb-4">
    <div class="feuer-mannschaft-detail-box">
        <div class="d-flex flex-wrap justify-content-between gap-3 align-items-start mb-3">
            <div>
                <div class="feuer-mannschaft-detail-label"><?php echo esc_html__('Erwähnungen', 'feuer-mannschaft'); ?></div>
                <h3 class="h5 mb-0"><?php echo esc_html(sprintf(__('Gesamtzahl Erwähnungen: %d', 'feuer-mannschaft'), (int) $count)); ?></h3>
            </div>
        </div>

        <div class="feuer-mannschaft-mentions-list">
            <?php foreach ($items as $item) : ?>
                <article class="feuer-mannschaft-mention-item">
                    <a href="<?php echo esc_url($item['url']); ?>" class="feuer-mannschaft-mention-link">
                        <?php echo esc_html($item['title']); ?>
                    </a>
                    <div class="feuer-mannschaft-mention-meta">
                        <span><?php echo esc_html($item['date']); ?></span>
                        <span><?php echo esc_html($item['post_type_label']); ?></span>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

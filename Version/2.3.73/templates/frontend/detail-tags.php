<?php
$items = isset($items) && is_array($items) ? $items : array();
?>
<span class="feuer-mannschaft-detail-tags">
    <?php foreach ($items as $item) : ?>
        <?php $url = isset($item['url']) ? (string) $item['url'] : ''; ?>
        <?php if ($url) : ?>
            <a class="feuer-mannschaft-detail-tag feuer-mannschaft-detail-link" href="<?php echo esc_url($url); ?>">
                <?php echo esc_html(isset($item['label']) ? $item['label'] : ''); ?>
            </a>
        <?php else : ?>
            <span class="feuer-mannschaft-detail-tag"><?php echo esc_html(isset($item['label']) ? $item['label'] : ''); ?></span>
        <?php endif; ?>
    <?php endforeach; ?>
</span>

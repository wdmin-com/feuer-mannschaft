<?php $url = isset($url) ? (string) $url : ''; ?>
<?php if ($url) : ?>
    <a href="<?php echo esc_url($url); ?>"<?php echo '' !== $class_name ? ' class="' . esc_attr(trim($class_name . ' feuer-mannschaft-detail-link')) . '"' : ' class="feuer-mannschaft-detail-link"'; ?>>
        <?php echo esc_html($value); ?>
    </a>
<?php else : ?>
    <span<?php echo '' !== $class_name ? ' class="' . esc_attr($class_name) . '"' : ''; ?>>
        <?php echo esc_html($value); ?>
    </span>
<?php endif; ?>

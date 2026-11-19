<a
    href="<?php echo esc_url($url); ?>"
    <?php echo '' !== $class_name ? 'class="' . esc_attr($class_name) . '"' : ''; ?>
    <?php echo '' !== $confirm_message ? 'onclick="return confirm(\'' . esc_js($confirm_message) . '\');"' : ''; ?>
>
    <?php echo esc_html($label); ?>
</a>

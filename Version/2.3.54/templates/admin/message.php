<?php

if (! defined('ABSPATH')) {
    exit;
}
?>
<p<?php echo ! empty($class_name) ? ' class="' . esc_attr($class_name) . '"' : ''; ?>><?php echo esc_html($message); ?></p>

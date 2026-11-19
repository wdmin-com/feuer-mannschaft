<?php

if (! defined('ABSPATH')) {
    exit;
}

get_header();

if (have_posts()) {
    while (have_posts()) {
        the_post();
        ?>
        <main class="feuer-mannschaft-shortcode-profile-page">
            <?php echo apply_filters('the_content', get_the_content()); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        </main>
        <?php
    }
}

get_footer();

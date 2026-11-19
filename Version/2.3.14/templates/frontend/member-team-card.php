<?php

if (! defined('ABSPATH')) {
    exit;
}
?>
<article class="feuer-mannschaft-team-card text-center mx-auto">
    <a href="<?php echo esc_url($profile_url); ?>" class="d-block mb-4 js-feuer-mannschaft-profile-link" aria-label="<?php echo esc_attr($name); ?>">
        <?php echo $this->get_member_visual_markup($member_id, 'medium_large', 'feuer-mannschaft-team-image'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
    </a>
    <h2 class="h5 mb-1">
        <a href="<?php echo esc_url($profile_url); ?>" class="text-decoration-none text-dark js-feuer-mannschaft-profile-link">
            <?php echo esc_html($name); ?>
        </a>
    </h2>
    <?php if ($role_caption) : ?>
        <span class="d-block text-muted"><?php echo esc_html($role_caption); ?></span>
    <?php endif; ?>
</article>

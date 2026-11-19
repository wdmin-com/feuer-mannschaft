<?php

if (! defined('ABSPATH')) {
    exit;
}
?>
<article class="card border-0 overflow-hidden h-100 feuer-mannschaft-member-card feuer-mannschaft-team-card">
    <a href="<?php echo esc_url($profile_url); ?>" class="d-block h-100 text-decoration-none text-white feuer-mannschaft-member-card-link js-feuer-mannschaft-profile-link" aria-label="<?php echo esc_attr(sprintf('Profil von %s ansehen', $name)); ?>">
        <span class="feuer-mannschaft-member-card-media">
            <?php echo $this->get_member_visual_markup($member_id, 'medium_large', 'feuer-mannschaft-member-card-image'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        </span>
        <span class="card-img-overlay d-flex flex-column justify-content-end feuer-mannschaft-member-card-overlay">
            <?php if ($rank_caption) : ?>
                <span class="feuer-mannschaft-member-card-rank"><?php echo esc_html($rank_caption); ?></span>
            <?php endif; ?>
            <span class="feuer-mannschaft-member-card-name<?php echo ! empty($title_class) ? ' ' . esc_attr($title_class) : ''; ?>"<?php echo ! empty($title_style) ? ' style="' . esc_attr($title_style) . '"' : ''; ?>><?php echo esc_html($name); ?></span>
            <?php if ($role_caption) : ?>
                <span class="feuer-mannschaft-member-card-role"><?php echo esc_html($role_caption); ?></span>
            <?php endif; ?>
        </span>
    </a>
</article>

<?php

if (! defined('ABSPATH')) {
    exit;
}
?>
<div class="col">
    <article class="card h-100 border-0 shadow-sm">
        <a href="<?php echo esc_url($profile_url); ?>" class="d-block js-feuer-mannschaft-profile-link">
            <?php echo $this->get_member_visual_markup($member_id, 'large', 'card-img-top'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        </a>
        <div class="card-body d-flex flex-column">
            <?php if ($role_caption) : ?>
                <div class="text-muted small text-uppercase mb-2"><?php echo esc_html($role_caption); ?></div>
            <?php endif; ?>

            <h2 class="h4 mb-2">
                <a href="<?php echo esc_url($profile_url); ?>" class="text-decoration-none text-dark js-feuer-mannschaft-profile-link<?php echo ! empty($title_class) ? ' ' . esc_attr($title_class) : ''; ?>">
                    <?php echo esc_html($name); ?>
                </a>
            </h2>

            <?php if (! empty($info_rows)) : ?>
                <p class="text-muted mb-3"><?php echo esc_html(implode(' | ', $info_rows)); ?></p>
            <?php endif; ?>

            <?php if ($summary) : ?>
                <p class="mb-4"><?php echo esc_html($summary); ?></p>
            <?php endif; ?>

            <div class="mt-auto">
                <a class="btn btn-outline-secondary js-feuer-mannschaft-profile-link" href="<?php echo esc_url($profile_url); ?>">Profil ansehen</a>
            </div>
        </div>
    </article>
</div>

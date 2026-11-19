<?php

if (! defined('ABSPATH')) {
    exit;
}
?>
<article class="feuer-mannschaft-list-row border-bottom py-3">
    <div class="row align-items-center g-3">
        <div class="col-4 col-sm-3 col-md-2">
            <a href="<?php echo esc_url($profile_url); ?>" class="feuer-mannschaft-list-photo js-feuer-mannschaft-profile-link">
                <?php echo $this->get_member_visual_markup($post_id, 'medium', 'feuer-mannschaft-list-photo-image'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            </a>
        </div>
        <div class="col-8 col-sm-6 col-md-7">
            <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                <?php if ($role_caption) : ?>
                    <span class="badge text-bg-secondary"><?php echo esc_html($role_caption); ?></span>
                <?php endif; ?>
            </div>
            <h2 class="h5 mb-1">
                <a class="text-decoration-none text-dark js-feuer-mannschaft-profile-link" href="<?php echo esc_url($profile_url); ?>">
                    <?php echo esc_html($name); ?>
                </a>
            </h2>
            <?php if (! empty($detail_rows)) : ?>
                <div class="small text-muted mb-1">
                    <?php echo esc_html(implode(' | ', wp_list_pluck($detail_rows, 'value'))); ?>
                </div>
            <?php endif; ?>
            <?php if ($description) : ?>
                <p class="mb-0 text-muted"><?php echo esc_html($description); ?></p>
            <?php endif; ?>
        </div>
        <div class="col-12 col-sm-3 col-md-3 text-sm-end">
            <a class="btn btn-outline-secondary btn-sm js-feuer-mannschaft-profile-link" href="<?php echo esc_url($profile_url); ?>">Profil ansehen</a>
        </div>
    </div>
</article>

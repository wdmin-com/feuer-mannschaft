<?php

if (! defined('ABSPATH')) {
    exit;
}
?>
<article class="feuer-mannschaft-list-row border-bottom py-3">
    <div class="feuer-mannschaft-list-layout">
        <div class="feuer-mannschaft-list-visual">
            <a href="<?php echo esc_url($profile_url); ?>" class="feuer-mannschaft-list-photo js-feuer-mannschaft-profile-link">
                <?php echo $this->get_member_visual_markup($post_id, 'medium', 'feuer-mannschaft-list-photo-image'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            </a>
        </div>
        <div class="feuer-mannschaft-list-main">
            <div class="feuer-mannschaft-list-topline">
                <?php if ($role_caption) : ?>
                    <span class="badge text-bg-secondary"><?php echo esc_html($role_caption); ?></span>
                <?php endif; ?>
            </div>
            <h2 class="h5 mb-1 feuer-mannschaft-list-title">
                <a class="text-decoration-none text-dark js-feuer-mannschaft-profile-link" href="<?php echo esc_url($profile_url); ?>">
                    <?php echo esc_html($name); ?>
                </a>
            </h2>
            <?php if (! empty($detail_rows)) : ?>
                <div class="feuer-mannschaft-list-meta" aria-label="Profilinformationen">
                    <?php foreach ($detail_rows as $detail) : ?>
                        <?php if (empty($detail['value'])) : ?>
                            <?php continue; ?>
                        <?php endif; ?>
                        <span class="feuer-mannschaft-list-meta-item">
                            <strong><?php echo esc_html($detail['label']); ?>:</strong>
                            <?php echo esc_html($detail['value']); ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <?php if ($description) : ?>
                <p class="mb-0 text-muted feuer-mannschaft-list-description"><?php echo esc_html($description); ?></p>
            <?php endif; ?>
        </div>
        <div class="feuer-mannschaft-list-actions">
            <a class="btn btn-outline-secondary btn-sm js-feuer-mannschaft-profile-link" href="<?php echo esc_url($profile_url); ?>">Profil ansehen</a>
        </div>
    </div>
</article>

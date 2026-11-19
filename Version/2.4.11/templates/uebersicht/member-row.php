<?php

if (! defined('ABSPATH')) {
    exit;
}
?>
<article class="feuer-mannschaft-list-row">
    <div class="feuer-mannschaft-list-layout">
        <div class="feuer-mannschaft-list-visual">
            <a href="<?php echo esc_url($profile_url); ?>" class="feuer-mannschaft-list-photo js-feuer-mannschaft-profile-link">
                <?php echo $this->get_member_visual_markup($post_id, 'medium', 'feuer-mannschaft-list-photo-image'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            </a>
        </div>
        <div class="feuer-mannschaft-list-main">
            <div class="feuer-mannschaft-list-topline">
                <?php if ($role_caption) : ?>
                    <span class="feuer-mannschaft-list-role"><?php echo esc_html($role_caption); ?></span>
                <?php endif; ?>
            </div>
            <h2 class="feuer-mannschaft-list-title<?php echo ! empty($title_class) ? ' ' . esc_attr($title_class) : ''; ?>"<?php echo ! empty($title_style) ? ' style="' . esc_attr($title_style) . '"' : ''; ?>>
                <a class="feuer-mannschaft-list-title-link js-feuer-mannschaft-profile-link<?php echo ! empty($title_class) ? ' ' . esc_attr($title_class) : ''; ?>" href="<?php echo esc_url($profile_url); ?>"<?php echo ! empty($title_style) ? ' style="' . esc_attr($title_style) . '"' : ''; ?>>
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
                <p class="feuer-mannschaft-list-description"><?php echo esc_html($description); ?></p>
            <?php endif; ?>
        </div>
        <div class="feuer-mannschaft-list-actions">
            <a class="feuer-mannschaft-list-button js-feuer-mannschaft-profile-link" href="<?php echo esc_url($profile_url); ?>">Profil ansehen</a>
        </div>
    </div>
</article>

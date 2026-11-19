<?php

if (! defined('ABSPATH')) {
    exit;
}

$is_modal_card    = 'modal' === $display_mode;
$modal_target_id  = $modal_id;
$modal_title_id   = $modal_id . '-title';
$card_link_url    = $is_modal_card ? '#' . $modal_target_id : $profile_url;
$card_link_class  = $is_modal_card
    ? 'd-block text-decoration-none feuer-mannschaft-modal-trigger'
    : 'd-block text-decoration-none js-feuer-mannschaft-profile-link';
?>
<article class="card border-0 overflow-hidden h-100 feuer-mannschaft-member-card eb-overview-card">
    <a
        href="<?php echo esc_url($card_link_url); ?>"
        class="d-block h-100 text-decoration-none text-white feuer-mannschaft-member-card-link <?php echo esc_attr($card_link_class); ?>"
        aria-label="<?php echo esc_attr(sprintf('Profil von %s ansehen', $name)); ?>"
        <?php if ($is_modal_card) : ?>
            data-feuer-mannschaft-modal-target="<?php echo esc_attr($modal_target_id); ?>"
            aria-haspopup="dialog"
            aria-controls="<?php echo esc_attr($modal_target_id); ?>"
        <?php endif; ?>
    >
        <span class="feuer-mannschaft-member-card-media">
            <?php echo $this->get_member_visual_markup($post_id, 'large', 'feuer-mannschaft-member-card-image'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        </span>
        <span class="card-img-overlay d-flex flex-column justify-content-end feuer-mannschaft-member-card-overlay">
            <?php if (! empty($meta['rank'])) : ?>
                <span class="feuer-mannschaft-member-card-rank"><?php echo esc_html($meta['rank']); ?></span>
            <?php endif; ?>
            <span class="feuer-mannschaft-member-card-name<?php echo ! empty($title_class) ? ' ' . esc_attr($title_class) : ''; ?>"<?php echo ! empty($title_style) ? ' style="' . esc_attr($title_style) . '"' : ''; ?>><?php echo esc_html($name); ?></span>
            <?php if ($function) : ?>
                <span class="feuer-mannschaft-member-card-role"><?php echo esc_html($function); ?></span>
            <?php endif; ?>
        </span>
    </a>
</article>

<?php if ($is_modal_card) : ?>
    <div class="modal feuer-mannschaft-modal" id="<?php echo esc_attr($modal_target_id); ?>" tabindex="-1" role="dialog" aria-modal="true" aria-hidden="true" aria-labelledby="<?php echo esc_attr($modal_title_id); ?>" hidden data-feuer-mannschaft-modal>
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0">
                <div class="modal-body p-0">
                    <div class="row g-0">
                        <div class="col-md-5">
                            <div class="feuer-mannschaft-modal-image">
                                <?php echo $this->get_member_visual_markup($post_id, 'large', 'feuer-mannschaft-modal-photo'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="p-4">
                                <button type="button" class="btn-close float-end" data-feuer-mannschaft-modal-close aria-label="Schliessen"></button>
                                <h3 class="h2 mb-2" id="<?php echo esc_attr($modal_title_id); ?>"><?php echo esc_html($name); ?></h3>
                                <?php if ($meta['rank']) : ?>
                                    <p class="text-muted mb-3"><?php echo esc_html($meta['rank']); ?></p>
                                <?php endif; ?>
                                <div class="row g-3 my-3">
                                    <?php foreach ($detail_rows as $detail) : ?>
                                        <div class="col-sm-6">
                                            <div class="feuer-mannschaft-detail-box h-100">
                                                <div class="feuer-mannschaft-detail-label"><?php echo esc_html($detail['label']); ?></div>
                                                <div class="feuer-mannschaft-detail-value"><?php echo $this->get_detail_value_markup($detail); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php if ($description) : ?>
                                    <p class="mb-3"><?php echo esc_html($description); ?></p>
                                <?php endif; ?>
                                <a class="btn btn-primary js-feuer-mannschaft-profile-link" href="<?php echo esc_url($profile_url); ?>">Profil oeffnen</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

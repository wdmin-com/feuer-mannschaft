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
$title_link_class = $is_modal_card
    ? 'text-decoration-none text-dark feuer-mannschaft-modal-trigger'
    : 'text-decoration-none text-dark js-feuer-mannschaft-profile-link';
$title_link_class .= ! empty($title_class) ? ' ' . $title_class : '';
?>
<article class="card text-center border-0 shadow-sm h-100 eb-overview-card">
    <div class="card-body d-flex flex-column">
        <div class="mb-4 mx-auto w-100">
            <a
                href="<?php echo esc_url($card_link_url); ?>"
                class="<?php echo esc_attr($card_link_class); ?>"
                aria-label="<?php echo esc_attr($name); ?>"
                <?php if ($is_modal_card) : ?>
                    data-feuer-mannschaft-modal-target="<?php echo esc_attr($modal_target_id); ?>"
                    aria-haspopup="dialog"
                    aria-controls="<?php echo esc_attr($modal_target_id); ?>"
                <?php endif; ?>
            >
                <div class="feuer-mannschaft-card-image mx-auto overflow-hidden">
                    <?php
                    echo $this->get_member_visual_markup(
                        $post_id,
                        'large',
                        'img-fluid w-100 h-100 object-fit-cover'
                    ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    ?>
                </div>
            </a>
        </div>

        <h2 class="h5 fw-bold mb-1">
            <a
                href="<?php echo esc_url($card_link_url); ?>"
                class="<?php echo esc_attr($title_link_class); ?>"
                <?php if ($is_modal_card) : ?>
                    data-feuer-mannschaft-modal-target="<?php echo esc_attr($modal_target_id); ?>"
                    aria-haspopup="dialog"
                    aria-controls="<?php echo esc_attr($modal_target_id); ?>"
                <?php endif; ?>
            >
                <?php echo esc_html($name); ?>
            </a>
        </h2>

        <?php if ($function) : ?>
            <div class="text-muted mb-3"><?php echo esc_html($function); ?></div>
        <?php elseif (! empty($meta['rank'])) : ?>
            <div class="text-muted"><?php echo esc_html($meta['rank']); ?></div>
        <?php endif; ?>

        <?php if ($is_modal_card) : ?>
            <div class="mt-auto d-flex flex-wrap justify-content-center gap-2">
                <button
                    type="button"
                    class="btn btn-outline-secondary feuer-mannschaft-modal-trigger"
                    data-feuer-mannschaft-modal-target="<?php echo esc_attr($modal_target_id); ?>"
                    aria-haspopup="dialog"
                    aria-controls="<?php echo esc_attr($modal_target_id); ?>"
                >
                    Schnellansicht
                </button>
                <a class="btn btn-primary js-feuer-mannschaft-profile-link" href="<?php echo esc_url($profile_url); ?>">Profil oeffnen</a>
            </div>
        <?php endif; ?>
    </div>
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

<?php

if (! defined('ABSPATH')) {
    exit;
}
?>
<article class="card text-center border-0 shadow-sm h-100 eb-overview-card">
    <div class="card-body d-flex flex-column">
        <div class="mb-4 mx-auto w-100">
            <a href="<?php echo esc_url($profile_url); ?>" class="d-block text-decoration-none js-feuer-mannschaft-profile-link" aria-label="<?php echo esc_attr($name); ?>">
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
            <a href="<?php echo esc_url($profile_url); ?>" class="text-decoration-none text-dark js-feuer-mannschaft-profile-link">
                <?php echo esc_html($name); ?>
            </a>
        </h2>

        <?php if ($function) : ?>
            <div class="text-muted mb-3"><?php echo esc_html($function); ?></div>
        <?php elseif (! empty($meta['rank'])) : ?>
            <div class="text-muted"><?php echo esc_html($meta['rank']); ?></div>
        <?php endif; ?>
    </div>
</article>

<?php if ('modal' === $display_mode) : ?>
    <div class="modal fade" id="<?php echo esc_attr($modal_id); ?>" tabindex="-1" aria-hidden="true">
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
                                <button type="button" class="btn-close float-end" data-bs-dismiss="modal" aria-label="Schließen"></button>
                                <h3 class="h2 mb-2"><?php echo esc_html($name); ?></h3>
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
                                <a class="btn btn-primary js-feuer-mannschaft-profile-link" href="<?php echo esc_url($profile_url); ?>">Profil öffnen</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

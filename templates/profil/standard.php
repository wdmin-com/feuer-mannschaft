<?php
/**
 * Template Name: Standardprofil
 * Beschreibung: Schlichtes Bootstrap-Profil mit Bild links und Profiltext rechts.
 */

if (! defined('ABSPATH')) {
    exit;
}

$role_caption = ! empty($meta['function']) ? $meta['function'] : $meta['rank'];
?>
<div class="feuer-mannschaft-main-content feuer-mannschaft-profile-content">
<section class="py-2 feuer-mannschaft-single-page feuer-mannschaft-single-template-standard">
    <div class="container">
        <div class="row align-items-center justify-content-center mb-md-4 mt-md-5 gy-4">
            <div class="col-md-6 col-lg-6">
                <div class="feuer-mannschaft-profile-hero-media">
                    <?php if ($has_slider) : ?>
                        <div
                            id="mannschaftCarousel-<?php echo esc_attr($post_id); ?>"
                            class="carousel feuer-mannschaft-carousel"
                            style="<?php echo $media_style; ?>"
                            data-feuer-mannschaft-carousel
                            data-feuer-mannschaft-interval="<?php echo esc_attr((int) $settings['slider_autoplay']); ?>"
                        >
                            <div class="carousel-inner">
                                <?php foreach ($gallery as $index => $attachment_id) : ?>
                                    <div class="carousel-item <?php echo 0 === $index ? 'active' : ''; ?>">
                                        <?php echo wp_get_attachment_image($attachment_id, 'large', false, array('class' => 'd-block w-100 feuer-mannschaft-single-image')); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <button class="feuer-mannschaft-carousel-control feuer-mannschaft-carousel-control-prev" type="button" data-feuer-mannschaft-carousel-target="mannschaftCarousel-<?php echo esc_attr($post_id); ?>" data-feuer-mannschaft-carousel-action="prev">
                                <span class="feuer-mannschaft-carousel-control-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Vorheriges Bild</span>
                            </button>
                            <button class="feuer-mannschaft-carousel-control feuer-mannschaft-carousel-control-next" type="button" data-feuer-mannschaft-carousel-target="mannschaftCarousel-<?php echo esc_attr($post_id); ?>" data-feuer-mannschaft-carousel-action="next">
                                <span class="feuer-mannschaft-carousel-control-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Nächstes Bild</span>
                            </button>
                        </div>
                    <?php else : ?>
                        <div class="feuer-mannschaft-single-media" style="<?php echo $media_style; ?>">
                            <?php echo $this->get_member_visual_markup($post_id, 'large', 'feuer-mannschaft-single-image'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-md-6 col-lg-6">
                <div class="card-body ms-md-4 ms-lg-5 p-0">
                    <?php if ($role_caption) : ?>
                        <div class="text-muted lead mb-2">
                            <?php echo esc_html($role_caption); ?>
                        </div>
                    <?php endif; ?>

                    <h1 class="display-5 m-0 mb-4<?php echo ! empty($title_class) ? ' ' . esc_attr($title_class) : ''; ?>"<?php echo ! empty($title_style) ? ' style="' . esc_attr($title_style) . '"' : ''; ?>><?php echo esc_html($name); ?></h1>

                    <?php if (! empty($post->post_content)) : ?>
                        <div class="lead feuer-mannschaft-standard-lead mb-4">
                            <?php echo apply_filters('the_content', $post->post_content); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        </div>
                    <?php endif; ?>

                    <?php if (! empty($detail_rows)) : ?>
                        <div class="feuer-mannschaft-single-meta mb-4">
                            <?php foreach ($detail_rows as $detail) : ?>
                                <div class="d-flex flex-wrap gap-2">
                                    <span class="text-muted"><?php echo esc_html($detail['label']); ?>:</span>
                                    <?php echo $this->get_detail_value_markup($detail, 'fw-semibold'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (! empty($contact_buttons_markup)) : ?>
                        <div class="mb-4">
                            <?php echo $contact_buttons_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        </div>
                    <?php endif; ?>

                    <?php if (! empty($admin_notice_markup)) : ?>
                        <div class="mb-4">
                            <?php echo $admin_notice_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>

        <div class="row align-items-center justify-content-center gy-4 my-md-4">
            <div class="col-md-10 col-lg-10">
                <?php echo $this->render_service_timeline($post_id); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                <?php echo $this->render_mentions_block($post_id); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                <?php if (! empty($settings['single_navigation_enabled'])) : ?>
                <div class="d-flex flex-wrap gap-2 btn-group mt-5">
                    <a class="btn btn-outline-secondary feuer-mannschaft-back-link" href="<?php echo esc_url($back_url); ?>" data-default-url="<?php echo esc_url($back_url); ?>">Zurück zur Übersicht</a>
                        <?php if (! empty($nav['prev'])) : ?>
                            <a class="btn btn-outline-secondary" href="<?php echo esc_url($this->get_member_permalink($nav['prev'], $single_template)); ?>">&larr; Vorheriger</a>
                        <?php endif; ?>
                        <?php if (! empty($nav['next'])) : ?>
                            <a class="btn btn-outline-secondary" href="<?php echo esc_url($this->get_member_permalink($nav['next'], $single_template)); ?>">Nächstes Mitglied &rarr;</a>
                        <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</section>
</div>

<?php if (! empty($related_ids)) : ?>
        <?php
        $member_count = count($related_ids);
        $column_class = 1 === $member_count
            ? 'col-12'
            : 'col-12 col-md-6 col-lg-4';
        ?>

        <section class="py-5 eb-team-section feuer-mannschaft-related-members">
            <div class="container">
                <div class="row justify-content-center text-center mb-4">
                    <div class="col-12 col-lg-8 col-xxl-7 mx-auto">
                        <span class="text-muted">Unser Team</span>
                        <h3 class="display-6 fw-bold mb-0">Weitere Mitglieder</h3>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <?php foreach ($related_ids as $related_id) : ?>
                        <div class="<?php echo esc_attr($column_class); ?> mb-4">
                            <?php
                            echo $this->get_archive_card_markup($related_id, 'team-grid', $single_template); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                            ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
<?php endif; ?>

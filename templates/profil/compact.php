<?php
/**
 * Template Name: Kompaktprofil
 * Beschreibung: Kompakter Bootstrap-Aufbau mit Meta-Karten und reduzierter Darstellung.
 */

if (! defined('ABSPATH')) {
    exit;
}
?>
<div class="container py-4 feuer-mannschaft-single-page feuer-mannschaft-single-template-compact">
    <article class="card border-0 shadow-sm overflow-hidden">
        <div class="row g-0">
            <div class="col-lg-5">
                <?php if ($has_slider) : ?>
                    <div
                        id="mannschaftCompactCarousel-<?php echo esc_attr($post_id); ?>"
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
                        <button class="feuer-mannschaft-carousel-control feuer-mannschaft-carousel-control-prev" type="button" data-feuer-mannschaft-carousel-target="mannschaftCompactCarousel-<?php echo esc_attr($post_id); ?>" data-feuer-mannschaft-carousel-action="prev">
                            <span class="feuer-mannschaft-carousel-control-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Vorheriges Bild</span>
                        </button>
                        <button class="feuer-mannschaft-carousel-control feuer-mannschaft-carousel-control-next" type="button" data-feuer-mannschaft-carousel-target="mannschaftCompactCarousel-<?php echo esc_attr($post_id); ?>" data-feuer-mannschaft-carousel-action="next">
                            <span class="feuer-mannschaft-carousel-control-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Naechstes Bild</span>
                        </button>
                    </div>
                <?php else : ?>
                    <div class="feuer-mannschaft-single-media" style="<?php echo $media_style; ?>">
                        <?php echo $this->get_member_visual_markup($post_id, 'large', 'feuer-mannschaft-single-image'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="col-lg-7">
                <div class="card-body p-4 p-lg-5">
                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
                        <div>
                            <h1 class="mb-1<?php echo ! empty($title_class) ? ' ' . esc_attr($title_class) : ''; ?>"<?php echo ! empty($title_style) ? ' style="' . esc_attr($title_style) . '"' : ''; ?>><?php echo esc_html($name); ?></h1>
                            <?php if ($meta['rank']) : ?>
                                <p class="text-muted mb-0"><?php echo esc_html($meta['rank']); ?></p>
                            <?php endif; ?>
                        </div>
                        <?php if ($meta['function']) : ?>
                            <span class="badge text-bg-secondary"><?php echo esc_html($meta['function']); ?></span>
                        <?php endif; ?>
                    </div>

                    <?php if (! empty($detail_rows)) : ?>
                        <div class="row g-3 mb-4">
                            <?php foreach ($detail_rows as $detail) : ?>
                                <div class="col-sm-6">
                                    <div class="feuer-mannschaft-detail-box h-100">
                                        <div class="feuer-mannschaft-detail-label"><?php echo esc_html($detail['label']); ?></div>
                                        <div class="feuer-mannschaft-detail-value"><?php echo $this->get_detail_value_markup($detail); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
                                    </div>
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

                    <?php if (! empty($post->post_content)) : ?>
                        <div class="feuer-mannschaft-single-content mt-0">
                            <?php echo apply_filters('the_content', $post->post_content); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        </div>
                    <?php endif; ?>

                    <?php echo $this->render_service_timeline($post_id); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    <?php echo $this->render_mentions_block($post_id); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    <?php if (! empty($settings['single_navigation_enabled'])) : ?>
                    <div class="d-flex flex-wrap gap-2 mt-4">
                        <a class="btn btn-outline-secondary feuer-mannschaft-back-link" href="<?php echo esc_url($back_url); ?>" data-default-url="<?php echo esc_url($back_url); ?>">Zurueck zur Uebersicht</a>
                        <?php if (! empty($nav['prev'])) : ?>
                            <a class="btn btn-outline-secondary" href="<?php echo esc_url($this->get_member_permalink($nav['prev'], $single_template)); ?>">&larr; Vorheriger</a>
                        <?php endif; ?>
                        <?php if (! empty($nav['next'])) : ?>
                            <a class="btn btn-outline-secondary" href="<?php echo esc_url($this->get_member_permalink($nav['next'], $single_template)); ?>">Naechster &rarr;</a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </article>

    <?php if (! empty($related_ids)) : ?>
        <section class="mt-5 feuer-mannschaft-related-members">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="mb-0">Weitere Mitglieder</h3>
            </div>
            <div class="einsatz-posts">
                <?php foreach ($related_ids as $related_id) : ?>
                    <?php echo $this->get_archive_card_markup($related_id, 'cards', $single_template); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
</div>

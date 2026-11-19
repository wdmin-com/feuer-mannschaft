<?php
/**
 * Template Name: Magazinprofil
 * Beschreibung: Grosses Hero-Profil mit Faktenband, Inhaltssektionen und weiteren Mitgliedern.
 */

if (! defined('ABSPATH')) {
    exit;
}

$role_caption    = trim((string) ($meta['function'] ? $meta['function'] : $meta['rank']));
$hero_details    = array_slice($detail_rows, 0, 6);
$mentions_markup = $this->render_mentions_block($post_id);
$timeline_markup = $this->render_service_timeline($post_id);
$has_content     = '' !== trim((string) $post->post_content);
$content_class   = ($has_content && '' !== $mentions_markup) ? 'col-lg-8' : 'col-12';
$mentions_class  = $has_content ? 'col-lg-4' : 'col-12';
?>
<section class="py-4 feuer-mannschaft-single-page feuer-mannschaft-single-template-magazin">
    <div class="container">
        <article class="feuer-mannschaft-magazine-hero">
            <div class="feuer-mannschaft-magazine-copy">
                <div class="d-flex flex-wrap gap-2">
                    <?php if (! empty($settings['single_navigation_enabled'])) : ?>
                    <a class="btn btn-outline-secondary btn-sm feuer-mannschaft-back-link" href="<?php echo esc_url($back_url); ?>" data-default-url="<?php echo esc_url($back_url); ?>">Zurueck zur Uebersicht</a>
                    <?php endif; ?>
                    <?php if ('' !== $role_caption) : ?>
                        <span class="badge text-bg-secondary"><?php echo esc_html($role_caption); ?></span>
                    <?php endif; ?>
                </div>

                <div>
                    <h1 class="display-5 mb-2<?php echo ! empty($title_class) ? ' ' . esc_attr($title_class) : ''; ?>"<?php echo ! empty($title_style) ? ' style="' . esc_attr($title_style) . '"' : ''; ?>><?php echo esc_html($name); ?></h1>
                    <?php if (! empty($meta['rank']) && $meta['rank'] !== $role_caption) : ?>
                        <p class="lead mb-0 text-muted"><?php echo esc_html($meta['rank']); ?></p>
                    <?php endif; ?>
                </div>

                <?php if (! empty($hero_details)) : ?>
                    <div class="feuer-mannschaft-magazine-facts" aria-label="Profilinformationen">
                        <?php foreach ($hero_details as $detail) : ?>
                            <div class="feuer-mannschaft-magazine-fact">
                                <div class="feuer-mannschaft-magazine-fact-label"><?php echo esc_html($detail['label']); ?></div>
                                <div class="feuer-mannschaft-magazine-fact-value">
                                    <?php echo $this->get_detail_value_markup($detail); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if (! empty($contact_buttons_markup)) : ?>
                    <div class="mb-2">
                        <?php echo $contact_buttons_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    </div>
                <?php endif; ?>

                <?php if (! empty($admin_notice_markup)) : ?>
                    <div class="mb-2">
                        <?php echo $admin_notice_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    </div>
                <?php endif; ?>

                <div class="d-flex flex-wrap gap-2 feuer-mannschaft-magazine-nav">
                    <?php if (! empty($nav['prev'])) : ?>
                        <a class="btn btn-outline-secondary" href="<?php echo esc_url($this->get_member_permalink($nav['prev'], $single_template)); ?>">&larr; Vorheriger</a>
                    <?php endif; ?>
                    <?php if (! empty($nav['next'])) : ?>
                        <a class="btn btn-outline-secondary" href="<?php echo esc_url($this->get_member_permalink($nav['next'], $single_template)); ?>">Naechster &rarr;</a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="feuer-mannschaft-magazine-media-shell">
                <?php if ($has_slider) : ?>
                    <div
                        id="mannschaftMagazinCarousel-<?php echo esc_attr($post_id); ?>"
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
                        <button class="feuer-mannschaft-carousel-control feuer-mannschaft-carousel-control-prev" type="button" data-feuer-mannschaft-carousel-target="mannschaftMagazinCarousel-<?php echo esc_attr($post_id); ?>" data-feuer-mannschaft-carousel-action="prev">
                            <span class="feuer-mannschaft-carousel-control-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Vorheriges Bild</span>
                        </button>
                        <button class="feuer-mannschaft-carousel-control feuer-mannschaft-carousel-control-next" type="button" data-feuer-mannschaft-carousel-target="mannschaftMagazinCarousel-<?php echo esc_attr($post_id); ?>" data-feuer-mannschaft-carousel-action="next">
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
        </article>

        <?php if ('' !== $timeline_markup) : ?>
            <section class="feuer-mannschaft-magazine-panel mt-4">
                <?php echo $timeline_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            </section>
        <?php endif; ?>

        <?php if ($has_content || '' !== $mentions_markup) : ?>
            <div class="row g-4 mt-4">
                <?php if ($has_content) : ?>
                    <div class="<?php echo esc_attr($content_class); ?>">
                        <section class="feuer-mannschaft-magazine-panel">
                            <div class="feuer-mannschaft-magazine-panel-label">Profil</div>
                            <div class="feuer-mannschaft-single-content mt-0">
                                <?php echo apply_filters('the_content', $post->post_content); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                            </div>
                        </section>
                    </div>
                <?php endif; ?>

                <?php if ('' !== $mentions_markup) : ?>
                    <div class="<?php echo esc_attr($mentions_class); ?>">
                        <aside class="feuer-mannschaft-magazine-panel feuer-mannschaft-magazine-panel-secondary">
                            <div class="feuer-mannschaft-magazine-panel-label">Erwaehnungen</div>
                            <?php echo $mentions_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        </aside>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if (! empty($related_ids)) : ?>
            <section class="feuer-mannschaft-magazine-related feuer-mannschaft-related-members mt-5">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                    <h2 class="h4 mb-0">Weitere Mitglieder</h2>
                    <span class="text-muted small">Passende Profile aus der Mannschaft</span>
                </div>
                <div class="einsatz-posts">
                    <?php foreach ($related_ids as $related_id) : ?>
                        <?php echo $this->get_archive_card_markup($related_id, 'cards', $single_template); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
    </div>
</section>

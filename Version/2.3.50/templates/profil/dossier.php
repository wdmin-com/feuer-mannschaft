<?php
/**
 * Template Name: Dossierprofil
 * Beschreibung: Profil mit Fakten-Seitenleiste, Inhaltsbereichen und Teambezug.
 */

if (! defined('ABSPATH')) {
    exit;
}

$role_caption    = trim((string) ($meta['function'] ? $meta['function'] : $meta['rank']));
$mentions_markup = $this->render_mentions_block($post_id);
$has_content     = '' !== trim((string) $post->post_content);
$description     = trim((string) $meta['description']);
?>
<section class="py-4 feuer-mannschaft-single-page feuer-mannschaft-single-template-dossier">
    <div class="container">
        <div class="feuer-mannschaft-dossier-layout">
            <aside class="feuer-mannschaft-dossier-sidebar">
                <div class="feuer-mannschaft-dossier-card feuer-mannschaft-dossier-media-card">
                    <?php if ($has_slider) : ?>
                        <div
                            id="mannschaftDossierCarousel-<?php echo esc_attr($post_id); ?>"
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
                            <button class="feuer-mannschaft-carousel-control feuer-mannschaft-carousel-control-prev" type="button" data-feuer-mannschaft-carousel-target="mannschaftDossierCarousel-<?php echo esc_attr($post_id); ?>" data-feuer-mannschaft-carousel-action="prev">
                                <span class="feuer-mannschaft-carousel-control-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Vorheriges Bild</span>
                            </button>
                            <button class="feuer-mannschaft-carousel-control feuer-mannschaft-carousel-control-next" type="button" data-feuer-mannschaft-carousel-target="mannschaftDossierCarousel-<?php echo esc_attr($post_id); ?>" data-feuer-mannschaft-carousel-action="next">
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

                <div class="feuer-mannschaft-dossier-card">
                    <?php if ('' !== $role_caption) : ?>
                        <span class="badge text-bg-secondary mb-3"><?php echo esc_html($role_caption); ?></span>
                    <?php endif; ?>
                    <h1 class="h2 mb-1<?php echo ! empty($title_class) ? ' ' . esc_attr($title_class) : ''; ?>"<?php echo ! empty($title_style) ? ' style="' . esc_attr($title_style) . '"' : ''; ?>><?php echo esc_html($name); ?></h1>
                    <?php if (! empty($meta['rank']) && $meta['rank'] !== $role_caption) : ?>
                        <p class="text-muted mb-0"><?php echo esc_html($meta['rank']); ?></p>
                    <?php endif; ?>

                    <?php if (! empty($detail_rows)) : ?>
                        <div class="feuer-mannschaft-dossier-detail-list" aria-label="Profilinformationen">
                            <?php foreach ($detail_rows as $detail) : ?>
                                <div class="feuer-mannschaft-dossier-detail-row">
                                    <div class="feuer-mannschaft-dossier-detail-label"><?php echo esc_html($detail['label']); ?></div>
                                    <div class="feuer-mannschaft-dossier-detail-value">
                                        <?php echo $this->get_detail_value_markup($detail); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="feuer-mannschaft-dossier-card feuer-mannschaft-dossier-actions-card">
                    <a class="btn btn-outline-secondary feuer-mannschaft-back-link" href="<?php echo esc_url($back_url); ?>" data-default-url="<?php echo esc_url($back_url); ?>">Zurueck zur Uebersicht</a>
                    <?php if (! empty($nav['prev'])) : ?>
                        <a class="btn btn-outline-secondary" href="<?php echo esc_url($this->get_member_permalink($nav['prev'], $single_template)); ?>">&larr; Vorheriger</a>
                    <?php endif; ?>
                    <?php if (! empty($nav['next'])) : ?>
                        <a class="btn btn-outline-secondary" href="<?php echo esc_url($this->get_member_permalink($nav['next'], $single_template)); ?>">Naechster &rarr;</a>
                    <?php endif; ?>
                </div>
            </aside>

            <article class="feuer-mannschaft-dossier-main">
                <section class="feuer-mannschaft-dossier-section feuer-mannschaft-dossier-intro">
                    <div class="feuer-mannschaft-dossier-section-label">Uebersicht</div>
                    <?php if ('' !== $description) : ?>
                        <p class="feuer-mannschaft-dossier-description"><?php echo esc_html($description); ?></p>
                    <?php else : ?>
                        <p class="feuer-mannschaft-dossier-description">Persoenliche Informationen, Inhalte und weitere Verweise aus der Mannschaftsseite.</p>
                    <?php endif; ?>
                </section>

                <?php if ($has_content) : ?>
                    <section class="feuer-mannschaft-dossier-section">
                        <div class="feuer-mannschaft-dossier-section-label">Profil</div>
                        <div class="feuer-mannschaft-single-content mt-0">
                            <?php echo apply_filters('the_content', $post->post_content); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        </div>
                    </section>
                <?php endif; ?>

                <?php if ('' !== $mentions_markup) : ?>
                    <section class="feuer-mannschaft-dossier-section">
                        <div class="feuer-mannschaft-dossier-section-label">Erwaehnungen</div>
                        <?php echo $mentions_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    </section>
                <?php endif; ?>

                <?php if (! empty($related_ids)) : ?>
                    <section class="feuer-mannschaft-dossier-section">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                            <div>
                                <div class="feuer-mannschaft-dossier-section-label">Team</div>
                                <h2 class="h4 mb-0">Weitere Mitglieder</h2>
                            </div>
                            <span class="text-muted small">Passende Profile aus der Mannschaft</span>
                        </div>
                        <div class="einsatz-posts">
                            <?php foreach ($related_ids as $related_id) : ?>
                                <?php echo $this->get_archive_card_markup($related_id, 'cards', $single_template); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endif; ?>
            </article>
        </div>
    </div>
</section>

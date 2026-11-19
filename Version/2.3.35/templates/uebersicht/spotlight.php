<?php
/**
 * Template Name: Spotlight und Liste
 * Beschreibung: Hebt ein Mitglied gross hervor und listet die restlichen Mitglieder kompakt darunter.
 */

if (! defined('ABSPATH')) {
    exit;
}

$member_ids = array();
while ($query->have_posts()) :
    $query->the_post();
    $member_ids[] = get_the_ID();
endwhile;
?>
<div class="container py-5 feuer-mannschaft-archive-page feuer-mannschaft-template-spotlight" data-feuer-mannschaft-archive-url="<?php echo esc_url($this->get_current_url_without_filter()); ?>">
    <?php if (! empty($title) || ! empty($description)) : ?>
        <div class="row justify-content-center mb-4">
            <div class="col-xl-8 text-center">
                <?php if (! empty($title)) : ?>
                    <h1 class="mb-2"><?php echo esc_html($title); ?></h1>
                <?php endif; ?>
                <?php if (! empty($description)) : ?>
                    <p class="text-muted mb-0"><?php echo esc_html($description); ?></p>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if (! empty($member_ids)) : ?>
        <?php
        $featured_id          = array_shift($member_ids);
        $featured_meta        = $this->get_member_meta($featured_id);
        $featured_name        = $this->get_member_display_name($featured_id);
        $featured_profile_url = $this->get_member_permalink($featured_id, $single_template);
        $featured_role        = trim((string) ($featured_meta['function'] ? $featured_meta['function'] : $featured_meta['rank']));
        $featured_summary     = $featured_meta['description'] ? wp_trim_words($featured_meta['description'], 36, '...') : '';
        $featured_details     = $this->get_single_detail_rows($featured_id, 4);
        ?>
        <div class="feuer-mannschaft-spotlight-shell">
            <article class="feuer-mannschaft-spotlight-feature">
                <a class="feuer-mannschaft-spotlight-media js-feuer-mannschaft-profile-link" href="<?php echo esc_url($featured_profile_url); ?>">
                    <?php echo $this->get_member_visual_markup($featured_id, 'large', 'feuer-mannschaft-spotlight-image'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </a>

                <div class="feuer-mannschaft-spotlight-content">
                    <?php if ('' !== $featured_role) : ?>
                        <span class="badge text-bg-secondary"><?php echo esc_html($featured_role); ?></span>
                    <?php endif; ?>

                    <div>
                        <h2 class="display-6 mb-2">
                            <a class="text-decoration-none text-dark js-feuer-mannschaft-profile-link" href="<?php echo esc_url($featured_profile_url); ?>">
                                <?php echo esc_html($featured_name); ?>
                            </a>
                        </h2>
                        <?php if ('' !== $featured_summary) : ?>
                            <p class="mb-0 feuer-mannschaft-spotlight-summary"><?php echo esc_html($featured_summary); ?></p>
                        <?php endif; ?>
                    </div>

                    <?php if (! empty($featured_details)) : ?>
                        <div class="feuer-mannschaft-spotlight-meta" aria-label="Profilinformationen">
                            <?php foreach ($featured_details as $detail) : ?>
                                <div class="feuer-mannschaft-spotlight-meta-row">
                                    <span class="feuer-mannschaft-spotlight-meta-label"><?php echo esc_html($detail['label']); ?></span>
                                    <div class="feuer-mannschaft-spotlight-meta-value">
                                        <?php echo $this->get_detail_value_markup($detail, 'fw-semibold'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <div class="d-flex flex-wrap gap-2">
                        <a class="btn btn-primary js-feuer-mannschaft-profile-link" href="<?php echo esc_url($featured_profile_url); ?>">Profil ansehen</a>
                    </div>
                </div>
            </article>

            <?php if (! empty($member_ids)) : ?>
                <section class="feuer-mannschaft-spotlight-list">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                        <h3 class="h4 mb-0">Weitere Mitglieder</h3>
                        <span class="text-muted small"><?php echo esc_html(sprintf('%d Profile', count($member_ids))); ?></span>
                    </div>
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-0">
                            <?php foreach ($member_ids as $member_id) : ?>
                                <?php echo $this->get_archive_row_markup($member_id, $single_template); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </section>
            <?php endif; ?>
        </div>
    <?php else : ?>
        <div class="alert alert-warning mb-0">
            Keine aktiven Mannschaftsmitglieder gefunden.
        </div>
    <?php endif; ?>
</div>

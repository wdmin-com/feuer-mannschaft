<?php
/**
 * Template Name: Verzeichnis Kacheln
 * Beschreibung: Verdichtete Teilnehmeransicht mit horizontalen Kacheln und Kurzinfos.
 */

if (! defined('ABSPATH')) {
    exit;
}

$member_ids = array();
while ($query->have_posts()) :
    $query->the_post();
    $member_ids[] = get_the_ID();
endwhile;

$total_members = count($member_ids);
?>
<div class="container py-5 feuer-mannschaft-archive-page feuer-mannschaft-template-directory" data-feuer-mannschaft-archive-url="<?php echo esc_url($this->get_current_url_without_filter()); ?>">
    <header class="feuer-mannschaft-directory-hero">
        <div class="feuer-mannschaft-directory-intro">
            <?php if (! empty($title)) : ?>
                <h1 class="mb-2"><?php echo esc_html($title); ?></h1>
            <?php endif; ?>
            <?php if (! empty($description)) : ?>
                <p class="text-muted mb-0"><?php echo esc_html($description); ?></p>
            <?php endif; ?>
        </div>
        <div class="feuer-mannschaft-directory-count" aria-label="Teilnehmeranzahl">
            <span class="feuer-mannschaft-directory-count-label">Aktive Mitglieder</span>
            <strong><?php echo esc_html((string) $total_members); ?></strong>
        </div>
    </header>

    <?php if (! empty($member_ids)) : ?>
        <div class="feuer-mannschaft-directory-grid">
            <?php foreach ($member_ids as $member_id) : ?>
                <?php
                $meta         = $this->get_member_meta($member_id);
                $name         = $this->get_member_display_name($member_id);
                $profile_url  = $this->get_member_permalink($member_id, $single_template);
                $role_caption = trim((string) ($meta['function'] ? $meta['function'] : $meta['rank']));
                $description  = $meta['description'] ? wp_trim_words($meta['description'], 18, '...') : '';
                $detail_rows  = $this->get_single_detail_rows($member_id, 3);
                ?>
                <article class="feuer-mannschaft-directory-card">
                    <a class="feuer-mannschaft-directory-media js-feuer-mannschaft-profile-link" href="<?php echo esc_url($profile_url); ?>">
                        <?php echo $this->get_member_visual_markup($member_id, 'medium_large', 'feuer-mannschaft-directory-image'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    </a>

                    <div class="feuer-mannschaft-directory-body">
                        <div class="feuer-mannschaft-directory-topline">
                            <?php if ('' !== $role_caption) : ?>
                                <span class="badge text-bg-secondary"><?php echo esc_html($role_caption); ?></span>
                            <?php endif; ?>
                        </div>

                        <div>
                            <h2 class="h5 mb-1">
                                <a class="text-decoration-none text-dark js-feuer-mannschaft-profile-link" href="<?php echo esc_url($profile_url); ?>">
                                    <?php echo esc_html($name); ?>
                                </a>
                            </h2>
                            <?php if ('' !== $description) : ?>
                                <p class="text-muted mb-0 feuer-mannschaft-directory-summary"><?php echo esc_html($description); ?></p>
                            <?php endif; ?>
                        </div>

                        <?php if (! empty($detail_rows)) : ?>
                            <div class="feuer-mannschaft-directory-meta" aria-label="Kurzinfos">
                                <?php foreach ($detail_rows as $detail) : ?>
                                    <div class="feuer-mannschaft-directory-meta-item">
                                        <span class="feuer-mannschaft-directory-meta-label"><?php echo esc_html($detail['label']); ?></span>
                                        <div class="feuer-mannschaft-directory-meta-value">
                                            <?php echo $this->get_detail_value_markup($detail); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <div class="feuer-mannschaft-directory-actions">
                            <a class="btn btn-outline-secondary btn-sm js-feuer-mannschaft-profile-link" href="<?php echo esc_url($profile_url); ?>">Profil ansehen</a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php else : ?>
        <div class="alert alert-warning mb-0">
            Keine aktiven Mannschaftsmitglieder gefunden.
        </div>
    <?php endif; ?>
</div>

<?php
/**
 * Template Name: Team Raster
 * Beschreibung: Schlichte Team-Übersicht mit Foto, Name und Funktion in drei Spalten.
 */

if (! defined('ABSPATH')) {
    exit;
}
?>
<div class="container py-5 feuer-mannschaft-archive-page <?php echo esc_attr($card_display_classes); ?> feuer-mannschaft-template-team-grid" data-feuer-mannschaft-archive-url="<?php echo esc_url($this->get_current_url_without_filter()); ?>">
    <?php if (! empty($title) || ! empty($description)) : ?>
        <div class="row justify-content-center mb-5">
            <div class="col-lg-8 text-center">
                <?php if (! empty($title)) : ?>
                    <h1 class="mb-2"><?php echo esc_html($title); ?></h1>
                <?php endif; ?>
                <?php if (! empty($description)) : ?>
                    <p class="text-muted mb-0"><?php echo esc_html($description); ?></p>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($query->have_posts()) : ?>
        <div class="feuer-mannschaft-member-grid">
            <?php while ($query->have_posts()) : ?>
                <?php
                $query->the_post();
                ?>
                <?php echo $this->get_archive_grid_markup(get_the_ID(), 'team-grid', $single_template); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            <?php endwhile; ?>
        </div>
    <?php else : ?>
        <div class="alert alert-warning mb-0">
            Keine aktiven Mannschaftsmitglieder gefunden.
        </div>
    <?php endif; ?>
</div>

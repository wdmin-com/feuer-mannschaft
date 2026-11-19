<?php
/**
 * Template Name: Team Raster Graustufen
 * Beschreibung: Team-Raster mit schwarz-weissen Fotos, die beim Hover farbig werden.
 */

if (! defined('ABSPATH')) {
    exit;
}
?>
<div class="container py-5 feuer-mannschaft-archive-page feuer-mannschaft-template-team-grid feuer-mannschaft-template-team-grayscale" data-feuer-mannschaft-archive-url="<?php echo esc_url($this->get_current_url_without_filter()); ?>">
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
        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-4">
            <?php while ($query->have_posts()) : ?>
                <?php $query->the_post(); ?>
                <div class="col">
                    <?php echo $this->get_archive_grid_markup(get_the_ID(), 'team-grid', $single_template); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else : ?>
        <div class="alert alert-warning mb-0">
            Keine aktiven Mannschaftsmitglieder gefunden.
        </div>
    <?php endif; ?>
</div>

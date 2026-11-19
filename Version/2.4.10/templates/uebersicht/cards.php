<?php
/**
 * Template Name: Karten
 * Beschreibung: Standardansicht mit Bootstrap-Karten.
 */

if (! defined('ABSPATH')) {
    exit;
}
?>
<div class="container py-4 eb-overview-page feuer-mannschaft-archive-page <?php echo esc_attr($card_display_classes); ?> feuer-mannschaft-template-cards" data-feuer-mannschaft-archive-url="<?php echo esc_url($this->get_current_url_without_filter()); ?>">
    <div class="row g-4">
        <div class="col-12">
            <?php if (! empty($title) || ! empty($description)) : ?>
                <header class="eb-overview-header mb-4">
                    <?php if (! empty($title)) : ?>
                        <h1 class="mb-2"><?php echo esc_html($title); ?></h1>
                    <?php endif; ?>
                    <?php if (! empty($description)) : ?>
                        <p class="text-muted mb-0"><?php echo esc_html($description); ?></p>
                    <?php endif; ?>
                </header>
            <?php endif; ?>

            <?php if ($query->have_posts()) : ?>
                <div class="einsatz-posts">
                    <?php while ($query->have_posts()) : ?>
                        <?php
                        $query->the_post();
                        echo $this->get_archive_card_markup(get_the_ID(), 'cards', $single_template); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        ?>
                    <?php endwhile; ?>
                </div>
            <?php else : ?>
                <div class="alert alert-warning">
                    Keine aktiven Mannschaftsmitglieder gefunden.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

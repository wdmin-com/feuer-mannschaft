<?php
/**
 * Template Name: Einfache Liste
 * Beschreibung: Schlichte Listenansicht mit Foto, Kurzinfos und Button.
 */

if (! defined('ABSPATH')) {
    exit;
}
?>
<div class="container py-4 feuer-mannschaft-archive-page feuer-mannschaft-template-list" data-feuer-mannschaft-archive-url="<?php echo esc_url($this->get_current_url_without_filter()); ?>">
    <div class="row g-4">
        <div class="col-12">
            <?php if (! empty($title) || ! empty($description)) : ?>
                <header class="mb-4">
                    <?php if (! empty($title)) : ?>
                        <h1 class="mb-2"><?php echo esc_html($title); ?></h1>
                    <?php endif; ?>
                    <?php if (! empty($description)) : ?>
                        <p class="text-muted mb-0"><?php echo esc_html($description); ?></p>
                    <?php endif; ?>
                </header>
            <?php endif; ?>

            <?php if ($query->have_posts()) : ?>
                <div class="card shadow-sm border-0">
                    <div class="card-body p-0">
                        <?php while ($query->have_posts()) : ?>
                            <?php
                            $query->the_post();
                            echo $this->get_archive_row_markup(get_the_ID(), $single_template); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                            ?>
                        <?php endwhile; ?>
                    </div>
                </div>
            <?php else : ?>
                <div class="alert alert-warning mb-0">
                    Keine aktiven Mannschaftsmitglieder gefunden.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php

if (! defined('ABSPATH')) {
    exit;
}
?>
<div class="feuer-mannschaft-category-box">
    <p class="description">Wählen Sie eine oder mehrere Kategorien, in denen Feuer-Mannschaft-Profile verwendet werden dürfen.</p>

    <?php if (empty($choices)) : ?>
        <p>Keine Kategorien gefunden.</p>
    <?php else : ?>
        <div class="feuer-mannschaft-category-list">
            <?php foreach ($choices as $choice) : ?>
                <?php
                $term       = $choice['term'];
                $depth      = (int) $choice['depth'];
                $is_checked = in_array((int) $term->term_id, $selected, true);
                ?>
                <label class="feuer-mannschaft-category-item">
                    <input type="checkbox" name="feuer_mannschaft_categories[]" value="<?php echo esc_attr($term->term_id); ?>" <?php checked($is_checked); ?>>
                    <span><?php echo wp_kses_post(str_repeat('&mdash; ', $depth)); ?><?php echo esc_html($term->name); ?></span>
                </label>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

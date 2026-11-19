<?php

if (! defined('ABSPATH')) {
    exit;
}
?>
<p class="description"><?php echo esc_html__('Wählen Sie Teilnehmer, die in diesem Beitrag erwähnt werden.', 'feuer-mannschaft'); ?></p>
<select name="<?php echo esc_attr($meta_key); ?>[]" class="widefat" multiple size="10">
    <?php foreach ($members as $member) : ?>
        <option value="<?php echo esc_attr((string) $member->ID); ?>" <?php selected(in_array((int) $member->ID, $selected_ids, true)); ?>>
            <?php echo esc_html(get_the_title($member)); ?>
        </option>
    <?php endforeach; ?>
</select>

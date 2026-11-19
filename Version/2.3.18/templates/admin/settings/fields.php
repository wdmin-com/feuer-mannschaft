<?php

if (! defined('ABSPATH')) {
    exit;
}

$conflicts = isset($pending_field_replacements['conflicts']) && is_array($pending_field_replacements['conflicts'])
    ? $pending_field_replacements['conflicts']
    : array();
$saved_replacements = isset($pending_field_replacements['replacements']) && is_array($pending_field_replacements['replacements'])
    ? $pending_field_replacements['replacements']
    : array();
?>
<div class="feuer-mannschaft-field-panels">
    <p class="description feuer-mannschaft-fields-intro">
        Verwalten Sie hier die auswählbaren Listen für Dienstgrad, Funktion und Ausbildung. Jede Zeile ist ein eigener Eintrag und kann bei Bedarf jederzeit ergänzt oder entfernt werden.
    </p>

    <?php foreach ($field_groups as $setting_key => $group) : ?>
        <?php
        $values = isset($settings[$setting_key]) && is_array($settings[$setting_key]) ? $settings[$setting_key] : array();
        if (empty($values)) {
            $values = array('');
        }

        $value_count = count(
            array_values(
                array_filter(
                    $values,
                    function ($value) {
                        return '' !== trim((string) $value);
                    }
                )
            )
        );

        $group_conflicts = isset($conflicts[$setting_key]) && is_array($conflicts[$setting_key]) ? $conflicts[$setting_key] : array();
        $replacement_options = array_values(array_filter(isset($settings[$setting_key]) && is_array($settings[$setting_key]) ? $settings[$setting_key] : array()));
        ?>
        <section class="feuer-mannschaft-field-panel">
            <div class="feuer-mannschaft-field-panel-header">
                <div class="feuer-mannschaft-field-panel-copy">
                    <h2 class="feuer-mannschaft-field-panel-title"><?php echo esc_html($group['label']); ?></h2>
                    <p class="description"><?php echo esc_html($group['description']); ?></p>
                </div>
                <span class="feuer-mannschaft-field-panel-count" aria-label="<?php echo esc_attr($group['label'] . ' Einträge'); ?>">
                    <?php echo esc_html((string) $value_count); ?>
                </span>
            </div>

            <div
                class="feuer-mannschaft-repeater"
                data-repeater
                data-repeater-name="<?php echo esc_attr(Feuer_Mannschaft_Plugin::OPTION_KEY . '[' . $setting_key . '][]'); ?>"
                data-repeater-placeholder="<?php echo esc_attr($group['label']); ?>"
            >
                <div class="feuer-mannschaft-repeater-rows" data-repeater-rows>
                    <?php foreach ($values as $index => $value) : ?>
                        <div class="feuer-mannschaft-repeater-row" data-repeater-row>
                            <input
                                type="text"
                                class="widefat"
                                id="<?php echo esc_attr('feuer_mannschaft_' . $setting_key . '_' . $index); ?>"
                                name="<?php echo esc_attr(Feuer_Mannschaft_Plugin::OPTION_KEY . '[' . $setting_key . '][]'); ?>"
                                value="<?php echo esc_attr($value); ?>"
                                placeholder="<?php echo esc_attr($group['label']); ?>"
                            >
                            <button type="button" class="button button-link-delete" data-repeater-remove>Entfernen</button>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="feuer-mannschaft-repeater-actions">
                    <button type="button" class="button button-secondary" data-repeater-add><?php echo esc_html($group['add_label']); ?></button>
                </div>
            </div>

            <?php if (! empty($group_conflicts)) : ?>
                <div class="notice notice-warning inline feuer-mannschaft-inline-notice">
                    <p>Entfernte Einträge sind bereits in Profilen hinterlegt. Bitte wählen Sie eine Ersetzung, bevor die Liste gespeichert wird.</p>
                </div>

                <div class="feuer-mannschaft-replacement-list">
                    <?php foreach ($group_conflicts as $removed_value => $details) : ?>
                        <?php
                        $replacement_value = isset($saved_replacements[$setting_key][$removed_value]) ? $saved_replacements[$setting_key][$removed_value] : '';
                        $member_names = array();

                        if (! empty($details['members']) && is_array($details['members'])) {
                            foreach ($details['members'] as $member) {
                                if (! empty($member['name'])) {
                                    $member_names[] = $member['name'];
                                }
                            }
                        }

                        $member_names = array_slice(array_values(array_unique($member_names)), 0, 5);
                        ?>
                        <div class="feuer-mannschaft-replacement-row">
                            <label for="<?php echo esc_attr('feuer_mannschaft_replace_' . md5($setting_key . $removed_value)); ?>">
                                <strong><?php echo esc_html($removed_value); ?></strong> ersetzen durch
                            </label>
                            <select
                                id="<?php echo esc_attr('feuer_mannschaft_replace_' . md5($setting_key . $removed_value)); ?>"
                                name="<?php echo esc_attr(Feuer_Mannschaft_Plugin::OPTION_KEY . '[pending_replacements][' . $setting_key . '][' . $removed_value . ']'); ?>"
                            >
                                <option value="">Bitte Ersatz wählen</option>
                                <?php foreach ($replacement_options as $option) : ?>
                                    <?php if ($option === $removed_value) : ?>
                                        <?php continue; ?>
                                    <?php endif; ?>
                                    <option value="<?php echo esc_attr($option); ?>" <?php selected($replacement_value, $option); ?>>
                                        <?php echo esc_html($option); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <p class="description">
                                Verwendet in <?php echo esc_html((string) $details['count']); ?> Profilen
                                <?php if (! empty($member_names)) : ?>
                                    : <?php echo esc_html(implode(', ', $member_names)); ?>
                                <?php endif; ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    <?php endforeach; ?>
</div>

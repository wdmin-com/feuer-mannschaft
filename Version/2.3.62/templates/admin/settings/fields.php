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
$group_conflicts = isset($pending_field_replacements['group_conflicts']) && is_array($pending_field_replacements['group_conflicts'])
    ? $pending_field_replacements['group_conflicts']
    : array();
$fixed_groups = array_filter(
    $field_groups,
    function ($group) {
        return 'fixed' === (isset($group['source']) ? $group['source'] : '');
    }
);
$custom_groups = array_filter(
    $field_groups,
    function ($group) {
        return 'custom' === (isset($group['source']) ? $group['source'] : '');
    }
);
$contact_field_definitions = isset($contact_field_definitions) && is_array($contact_field_definitions)
    ? $contact_field_definitions
    : array();
$contact_field_settings = isset($contact_field_settings) && is_array($contact_field_settings)
    ? $contact_field_settings
    : array();
?>
<div class="feuer-mannschaft-field-panels">
    <p class="description feuer-mannschaft-fields-intro">
        Verwalten Sie hier die auswählbaren Listen für Dienstgrad, Funktion, Ausbildung und zusätzliche Listenfelder.
        Jeder Eintrag besteht aus einem Namen und optional einer URL, die später im Profil des Teilnehmers klickbar ausgegeben wird.
    </p>

    <?php if (! empty($group_conflicts)) : ?>
        <div class="notice notice-warning inline feuer-mannschaft-inline-notice">
            <p>Einige Zusatzfelder werden noch in Profilen verwendet und können deshalb noch nicht entfernt werden.</p>
            <div class="feuer-mannschaft-replacement-list">
                <?php foreach ($group_conflicts as $group_conflict) : ?>
                    <?php
                    $member_names = array();
                    if (! empty($group_conflict['members']) && is_array($group_conflict['members'])) {
                        foreach ($group_conflict['members'] as $member) {
                            if (! empty($member['name'])) {
                                $member_names[] = $member['name'];
                            }
                        }
                    }
                    $member_names = array_slice(array_values(array_unique($member_names)), 0, 6);
                    ?>
                    <div class="feuer-mannschaft-replacement-row">
                        <strong><?php echo esc_html(isset($group_conflict['label']) ? $group_conflict['label'] : 'Zusatzfeld'); ?></strong>
                        <p class="description">
                            Verwendet in <?php echo esc_html((string) (isset($group_conflict['count']) ? (int) $group_conflict['count'] : 0)); ?> Profilen
                            <?php if (! empty($member_names)) : ?>
                                : <?php echo esc_html(implode(', ', $member_names)); ?>
                            <?php endif; ?>
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <section class="feuer-mannschaft-settings-group">
        <div class="feuer-mannschaft-settings-group-header">
            <h2 class="feuer-mannschaft-field-panel-title">Standardfelder</h2>
            <p class="description">Diese Bereiche sind fest im Plugin verankert und stehen jedem Teilnehmerprofil automatisch zur Verfügung.</p>
        </div>

        <section class="feuer-mannschaft-field-panel">
            <div class="feuer-mannschaft-field-panel-header">
                <div class="feuer-mannschaft-field-panel-copy">
                    <h3 class="feuer-mannschaft-field-panel-title">Kontaktfelder</h3>
                    <p class="description">Aktivierte Kontaktfelder erscheinen in der Profilbearbeitung eines Mitglieds und werden im Frontend als Kontakt-Buttons ausgegeben.</p>
                </div>
            </div>

            <div class="feuer-mannschaft-switch-list feuer-mannschaft-switch-list-wide">
                <?php foreach ($contact_field_definitions as $field_key => $field_definition) : ?>
                    <?php
                    $field_id = 'feuer_mannschaft_contact_field_' . sanitize_html_class($field_key);
                    $is_enabled = ! empty($contact_field_settings[$field_key]);
                    ?>
                    <label class="form-check form-switch feuer-mannschaft-switch-item" for="<?php echo esc_attr($field_id); ?>">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            id="<?php echo esc_attr($field_id); ?>"
                            name="<?php echo esc_attr(Feuer_Mannschaft_Plugin::OPTION_KEY . '[contact_fields][' . $field_key . ']'); ?>"
                            value="1"
                            <?php checked($is_enabled); ?>
                        >
                        <span class="form-check-label">
                            <span class="feuer-mannschaft-switch-copy">
                                <span><?php echo esc_html(isset($field_definition['label']) ? $field_definition['label'] : $field_key); ?></span>
                                <?php if (! empty($field_definition['description'])) : ?>
                                    <span class="description"><?php echo esc_html($field_definition['description']); ?></span>
                                <?php endif; ?>
                            </span>
                        </span>
                    </label>
                <?php endforeach; ?>
            </div>
        </section>

        <?php foreach ($fixed_groups as $group_id => $group) : ?>
            <?php
            $options = isset($group['options']) && is_array($group['options']) ? $group['options'] : array();
            if (empty($options)) {
                $options = array(
                    array(
                        'label' => '',
                        'url'   => '',
                    ),
                );
            }
            $value_count = count(
                array_values(
                    array_filter(
                        array_map(
                            function ($option) {
                                return isset($option['label']) ? trim((string) $option['label']) : '';
                            },
                            $options
                        )
                    )
                )
            );
            $field_conflicts = isset($conflicts[$group_id]) && is_array($conflicts[$group_id]) ? $conflicts[$group_id] : array();
            $replacement_options = array_values(
                array_filter(
                    array_map(
                        function ($option) {
                            return isset($option['label']) ? trim((string) $option['label']) : '';
                        },
                        isset($group['options']) && is_array($group['options']) ? $group['options'] : array()
                    )
                )
            );
            $option_prefix = Feuer_Mannschaft_Plugin::OPTION_KEY . '[' . $group['setting_key'] . ']';
            ?>
            <section class="feuer-mannschaft-field-panel" data-field-group-panel>
                <div class="feuer-mannschaft-field-panel-header">
                    <div class="feuer-mannschaft-field-panel-copy">
                        <h3 class="feuer-mannschaft-field-panel-title"><?php echo esc_html($group['label']); ?></h3>
                        <p class="description"><?php echo esc_html($group['description']); ?></p>
                    </div>
                    <span class="feuer-mannschaft-field-panel-count" data-option-count><?php echo esc_html((string) $value_count); ?></span>
                </div>

                <div class="feuer-mannschaft-option-repeater" data-option-repeater data-option-prefix="<?php echo esc_attr($option_prefix); ?>" data-option-placeholder="<?php echo esc_attr($group['label']); ?>">
                    <div class="feuer-mannschaft-repeater-rows" data-option-rows>
                        <?php foreach ($options as $option_index => $option) : ?>
                            <div class="feuer-mannschaft-repeater-row feuer-mannschaft-option-row" data-option-row>
                                <input
                                    type="text"
                                    class="widefat"
                                    data-option-field="label"
                                    name="<?php echo esc_attr($option_prefix . '[' . $option_index . '][label]'); ?>"
                                    value="<?php echo esc_attr(isset($option['label']) ? $option['label'] : ''); ?>"
                                    placeholder="<?php echo esc_attr($group['label']); ?>"
                                >
                                <input
                                    type="url"
                                    class="widefat"
                                    data-option-field="url"
                                    name="<?php echo esc_attr($option_prefix . '[' . $option_index . '][url]'); ?>"
                                    value="<?php echo esc_attr(isset($option['url']) ? $option['url'] : ''); ?>"
                                    placeholder="https://example.de/"
                                >
                                <div class="feuer-mannschaft-option-actions">
                                    <button type="button" class="button button-secondary" data-option-move-up>Hoch</button>
                                    <button type="button" class="button button-secondary" data-option-move-down>Runter</button>
                                    <button type="button" class="button button-link-delete" data-option-remove>Entfernen</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="feuer-mannschaft-repeater-actions">
                        <button type="button" class="button button-secondary" data-option-add><?php echo esc_html($group['add_label']); ?></button>
                    </div>
                </div>

                <?php if (! empty($field_conflicts)) : ?>
                    <div class="notice notice-warning inline feuer-mannschaft-inline-notice">
                        <p>Entfernte Einträge sind bereits in Profilen hinterlegt. Bitte wählen Sie eine Ersetzung, bevor die Liste gespeichert wird.</p>
                    </div>

                    <div class="feuer-mannschaft-replacement-list">
                        <?php foreach ($field_conflicts as $removed_value => $details) : ?>
                            <?php
                            $replacement_value = isset($saved_replacements[$group_id][$removed_value]) ? $saved_replacements[$group_id][$removed_value] : '';
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
                                <label for="<?php echo esc_attr('feuer_mannschaft_replace_' . md5($group_id . $removed_value)); ?>">
                                    <strong><?php echo esc_html($removed_value); ?></strong> ersetzen durch
                                </label>
                                <select
                                    id="<?php echo esc_attr('feuer_mannschaft_replace_' . md5($group_id . $removed_value)); ?>"
                                    name="<?php echo esc_attr(Feuer_Mannschaft_Plugin::OPTION_KEY . '[pending_replacements][' . $group_id . '][' . $removed_value . ']'); ?>"
                                >
                                    <option value="">Bitte Ersatz wählen</option>
                                    <?php foreach ($replacement_options as $option_label) : ?>
                                        <?php if ($option_label === $removed_value) : ?>
                                            <?php continue; ?>
                                        <?php endif; ?>
                                        <option value="<?php echo esc_attr($option_label); ?>" <?php selected($replacement_value, $option_label); ?>>
                                            <?php echo esc_html($option_label); ?>
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
    </section>

    <section class="feuer-mannschaft-settings-group">
        <div class="feuer-mannschaft-settings-group-header">
            <h2 class="feuer-mannschaft-field-panel-title">Zusatzfelder</h2>
            <p class="description">Legen Sie hier weitere Abschnitte an, die im Profil eines Teilnehmers auswählbar und im Frontend sichtbar sein sollen.</p>
        </div>

        <div class="feuer-mannschaft-custom-group-list" data-custom-field-groups data-custom-field-groups-name="<?php echo esc_attr(Feuer_Mannschaft_Plugin::OPTION_KEY . '[custom_field_groups]'); ?>">
            <?php foreach ($custom_groups as $group_index => $group) : ?>
                <?php
                $options = isset($group['options']) && is_array($group['options']) ? $group['options'] : array();
                if (empty($options)) {
                    $options = array(
                        array(
                            'label' => '',
                            'url'   => '',
                        ),
                    );
                }
                $option_count = count(
                    array_values(
                        array_filter(
                            array_map(
                                function ($option) {
                                    return isset($option['label']) ? trim((string) $option['label']) : '';
                                },
                                $options
                            )
                        )
                    )
                );
                $option_prefix = Feuer_Mannschaft_Plugin::OPTION_KEY . '[custom_field_groups][' . $group_index . '][options]';
                ?>
                <section class="feuer-mannschaft-field-panel feuer-mannschaft-custom-group-panel" data-field-group-panel data-custom-field-group>
                    <div class="feuer-mannschaft-field-panel-header">
                        <div class="feuer-mannschaft-field-panel-copy">
                            <h3 class="feuer-mannschaft-field-panel-title" data-custom-group-title><?php echo esc_html($group['label']); ?></h3>
                            <p class="description">Zusätzlicher Profilabschnitt mit optionalen Links pro Eintrag.</p>
                        </div>
                        <div class="feuer-mannschaft-custom-group-header-actions">
                            <span class="feuer-mannschaft-field-panel-count" data-option-count><?php echo esc_html((string) $option_count); ?></span>
                            <button type="button" class="button button-link-delete" data-custom-field-group-remove>Feld entfernen</button>
                        </div>
                    </div>

                    <input type="hidden" data-custom-group-field="key" name="<?php echo esc_attr(Feuer_Mannschaft_Plugin::OPTION_KEY . '[custom_field_groups][' . $group_index . '][key]'); ?>" value="<?php echo esc_attr(isset($group['custom_key']) ? $group['custom_key'] : ''); ?>">

                    <div class="feuer-mannschaft-custom-group-grid">
                        <div class="feuer-mannschaft-custom-group-field">
                            <label><strong>Name des Abschnitts</strong></label>
                            <input type="text" class="widefat" data-custom-group-field="label" name="<?php echo esc_attr(Feuer_Mannschaft_Plugin::OPTION_KEY . '[custom_field_groups][' . $group_index . '][label]'); ?>" value="<?php echo esc_attr($group['label']); ?>" placeholder="z. B. Lehrgänge">
                        </div>
                        <div class="feuer-mannschaft-custom-group-field">
                            <label><strong>Auswahlmodus</strong></label>
                            <select class="widefat" data-custom-group-field="multiple" name="<?php echo esc_attr(Feuer_Mannschaft_Plugin::OPTION_KEY . '[custom_field_groups][' . $group_index . '][multiple]'); ?>">
                                <option value="0" <?php selected(empty($group['multiple'])); ?>>Einzelauswahl</option>
                                <option value="1" <?php selected(! empty($group['multiple'])); ?>>Mehrfachauswahl</option>
                            </select>
                        </div>
                        <div class="feuer-mannschaft-custom-group-field feuer-mannschaft-custom-group-field-full">
                            <label><strong>Beschreibung</strong></label>
                            <textarea class="widefat" rows="3" data-custom-group-field="description" name="<?php echo esc_attr(Feuer_Mannschaft_Plugin::OPTION_KEY . '[custom_field_groups][' . $group_index . '][description]'); ?>" placeholder="Kurzbeschreibung für den Bereich"><?php echo esc_textarea(isset($group['description']) ? $group['description'] : ''); ?></textarea>
                        </div>
                    </div>

                    <div class="feuer-mannschaft-option-repeater" data-option-repeater data-option-prefix="<?php echo esc_attr($option_prefix); ?>" data-option-placeholder="Eintrag">
                        <div class="feuer-mannschaft-repeater-rows" data-option-rows>
                            <?php foreach ($options as $option_index => $option) : ?>
                                <div class="feuer-mannschaft-repeater-row feuer-mannschaft-option-row" data-option-row>
                                    <input
                                        type="text"
                                        class="widefat"
                                        data-option-field="label"
                                        name="<?php echo esc_attr($option_prefix . '[' . $option_index . '][label]'); ?>"
                                        value="<?php echo esc_attr(isset($option['label']) ? $option['label'] : ''); ?>"
                                        placeholder="Eintrag"
                                    >
                                    <input
                                        type="url"
                                        class="widefat"
                                        data-option-field="url"
                                        name="<?php echo esc_attr($option_prefix . '[' . $option_index . '][url]'); ?>"
                                        value="<?php echo esc_attr(isset($option['url']) ? $option['url'] : ''); ?>"
                                        placeholder="https://example.de/"
                                    >
                                    <div class="feuer-mannschaft-option-actions">
                                        <button type="button" class="button button-secondary" data-option-move-up>Hoch</button>
                                        <button type="button" class="button button-secondary" data-option-move-down>Runter</button>
                                        <button type="button" class="button button-link-delete" data-option-remove>Entfernen</button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="feuer-mannschaft-repeater-actions">
                            <button type="button" class="button button-secondary" data-option-add>Eintrag hinzufügen</button>
                        </div>
                    </div>
                </section>
            <?php endforeach; ?>
        </div>

        <div class="feuer-mannschaft-repeater-actions">
            <button type="button" class="button button-secondary" data-custom-field-group-add>Zusatzfeld hinzufügen</button>
        </div>

        <template id="feuer-mannschaft-custom-field-group-template">
            <section class="feuer-mannschaft-field-panel feuer-mannschaft-custom-group-panel" data-field-group-panel data-custom-field-group>
                <div class="feuer-mannschaft-field-panel-header">
                    <div class="feuer-mannschaft-field-panel-copy">
                        <h3 class="feuer-mannschaft-field-panel-title" data-custom-group-title>Neues Zusatzfeld</h3>
                        <p class="description">Zusätzlicher Profilabschnitt mit optionalen Links pro Eintrag.</p>
                    </div>
                    <div class="feuer-mannschaft-custom-group-header-actions">
                        <span class="feuer-mannschaft-field-panel-count" data-option-count>0</span>
                        <button type="button" class="button button-link-delete" data-custom-field-group-remove>Feld entfernen</button>
                    </div>
                </div>

                <input type="hidden" data-custom-group-field="key" value="">

                <div class="feuer-mannschaft-custom-group-grid">
                    <div class="feuer-mannschaft-custom-group-field">
                        <label><strong>Name des Abschnitts</strong></label>
                        <input type="text" class="widefat" data-custom-group-field="label" value="" placeholder="z. B. Lehrgänge">
                    </div>
                    <div class="feuer-mannschaft-custom-group-field">
                        <label><strong>Auswahlmodus</strong></label>
                        <select class="widefat" data-custom-group-field="multiple">
                            <option value="0">Einzelauswahl</option>
                            <option value="1">Mehrfachauswahl</option>
                        </select>
                    </div>
                    <div class="feuer-mannschaft-custom-group-field feuer-mannschaft-custom-group-field-full">
                        <label><strong>Beschreibung</strong></label>
                        <textarea class="widefat" rows="3" data-custom-group-field="description" placeholder="Kurzbeschreibung für den Bereich"></textarea>
                    </div>
                </div>

                <div class="feuer-mannschaft-option-repeater" data-option-repeater data-option-prefix="" data-option-placeholder="Eintrag">
                    <div class="feuer-mannschaft-repeater-rows" data-option-rows></div>

                    <div class="feuer-mannschaft-repeater-actions">
                        <button type="button" class="button button-secondary" data-option-add>Eintrag hinzufügen</button>
                    </div>
                </div>
            </section>
        </template>
    </section>
</div>

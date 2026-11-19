<?php

if (! defined('ABSPATH')) {
    exit;
}

$enabled_contact_fields = isset($enabled_contact_fields) && is_array($enabled_contact_fields)
    ? $enabled_contact_fields
    : array();
$contact_field_values = isset($contact_field_values) && is_array($contact_field_values)
    ? $contact_field_values
    : array();
$show_admin_note = ! empty($show_admin_note);
?>
<input type="hidden" name="feuer_mannschaft_profile" value="1">

<div class="feuer-mannschaft-admin-grid">
    <p class="description feuer-mannschaft-field-full">Pflegen Sie hier das Mannschaft-Profil. Der Titel wird automatisch aus Vorname und Nachname erzeugt. Wenn unten ein eigener Link hinterlegt ist, verwenden Karten und Buttons diesen Link statt der Profilseite.</p>

    <div class="feuer-mannschaft-field">
        <label for="feuer_mannschaft_first_name"><strong>Vorname</strong></label>
        <input type="text" class="widefat" id="feuer_mannschaft_first_name" name="feuer_mannschaft_meta[first_name]" value="<?php echo esc_attr($meta['first_name']); ?>">
    </div>

    <div class="feuer-mannschaft-field">
        <label for="feuer_mannschaft_last_name"><strong>Nachname</strong></label>
        <input type="text" class="widefat" id="feuer_mannschaft_last_name" name="feuer_mannschaft_meta[last_name]" value="<?php echo esc_attr($meta['last_name']); ?>">
    </div>

    <div class="feuer-mannschaft-field">
        <label for="feuer_mannschaft_entry_date"><strong>Eintrittsjahr</strong></label>
        <input type="number" class="small-text" id="feuer_mannschaft_entry_date" name="feuer_mannschaft_meta[entry_date]" min="1900" max="<?php echo esc_attr($current_year); ?>" step="1" inputmode="numeric" placeholder="<?php echo esc_attr((string) wp_date('Y')); ?>" value="<?php echo esc_attr($entry_year); ?>">
        <p class="description">Bitte nur das Jahr eingeben.</p>
    </div>

    <?php foreach ($choice_groups as $group_id => $group) : ?>
        <?php
        $selected_values = isset($group['selected_values']) && is_array($group['selected_values']) ? $group['selected_values'] : array();
        $display_options = isset($group['display_options']) && is_array($group['display_options']) ? $group['display_options'] : array();
        ?>
        <div class="feuer-mannschaft-field feuer-mannschaft-field-full feuer-mannschaft-choice-panel">
            <label><strong><?php echo esc_html($group['label']); ?></strong></label>
            <?php if (! empty($group['description'])) : ?>
                <p class="description"><?php echo esc_html($group['description']); ?></p>
            <?php endif; ?>

            <?php if (! empty($display_options)) : ?>
                <div class="feuer-mannschaft-switch-list feuer-mannschaft-switch-list-wide" data-switch-list>
                    <?php foreach ($display_options as $index => $option) : ?>
                        <?php
                        $field_id = 'feuer_mannschaft_' . md5($group_id) . '_' . $index;
                        $option_label = isset($option['label']) ? (string) $option['label'] : '';
                        $option_url = isset($option['url']) ? (string) $option['url'] : '';
                        $is_selected = in_array($option_label, $selected_values, true);
                        ?>
                        <label class="form-check form-switch feuer-mannschaft-switch-item" for="<?php echo esc_attr($field_id); ?>">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                id="<?php echo esc_attr($field_id); ?>"
                                name="<?php echo esc_attr($group['field_name']); ?>"
                                value="<?php echo esc_attr($option_label); ?>"
                                <?php if (! empty($group['switch_group'])) : ?>
                                    data-switch-exclusive="<?php echo esc_attr($group['switch_group']); ?>"
                                <?php endif; ?>
                                <?php checked($is_selected); ?>
                            >
                            <span class="form-check-label">
                                <span class="feuer-mannschaft-switch-copy">
                                    <span>
                                        <?php echo esc_html($option_label); ?>
                                        <?php if (! empty($option['legacy'])) : ?>
                                            (gespeichert)
                                        <?php endif; ?>
                                    </span>
                                    <?php if ($option_url) : ?>
                                        <a href="<?php echo esc_url($option_url); ?>" target="_blank" rel="noopener noreferrer" class="feuer-mannschaft-admin-option-link">
                                            Link prüfen
                                        </a>
                                    <?php endif; ?>
                                </span>
                            </span>
                        </label>
                    <?php endforeach; ?>
                </div>
                <p class="description">
                    <?php if (! empty($group['multiple'])) : ?>
                        Mehrere Einträge können gleichzeitig aktiviert werden.
                    <?php else : ?>
                        Es kann genau ein Eintrag aktiviert werden.
                    <?php endif; ?>
                </p>
            <?php else : ?>
                <p class="description">Bitte zuerst unter <a href="<?php echo esc_url($settings_url); ?>">Mannschaft-Einstellungen</a> mindestens einen Eintrag für <?php echo esc_html($group['label']); ?> anlegen.</p>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>

    <?php if (! empty($enabled_contact_fields)) : ?>
        <div class="feuer-mannschaft-field feuer-mannschaft-field-full">
            <label><strong>Kontaktfelder</strong></label>
            <p class="description">Diese Angaben werden im Frontend als Kontakt-Buttons angezeigt.</p>
        </div>

        <?php foreach ($enabled_contact_fields as $field_key => $field_definition) : ?>
            <?php
            $field_label = isset($field_definition['label']) ? (string) $field_definition['label'] : $field_key;
            $field_description = isset($field_definition['description']) ? (string) $field_definition['description'] : '';
            $field_placeholder = isset($field_definition['placeholder']) ? (string) $field_definition['placeholder'] : '';
            $field_value = isset($contact_field_values[$field_key]) ? $contact_field_values[$field_key] : '';
            ?>

            <?php if ('link' === $field_key) : ?>
                <?php
                $link_value = is_array($field_value) ? $field_value : array();
                $link_label = isset($link_value['label']) ? (string) $link_value['label'] : '';
                $link_url   = isset($link_value['url']) ? (string) $link_value['url'] : '';
                ?>
                <div class="feuer-mannschaft-field feuer-mannschaft-field-full">
                    <label for="feuer_mannschaft_contact_<?php echo esc_attr($field_key); ?>_label"><strong><?php echo esc_html($field_label); ?></strong></label>
                    <input type="text" class="widefat" id="feuer_mannschaft_contact_<?php echo esc_attr($field_key); ?>_label" name="feuer_mannschaft_meta[contact_fields][<?php echo esc_attr($field_key); ?>][label]" value="<?php echo esc_attr($link_label); ?>" placeholder="<?php echo esc_attr($field_placeholder); ?>">
                    <input type="url" class="widefat" id="feuer_mannschaft_contact_<?php echo esc_attr($field_key); ?>_url" name="feuer_mannschaft_meta[contact_fields][<?php echo esc_attr($field_key); ?>][url]" value="<?php echo esc_attr($link_url); ?>" placeholder="<?php echo esc_attr(isset($field_definition['url_placeholder']) ? $field_definition['url_placeholder'] : 'https://example.de/'); ?>" style="margin-top:8px;">
                    <?php if ($field_description) : ?>
                        <p class="description"><?php echo esc_html($field_description); ?></p>
                    <?php endif; ?>
                </div>
            <?php else : ?>
                <div class="feuer-mannschaft-field">
                    <label for="feuer_mannschaft_contact_<?php echo esc_attr($field_key); ?>"><strong><?php echo esc_html($field_label); ?></strong></label>
                    <input type="<?php echo 'email' === $field_key ? 'email' : 'text'; ?>" class="widefat" id="feuer_mannschaft_contact_<?php echo esc_attr($field_key); ?>" name="feuer_mannschaft_meta[contact_fields][<?php echo esc_attr($field_key); ?>]" value="<?php echo esc_attr(is_array($field_value) ? '' : (string) $field_value); ?>" placeholder="<?php echo esc_attr($field_placeholder); ?>">
                    <?php if ($field_description) : ?>
                        <p class="description"><?php echo esc_html($field_description); ?></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>

    <?php if ($show_admin_note) : ?>
        <div class="feuer-mannschaft-field feuer-mannschaft-field-full feuer-mannschaft-admin-note-field">
            <label for="feuer_mannschaft_admin_note"><strong>Administrator-Hinweis</strong></label>
            <textarea class="widefat" id="feuer_mannschaft_admin_note" name="feuer_mannschaft_meta[admin_note]" rows="4" placeholder="Nur fuer Administratoren sichtbar"><?php echo esc_textarea(isset($meta['admin_note']) ? $meta['admin_note'] : ''); ?></textarea>
            <p class="description">Nur Administratoren sehen und bearbeiten dieses Feld. Im Frontend wird es ebenfalls nur Administratoren angezeigt.</p>
        </div>
    <?php endif; ?>

    <div class="feuer-mannschaft-field">
        <label for="feuer_mannschaft_sort_order"><strong>Position in der Liste</strong></label>
        <input type="number" class="small-text" id="feuer_mannschaft_sort_order" name="feuer_mannschaft_meta[sort_order]" min="0" step="1" value="<?php echo esc_attr($meta['sort_order']); ?>">
        <p class="description">Je kleiner der Wert, desto weiter oben erscheint das Profil in Mannschaftslisten.</p>
    </div>

    <div class="feuer-mannschaft-field feuer-mannschaft-field-full">
        <label for="feuer_mannschaft_profile_category"><strong>Kategorie</strong></label>
        <?php if (empty($choices)) : ?>
            <p class="description">Es wurde noch keine Kategorie freigegeben. Bitte zuerst unter <a href="<?php echo esc_url($settings_url); ?>">Mannschaft-Einstellungen</a> die erlaubten Kategorien wählen.</p>
        <?php else : ?>
            <select class="widefat" id="feuer_mannschaft_profile_category" name="feuer_mannschaft_profile_category">
                <?php foreach ($choices as $choice) : ?>
                    <?php
                    $term  = $choice['term'];
                    $depth = (int) $choice['depth'];
                    ?>
                    <option value="<?php echo esc_attr($term->term_id); ?>" <?php selected((int) $term->term_id, $category_id); ?>>
                        <?php echo esc_html(str_repeat('- ', $depth) . $term->name); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <p class="description">Das Profil wird als normaler WordPress-Beitrag in genau dieser freigegebenen Kategorie gespeichert.</p>
        <?php endif; ?>
    </div>

    <div class="feuer-mannschaft-field feuer-mannschaft-field-full">
        <label for="feuer_mannschaft_custom_link"><strong>Eigener Link</strong></label>
        <input type="url" class="widefat" id="feuer_mannschaft_custom_link" name="feuer_mannschaft_custom_link" value="<?php echo esc_attr($custom_link); ?>" placeholder="https://example.de/mitglied-werden/">
        <p class="description">Optional. Wenn hier eine Adresse hinterlegt ist, verweisen Karten und Buttons auf diesen Link statt auf die Profilseite. Interne Pfade wie <code>/mitglied-werden/</code> sind ebenfalls möglich.</p>
        <label class="form-check feuer-mannschaft-inline-check" for="feuer_mannschaft_exclude_from_count">
            <input type="checkbox" id="feuer_mannschaft_exclude_from_count" name="feuer_mannschaft_exclude_from_count" value="1" <?php checked($exclude_from_count); ?>>
            <span><strong>Im Gesamtzähler nicht mitzählen</strong></span>
        </label>
        <p class="description">Aktivieren Sie diese Option für Karten mit eigenem Link oder Platzhalter-Einträge, die nicht in der allgemeinen Anzahl der Teilnehmer erscheinen sollen.</p>
    </div>
</div>

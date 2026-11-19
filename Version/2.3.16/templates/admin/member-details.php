<?php

if (! defined('ABSPATH')) {
    exit;
}

$rank_items = $rank_options;
if ($legacy_rank_value) {
    array_unshift($rank_items, $legacy_rank_value);
    $rank_items = array_values(array_unique($rank_items));
}

$function_items  = array_values(array_unique(array_merge($legacy_function_values, $function_options)));
$education_items = array_values(array_unique(array_merge($legacy_education_values, $education_options)));
?>
<input type="hidden" name="feuer_mannschaft_profile" value="1">

<div class="feuer-mannschaft-admin-grid">
    <p class="description feuer-mannschaft-field-full">Pflegen Sie hier das Feuer-Mannschaft-Profil. Der Titel wird automatisch aus Vorname und Nachname erzeugt. Wenn unten ein eigener Link hinterlegt ist, verwenden Karten und Buttons diesen Link statt der Profilseite.</p>

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

    <div class="feuer-mannschaft-field feuer-mannschaft-field-full feuer-mannschaft-choice-panel">
        <label><strong>Dienstgrad</strong></label>
        <?php if (! empty($rank_items)) : ?>
            <div class="feuer-mannschaft-switch-list feuer-mannschaft-switch-list-wide" data-switch-list>
                <?php foreach ($rank_items as $index => $option) : ?>
                    <?php $field_id = 'feuer_mannschaft_rank_' . $index; ?>
                    <label class="form-check form-switch feuer-mannschaft-switch-item" for="<?php echo esc_attr($field_id); ?>">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            id="<?php echo esc_attr($field_id); ?>"
                            name="feuer_mannschaft_meta[rank][]"
                            value="<?php echo esc_attr($option); ?>"
                            data-switch-exclusive="feuer-mannschaft-rank"
                            <?php checked($rank_value, $option); ?>
                        >
                        <span class="form-check-label">
                            <?php echo esc_html($option); ?>
                            <?php if ($legacy_rank_value && $legacy_rank_value === $option) : ?>
                                (gespeichert)
                            <?php endif; ?>
                        </span>
                    </label>
                <?php endforeach; ?>
            </div>
            <p class="description">Es kann genau ein Dienstgrad aktiviert werden.</p>
        <?php else : ?>
            <p class="description">Bitte zuerst unter <a href="<?php echo esc_url($settings_url); ?>">Feuer-Mannschaft-Einstellungen</a> mindestens einen Dienstgrad anlegen.</p>
        <?php endif; ?>
    </div>

    <div class="feuer-mannschaft-field feuer-mannschaft-field-full feuer-mannschaft-choice-panel">
        <label><strong>Funktion</strong></label>
        <?php if (! empty($function_items)) : ?>
            <div class="feuer-mannschaft-switch-list feuer-mannschaft-switch-list-wide" data-switch-list>
                <?php foreach ($function_items as $index => $option) : ?>
                    <?php $field_id = 'feuer_mannschaft_function_' . $index; ?>
                    <label class="form-check form-switch feuer-mannschaft-switch-item" for="<?php echo esc_attr($field_id); ?>">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            id="<?php echo esc_attr($field_id); ?>"
                            name="feuer_mannschaft_meta[function][]"
                            value="<?php echo esc_attr($option); ?>"
                            <?php checked(in_array($option, $function_values, true)); ?>
                        >
                        <span class="form-check-label">
                            <?php echo esc_html($option); ?>
                            <?php if (in_array($option, $legacy_function_values, true)) : ?>
                                (gespeichert)
                            <?php endif; ?>
                        </span>
                    </label>
                <?php endforeach; ?>
            </div>
            <p class="description">Mehrere Funktionen können gleichzeitig aktiviert werden.</p>
        <?php else : ?>
            <p class="description">Bitte zuerst unter <a href="<?php echo esc_url($settings_url); ?>">Feuer-Mannschaft-Einstellungen</a> mindestens eine Funktion anlegen.</p>
        <?php endif; ?>
    </div>

    <div class="feuer-mannschaft-field feuer-mannschaft-field-full feuer-mannschaft-choice-panel">
        <label><strong>Ausbildung</strong></label>
        <?php if (! empty($education_items)) : ?>
            <div class="feuer-mannschaft-switch-list feuer-mannschaft-switch-list-wide" data-switch-list>
                <?php foreach ($education_items as $index => $option) : ?>
                    <?php $field_id = 'feuer_mannschaft_education_' . $index; ?>
                    <label class="form-check form-switch feuer-mannschaft-switch-item" for="<?php echo esc_attr($field_id); ?>">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            id="<?php echo esc_attr($field_id); ?>"
                            name="feuer_mannschaft_meta[education][]"
                            value="<?php echo esc_attr($option); ?>"
                            <?php checked(in_array($option, $education_values, true)); ?>
                        >
                        <span class="form-check-label">
                            <?php echo esc_html($option); ?>
                            <?php if (in_array($option, $legacy_education_values, true)) : ?>
                                (gespeichert)
                            <?php endif; ?>
                        </span>
                    </label>
                <?php endforeach; ?>
            </div>
            <p class="description">Mehrere Ausbildungen können gleichzeitig aktiviert werden.</p>
        <?php else : ?>
            <p class="description">Bitte zuerst unter <a href="<?php echo esc_url($settings_url); ?>">Feuer-Mannschaft-Einstellungen</a> mindestens eine Ausbildung anlegen.</p>
        <?php endif; ?>
    </div>

    <div class="feuer-mannschaft-field">
        <label for="feuer_mannschaft_sort_order"><strong>Position in der Liste</strong></label>
        <input type="number" class="small-text" id="feuer_mannschaft_sort_order" name="feuer_mannschaft_meta[sort_order]" min="0" step="1" value="<?php echo esc_attr($meta['sort_order']); ?>">
        <p class="description">Je kleiner der Wert, desto weiter oben erscheint das Profil in Mannschaftslisten.</p>
    </div>

    <div class="feuer-mannschaft-field feuer-mannschaft-field-full">
        <label for="feuer_mannschaft_profile_category"><strong>Kategorie</strong></label>
        <?php if (empty($choices)) : ?>
            <p class="description">Es wurde noch keine Kategorie freigegeben. Bitte zuerst unter <a href="<?php echo esc_url($settings_url); ?>">Feuer-Mannschaft-Einstellungen</a> die erlaubten Kategorien wählen.</p>
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

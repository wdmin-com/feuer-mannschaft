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
$profile_slug = isset($profile_slug) ? (string) $profile_slug : '';
?>
<input type="hidden" name="feuer_mannschaft_profile" value="1">

<?php echo $this->render_admin_template('member-preview', get_defined_vars()); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

<div class="feuer-mannschaft-admin-grid">
    <section class="feuer-profile-section feuer-profile-section-basics">
        <header class="feuer-profile-section-header">
            <span class="feuer-profile-step">01</span>
            <div><h3>Stammdaten</h3><p>Beginnen Sie mit Name, Profiladresse und Eintrittsjahr.</p></div>
        </header>
        <div class="feuer-profile-fields-grid">

    <div class="feuer-mannschaft-field">
        <label for="feuer_mannschaft_first_name"><strong>Vorname</strong></label>
        <input type="text" class="widefat" id="feuer_mannschaft_first_name" name="feuer_mannschaft_meta[first_name]" value="<?php echo esc_attr($meta['first_name']); ?>">
    </div>

    <div class="feuer-mannschaft-field">
        <label for="feuer_mannschaft_last_name"><strong>Nachname</strong></label>
        <input type="text" class="widefat" id="feuer_mannschaft_last_name" name="feuer_mannschaft_meta[last_name]" value="<?php echo esc_attr($meta['last_name']); ?>">
    </div>

    <div class="feuer-mannschaft-field feuer-mannschaft-field-full">
        <label for="feuer_mannschaft_slug"><strong>Profil-URL</strong></label>
        <div class="feuer-mannschaft-slug-field">
            <span><?php echo esc_html(trailingslashit(home_url('/mannschaft/'))); ?></span>
            <input type="text" class="widefat" id="feuer_mannschaft_slug" name="feuer_mannschaft_slug" value="<?php echo esc_attr($profile_slug); ?>" autocomplete="off" aria-describedby="feuer_mannschaft_slug_help">
        </div>
        <p class="description" id="feuer_mannschaft_slug_help">Geben Sie nur den Teil der Adresse nach <code>/mannschaft/</code> ein. Bei einer Änderung wird die bisherige Adresse auf die neue weitergeleitet.</p>
    </div>

    <div class="feuer-mannschaft-field">
        <label for="feuer_mannschaft_entry_date"><strong>Eintrittsjahr</strong></label>
        <input type="number" class="small-text" id="feuer_mannschaft_entry_date" name="feuer_mannschaft_meta[entry_date]" min="1900" max="<?php echo esc_attr($current_year); ?>" step="1" inputmode="numeric" placeholder="<?php echo esc_attr((string) wp_date('Y')); ?>" value="<?php echo esc_attr($entry_year); ?>">
        <p class="description">Bitte nur das Jahr eingeben.</p>
    </div>

    <div class="feuer-mannschaft-field feuer-mannschaft-field-full feuer-profile-description-field">
        <label for="content"><strong>Profilbeschreibung</strong></label>
        <?php
        wp_editor(
            (string) $meta['description'],
            'content',
            array(
                'textarea_name' => 'content',
                'textarea_rows' => 5,
                'media_buttons' => false,
                'teeny'         => true,
                'quicktags'     => false,
                'tinymce'       => array('toolbar1' => 'bold,italic,bullist,numlist,link,unlink,undo,redo', 'toolbar2' => ''),
            )
        );
        ?>
        <p class="description">Optionaler Text, der im öffentlichen Profil angezeigt wird.</p>
    </div>
        </div>
    </section>

    <section class="feuer-profile-section feuer-profile-section-qualifications">
        <header class="feuer-profile-section-header">
            <span class="feuer-profile-step">02</span>
            <div><h3>Dienst und Qualifikationen</h3><p>Wählen Sie Dienstgrad, Funktion und absolvierte Ausbildungen.</p></div>
        </header>
        <div class="feuer-profile-choice-grid">
    <?php foreach ($choice_groups as $group_id => $group) : ?>
        <?php
        $selected_values = isset($group['selected_values']) && is_array($group['selected_values']) ? $group['selected_values'] : array();
        $display_options = isset($group['display_options']) && is_array($group['display_options']) ? $group['display_options'] : array();
        ?>
        <div class="feuer-mannschaft-field feuer-mannschaft-choice-panel">
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
                        <label class="feuer-mannschaft-profile-switch-item" for="<?php echo esc_attr($field_id); ?>">
                            <input
                                class="feuer-mannschaft-profile-switch-input"
                                type="checkbox"
                                id="<?php echo esc_attr($field_id); ?>"
                                name="<?php echo esc_attr($group['field_name']); ?>"
                                value="<?php echo esc_attr($option_label); ?>"
                                <?php if (! empty($group['switch_group'])) : ?>
                                    data-switch-exclusive="<?php echo esc_attr($group['switch_group']); ?>"
                                <?php endif; ?>
                                <?php checked($is_selected); ?>
                            >
                            <span class="feuer-mannschaft-profile-switch-track" aria-hidden="true"></span>
                            <span class="feuer-mannschaft-profile-switch-label">
                                <span><?php echo esc_html($option_label); ?><?php if (! empty($option['legacy'])) : ?> <span class="feuer-mannschaft-profile-switch-legacy">(gespeichert)</span><?php endif; ?></span>
                                <?php if ($option_url) : ?>
                                    <a href="<?php echo esc_url($option_url); ?>" target="_blank" rel="noopener noreferrer" class="feuer-mannschaft-admin-option-link">Link prüfen</a>
                                <?php endif; ?>
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
        </div>
    </section>

    <section class="feuer-profile-section feuer-profile-section-extra-training">
        <div class="feuer-mannschaft-field feuer-mannschaft-field-full">
        <label for="feuer_mannschaft_lehrgang"><strong>Weitere Lehrgänge</strong></label>
        <textarea class="widefat" id="feuer_mannschaft_lehrgang" name="feuer_mannschaft_meta[lehrgang]" rows="4" placeholder="Zum Beispiel: Leiter einer Feuerwehr"><?php echo esc_textarea(isset($meta['lehrgang']) ? $meta['lehrgang'] : ''); ?></textarea>
        <p class="description">Zusätzliche Lehrgänge, die nicht in der Auswahlliste stehen. Jeden Eintrag in eine eigene Zeile schreiben.</p>
        </div>
    </section>

    <?php if (! empty($enabled_contact_fields)) : ?>
        <details class="feuer-profile-section feuer-profile-optional-section">
            <summary><span class="feuer-profile-step">03</span><span><strong>Kontaktangaben</strong><small>Nur ausfüllen, wenn Kontaktdaten öffentlich angezeigt werden sollen.</small></span></summary>
            <div class="feuer-profile-fields-grid">

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
            </div>
        </details>
    <?php endif; ?>

    <?php if ($show_admin_note) : ?>
        <div class="feuer-mannschaft-field feuer-mannschaft-field-full feuer-mannschaft-admin-note-field">
            <details class="feuer-mannschaft-admin-note-toggle">
                <summary class="feuer-mannschaft-admin-note-summary">
                    <span class="feuer-mannschaft-admin-note-summary-label">Administrator-Hinweis</span>
                    <span class="feuer-mannschaft-admin-note-summary-hint">Nur fuer Administratoren sichtbar</span>
                </summary>
                <div class="feuer-mannschaft-admin-note-panel">
                    <label for="feuer_mannschaft_admin_note"><strong>Interne Notiz</strong></label>
                    <textarea class="widefat" id="feuer_mannschaft_admin_note" name="feuer_mannschaft_meta[admin_note]" rows="4" placeholder="Nur fuer Administratoren sichtbar"><?php echo esc_textarea(isset($meta['admin_note']) ? $meta['admin_note'] : ''); ?></textarea>
                    <p class="description">Nur Administratoren sehen und bearbeiten dieses Feld. Im Frontend wird es ebenfalls nur Administratoren angezeigt.</p>
                </div>
            </details>
        </div>
    <?php endif; ?>

    <section class="feuer-profile-section feuer-profile-section-publishing">
        <header class="feuer-profile-section-header">
            <span class="feuer-profile-step">04</span>
            <div><h3>Zuordnung und Darstellung</h3><p>Ordnen Sie das Profil ein und legen Sie optionale Links fest.</p></div>
        </header>
        <div class="feuer-profile-fields-grid">
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
    </section>

    <details class="feuer-profile-section feuer-profile-optional-section feuer-profile-archive-section">
        <summary><span class="feuer-profile-step">05</span><span><strong>Archivangaben</strong><small>Nur relevant, wenn ein Mitglied die aktive Mannschaft verlässt.</small></span></summary>
        <div class="feuer-profile-fields-grid">
    <div class="feuer-mannschaft-field">
        <label for="feuer_mannschaft_service_end_date"><strong>Dienstende</strong></label>
        <input type="date" class="widefat" id="feuer_mannschaft_service_end_date" name="feuer_mannschaft_service_end_date" value="<?php echo esc_attr($service_end_date); ?>">
        <p class="description">Optional. Wird auf der Seite der ehemaligen Mitglieder angezeigt.</p>
    </div>

    <div class="feuer-mannschaft-field">
        <label for="feuer_mannschaft_archive_reason"><strong>Archivgrund</strong></label>
        <textarea class="widefat" id="feuer_mannschaft_archive_reason" name="feuer_mannschaft_archive_reason" rows="3" placeholder="Zum Beispiel: Wechsel in die Ehrenabteilung"><?php echo esc_textarea($archive_reason); ?></textarea>
        <p class="description">Kontaktangaben werden in der öffentlichen Archivansicht grundsätzlich nicht ausgegeben.</p>
    </div>
        </div>
    </details>
</div>

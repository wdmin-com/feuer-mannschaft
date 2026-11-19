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
$qualification_expiries = isset($qualification_expiries) && is_array($qualification_expiries) ? array_values($qualification_expiries) : array();
if (empty($qualification_expiries)) {
    $qualification_expiries[] = array('title' => '', 'expires_on' => '');
}
$profile_reviewed_on = isset($profile_reviewed_on) ? (string) $profile_reviewed_on : '';
$wizard_is_new = ! isset($post->ID) || ! $post->ID || 'auto-draft' === $post->post_status;
?>
<input type="hidden" name="feuer_mannschaft_profile" value="1">

<div class="feuer-mannschaft-admin-grid">
<div class="feuer-profile-wizard feuer-profile-editor">
    <header class="feuer-profile-wizard-header">
        <div class="feuer-profile-wizard-heading">
            <span class="feuer-profile-wizard-kicker">MITGLIEDERPROFIL</span>
            <h2><?php echo esc_html($wizard_is_new ? 'Neues Teilnehmerprofil' : 'Teilnehmerprofil bearbeiten'); ?></h2>
            <p>Füllen Sie die Pflichtangaben aus und speichern Sie das Profil rechts im Kasten „Veröffentlichen“. Alle Bereiche dieser Seite sind jederzeit erreichbar.</p>
        </div>
        <div class="feuer-profile-editor-guide" role="note"><strong>So funktioniert es</strong><span>1. Name und Foto eingeben</span><span>2. Dienstgrad, Funktion und Kategorie wählen</span><span>3. Als Entwurf speichern oder veröffentlichen</span></div>
        <nav class="feuer-profile-wizard-nav" aria-label="Zu den Profilbereichen">
            <ol>
                <li><a href="#feuer-wizard-step-title-0">Person &amp; Foto</a></li>
                <li><a href="#feuer-wizard-step-title-1">Beschreibung</a></li>
                <li><a href="#feuer-wizard-step-title-2">Dienst &amp; Funktion</a></li>
                <li><a href="#feuer-wizard-step-title-3">Weitere Angaben</a></li>
                <li><a href="#feuer-wizard-step-title-4">Kategorie</a></li>
            </ol>
        </nav>
    </header>

    <div class="feuer-profile-wizard-panels">
    <section class="feuer-profile-wizard-panel is-active" data-wizard-panel="0" aria-labelledby="feuer-wizard-step-title-0">
        <header class="feuer-profile-wizard-panel-header">
            <span>Pflichtangaben</span>
            <h3 id="feuer-wizard-step-title-0">Foto und persönliche Daten</h3>
            <p>Laden Sie zuerst ein Foto hoch und tragen Sie den Namen des Mitglieds ein.</p>
        </header>
        <div class="feuer-profile-wizard-photo">
            <h4>Profilfoto <small>für die Veröffentlichung erforderlich</small></h4>
            <?php $this->render_gallery_meta_box($post); ?>
        </div>
    <section class="feuer-profile-section feuer-profile-section-basics">
        <header class="feuer-profile-section-header">
            <div><h3><span class="dashicons dashicons-admin-users feuer-admin-section-icon" aria-hidden="true"></span>Stammdaten</h3><p>Beginnen Sie mit Name, Profiladresse und Eintrittsjahr.</p></div>
        </header>
        <div class="feuer-profile-fields-grid">

    <div class="feuer-mannschaft-field">
        <label for="feuer_mannschaft_first_name"><strong>Vorname <span class="feuer-required-marker">Pflichtfeld</span></strong></label>
        <input type="text" class="widefat" id="feuer_mannschaft_first_name" name="feuer_mannschaft_meta[first_name]" value="<?php echo esc_attr($meta['first_name']); ?>" autocomplete="given-name" data-wizard-required="true" aria-required="true">
    </div>

    <div class="feuer-mannschaft-field">
        <label for="feuer_mannschaft_last_name"><strong>Nachname <span class="feuer-required-marker">Pflichtfeld</span></strong></label>
        <input type="text" class="widefat" id="feuer_mannschaft_last_name" name="feuer_mannschaft_meta[last_name]" value="<?php echo esc_attr($meta['last_name']); ?>" autocomplete="family-name" data-wizard-required="true" aria-required="true">
    </div>

    <div class="feuer-mannschaft-field feuer-mannschaft-field-full">
        <label for="feuer_mannschaft_slug"><strong>Profil-URL</strong></label>
        <div class="feuer-mannschaft-slug-field">
            <span><?php echo esc_html(trailingslashit(home_url('/mannschaft/'))); ?></span>
            <input type="text" class="widefat" id="feuer_mannschaft_slug" name="feuer_mannschaft_slug" value="<?php echo esc_attr($profile_slug); ?>" autocomplete="off" aria-describedby="feuer_mannschaft_slug_help">
        </div>
        <p class="description" id="feuer_mannschaft_slug_help">Die Adresse wird aus Vor- und Nachname erstellt. Sie können sie hier jederzeit manuell anpassen. Bei einer Änderung wird die bisherige Adresse weitergeleitet.</p>
    </div>

    <div class="feuer-mannschaft-field">
        <label for="feuer_mannschaft_entry_date"><strong>Eintrittsjahr</strong></label>
        <input type="number" class="small-text" id="feuer_mannschaft_entry_date" name="feuer_mannschaft_meta[entry_date]" min="1900" max="<?php echo esc_attr($current_year); ?>" step="1" inputmode="numeric" placeholder="<?php echo esc_attr((string) wp_date('Y')); ?>" value="<?php echo esc_attr($entry_year); ?>">
        <p class="description">Bitte nur das Jahr eingeben.</p>
    </div>

        </div>
    </section>
        <?php echo $this->render_admin_template('member-preview', get_defined_vars()); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
    </section>

    <section class="feuer-profile-wizard-panel" data-wizard-panel="1" aria-labelledby="feuer-wizard-step-title-1">
        <header class="feuer-profile-wizard-panel-header">
            <span>Optional</span>
            <h3 id="feuer-wizard-step-title-1">Profilbeschreibung</h3>
            <p>Beschreiben Sie kurz die Aufgaben, Erfahrungen oder den Werdegang des Mitglieds.</p>
        </header>
    <?php $this->render_profile_description_editor($post); ?>
    </section>

    <section class="feuer-profile-wizard-panel" data-wizard-panel="2" aria-labelledby="feuer-wizard-step-title-2">
        <header class="feuer-profile-wizard-panel-header">
            <span>Pflichtangaben und Qualifikationen</span>
            <h3 id="feuer-wizard-step-title-2">Dienstgrad, Funktion und Qualifikationen</h3>
            <p>Wählen Sie die passenden Angaben aus. Mehrfachauswahl ist möglich, wenn sie angezeigt wird.</p>
        </header>
    <details class="feuer-profile-section feuer-profile-optional-section feuer-profile-section-qualifications" open>
        <summary><span class="dashicons dashicons-awards feuer-admin-section-icon" aria-hidden="true"></span><span><strong>Dienst und Qualifikationen</strong><small>Dienstgrad, Funktionen und absolvierte Ausbildungen</small></span></summary>
        <div class="feuer-profile-choice-grid">
    <?php $group_icons = array('rank_options' => 'dashicons-awards', 'function_options' => 'dashicons-admin-users', 'education_options' => 'dashicons-welcome-learn-more'); ?>
    <?php foreach ($choice_groups as $group_id => $group) : ?>
        <?php
        $selected_values = isset($group['selected_values']) && is_array($group['selected_values']) ? $group['selected_values'] : array();
        $display_options = isset($group['display_options']) && is_array($group['display_options']) ? $group['display_options'] : array();
        $group_icon = isset($group_icons[$group_id]) ? $group_icons[$group_id] : 'dashicons-category';
        ?>
        <div class="feuer-mannschaft-field feuer-mannschaft-choice-panel">
            <label><span class="dashicons <?php echo esc_attr($group_icon); ?> feuer-admin-section-icon" aria-hidden="true"></span><strong><?php echo esc_html($group['label']); ?></strong></label>
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
    </details>
    </section>

    <section class="feuer-profile-wizard-panel" data-wizard-panel="3" aria-labelledby="feuer-wizard-step-title-3">
        <header class="feuer-profile-wizard-panel-header">
            <span>Optional</span>
            <h3 id="feuer-wizard-step-title-3">Weitere Angaben</h3>
            <p>Ergänzen Sie weitere Lehrgänge oder freigegebene Kontaktdaten. Diese Angaben sind optional.</p>
        </header>
    <details class="feuer-profile-section feuer-profile-optional-section feuer-profile-section-extra-training" open>
        <summary><span class="dashicons dashicons-welcome-learn-more feuer-admin-section-icon" aria-hidden="true"></span><span><strong>Weitere Lehrgänge</strong><small>Zusätzliche Qualifikationen, je Eintrag eine Zeile</small></span></summary>
        <div class="feuer-profile-collapsible-content">
            <div class="feuer-mannschaft-field feuer-mannschaft-field-full">
                <label for="feuer_mannschaft_lehrgang"><strong>Lehrgänge</strong></label>
                <textarea class="widefat" id="feuer_mannschaft_lehrgang" name="feuer_mannschaft_meta[lehrgang]" rows="3" placeholder="Zum Beispiel: Leiter einer Feuerwehr"><?php echo esc_textarea(isset($meta['lehrgang']) ? $meta['lehrgang'] : ''); ?></textarea>
            </div>
            <fieldset class="feuer-mannschaft-field feuer-mannschaft-field-full feuer-qualification-expiries" data-qualification-expiries>
                <input type="hidden" name="feuer_mannschaft_qualification_expiries_submitted" value="1">
                <legend><strong>Qualifikationen mit Ablaufdatum</strong></legend>
                <p class="description">Optional. Lassen Sie „Gültig bis“ leer, wenn die Qualifikation unbefristet ist. Für befristete Nachweise erfolgt eine Erinnerung 30 Tage vor Ablauf.</p>
                <div class="feuer-qualification-expiry-rows" data-expiry-rows>
                    <?php foreach ($qualification_expiries as $index => $qualification) : ?>
                        <div class="feuer-qualification-expiry-row" data-expiry-row>
                            <label><span>Qualifikation</span><input type="text" class="regular-text" name="feuer_mannschaft_qualification_expiries[<?php echo esc_attr((string) $index); ?>][title]" value="<?php echo esc_attr(isset($qualification['title']) ? $qualification['title'] : ''); ?>"></label>
                            <label><span>Gültig bis (optional)</span><input type="date" name="feuer_mannschaft_qualification_expiries[<?php echo esc_attr((string) $index); ?>][expires_on]" value="<?php echo esc_attr(isset($qualification['expires_on']) ? $qualification['expires_on'] : ''); ?>"></label>
                            <button type="button" class="button-link-delete" data-expiry-remove>Eintrag entfernen</button>
                        </div>
                    <?php endforeach; ?>
                </div>
                <template data-expiry-template>
                    <div class="feuer-qualification-expiry-row" data-expiry-row>
                        <label><span>Qualifikation</span><input type="text" class="regular-text" name="feuer_mannschaft_qualification_expiries[__INDEX__][title]"></label>
                        <label><span>Gültig bis (optional)</span><input type="date" name="feuer_mannschaft_qualification_expiries[__INDEX__][expires_on]"></label>
                        <button type="button" class="button-link-delete" data-expiry-remove>Eintrag entfernen</button>
                    </div>
                </template>
                <button type="button" class="button" data-expiry-add>Qualifikation hinzufügen</button>
            </fieldset>
        </div>
    </details>

    <?php if (! empty($enabled_contact_fields)) : ?>
        <details class="feuer-profile-section feuer-profile-optional-section" open>
            <summary><span class="dashicons dashicons-email-alt feuer-admin-section-icon" aria-hidden="true"></span><span><strong>Kontaktangaben</strong><small>Nur ausfüllen, wenn Kontaktdaten öffentlich angezeigt werden sollen.</small></span></summary>
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
                    <span class="dashicons dashicons-lock feuer-admin-section-icon" aria-hidden="true"></span>
                    <span class="feuer-mannschaft-admin-note-summary-label">Administrator-Hinweis</span>
                    <span class="feuer-mannschaft-admin-note-summary-hint">Nur für Administratoren sichtbar</span>
                </summary>
                <div class="feuer-mannschaft-admin-note-panel">
                    <label for="feuer_mannschaft_admin_note"><strong>Interne Notiz</strong></label>
                    <textarea class="widefat" id="feuer_mannschaft_admin_note" name="feuer_mannschaft_meta[admin_note]" rows="4" placeholder="Nur für Administratoren sichtbar"><?php echo esc_textarea(isset($meta['admin_note']) ? $meta['admin_note'] : ''); ?></textarea>
                    <p class="description">Nur Administratoren sehen und bearbeiten dieses Feld. Im Frontend wird es ebenfalls nur Administratoren angezeigt.</p>
                </div>
            </details>
        </div>
    <?php endif; ?>

    </section>

    <section class="feuer-profile-wizard-panel" data-wizard-panel="4" aria-labelledby="feuer-wizard-step-title-4">
        <header class="feuer-profile-wizard-panel-header">
            <span>Kategorie erforderlich</span>
            <h3 id="feuer-wizard-step-title-4">Zuordnung und Veröffentlichung</h3>
            <p>Ordnen Sie das Profil einer Mannschaft zu und prüfen Sie die Veröffentlichungseinstellungen.</p>
        </header>
    <section class="feuer-profile-review-status">
        <div>
            <strong>Jährliche Datenprüfung</strong>
            <p><?php echo $profile_reviewed_on ? 'Zuletzt bestätigt am ' . esc_html(wp_date('d.m.Y', strtotime($profile_reviewed_on))) . '.' : 'Für dieses Profil wurde noch keine jährliche Bestätigung gespeichert.'; ?></p>
        </div>
        <?php if (! $wizard_is_new && current_user_can('edit_post', $post->ID)) : ?>
            <a class="button" href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=feuer_complete_confirm_member_review&member_id=' . absint($post->ID)), 'feuer_complete_confirm_member_review_' . absint($post->ID))); ?>">Daten als aktuell bestätigen</a>
        <?php endif; ?>
    </section>
    <details class="feuer-profile-section feuer-profile-optional-section feuer-profile-section-publishing" open>
        <summary><span class="dashicons dashicons-admin-generic feuer-admin-section-icon" aria-hidden="true"></span><span><strong>Zuordnung und Darstellung</strong><small>Kategorie, Listenposition und optionaler Link</small></span></summary>
        <div class="feuer-profile-fields-grid">
    <div class="feuer-mannschaft-field">
        <label for="feuer_mannschaft_sort_order"><strong>Position in der Liste</strong></label>
        <input type="number" class="small-text" id="feuer_mannschaft_sort_order" name="feuer_mannschaft_meta[sort_order]" min="0" step="1" value="<?php echo esc_attr($meta['sort_order']); ?>">
        <p class="description">Je kleiner der Wert, desto weiter oben erscheint das Profil in Mannschaftslisten.</p>
    </div>

    <div class="feuer-mannschaft-field feuer-mannschaft-field-full">
        <label for="feuer_mannschaft_profile_category"><strong>Kategorie <span class="feuer-required-marker">Pflichtfeld</span></strong></label>
        <?php if (empty($choices)) : ?>
            <p class="description">Es wurde noch keine Kategorie freigegeben. Bitte zuerst unter <a href="<?php echo esc_url($settings_url); ?>">Mannschaft-Einstellungen</a> die erlaubten Kategorien wählen.</p>
        <?php else : ?>
            <select class="widefat" id="feuer_mannschaft_profile_category" name="feuer_mannschaft_profile_category">
                <option value="0" <?php selected(0, $category_id); ?>>Bitte Kategorie auswählen</option>
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
            <p class="description">Das Profil bleibt im geschützten Inhaltstyp „Mannschaft“ und wird genau dieser freigegebenen Kategorie zugeordnet.</p>
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
    </details>

    <details class="feuer-profile-section feuer-profile-optional-section feuer-profile-archive-section">
        <summary><span class="dashicons dashicons-archive feuer-admin-section-icon" aria-hidden="true"></span><span><strong>Archivangaben</strong><small>Nur relevant, wenn ein Mitglied die aktive Mannschaft verlässt.</small></span></summary>
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
    </section>
    </div>

    <footer class="feuer-profile-editor-footer"><strong>Angaben vollständig?</strong><span>Wählen Sie im Bereich „Mitgliedsstatus“ die Phase und speichern Sie anschließend rechts mit der WordPress-Schaltfläche.</span><a class="button button-primary" href="#submitdiv">Zu den Speicheraktionen</a></footer>
</div>
</div>

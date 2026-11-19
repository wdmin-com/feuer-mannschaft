<?php

if (! defined('ABSPATH')) {
    exit;
}

$default_profile_image_id = isset($settings['default_profile_image_id']) ? absint($settings['default_profile_image_id']) : 0;
$default_profile_preview  = $default_profile_image_id
    ? wp_get_attachment_image($default_profile_image_id, array(240, 240), false, array('class' => 'feuer-mannschaft-default-image-preview-image'))
    : '';
$card_styles = array(
    'classic'            => 'Klassisch',
    'minimal'            => 'Minimal',
    'dark'               => 'Dunkel',
    'official'           => 'Offiziell',
    'portrait-light'     => 'Klassische helle Porträt-Karte',
    'compact-horizontal' => 'Kompakte Horizontal-Karte',
    'editorial'           => 'Editorial-Karte',
);
?>
<div class="feuer-settings-intro">
    <p>Lege fest, wie Mannschaftslisten und Profile standardmäßig auf der Website aussehen. Die Vorschauen reagieren direkt auf Änderungen.</p>
    <span class="feuer-settings-intro-badge">Änderungen werden nach dem Speichern veröffentlicht</span>
</div>

<section class="feuer-mannschaft-settings-section feuer-settings-section">
    <header class="feuer-settings-section-heading">
        <div><span class="feuer-settings-kicker">01 · Grundlagen</span><h2>Allgemeine Darstellung</h2></div>
        <p>Standardinhalte für Profile und die Auswahl im Redaktionsbereich.</p>
    </header>
    <div class="feuer-settings-grid feuer-settings-grid-two">
        <div class="feuer-settings-field">
            <label for="feuer_mannschaft_watermark_text">Watermark</label>
            <input type="text" class="regular-text" id="feuer_mannschaft_watermark_text" name="<?php echo esc_attr(Feuer_Mannschaft_Plugin::OPTION_KEY); ?>[watermark_text]" value="<?php echo esc_attr($settings['watermark_text']); ?>">
            <p class="description">Text, der beim Erstellen eines Profilbildes nach Vor- und Nachname eingeblendet wird.</p>
        </div>
        <div class="feuer-settings-field">
            <label>Standard-Profilfoto</label>
            <div class="feuer-mannschaft-default-image-picker" data-feuer-mannschaft-default-image-picker>
                <input type="hidden" id="feuer_mannschaft_default_profile_image_id" name="<?php echo esc_attr(Feuer_Mannschaft_Plugin::OPTION_KEY); ?>[default_profile_image_id]" value="<?php echo esc_attr($default_profile_image_id); ?>" data-default-image-input>
                <div class="feuer-mannschaft-default-image-preview" data-default-image-preview>
                    <?php if ($default_profile_preview) : ?>
                        <?php echo $default_profile_preview; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    <?php else : ?>
                        <p class="description">Noch kein Standardfoto ausgewählt.</p>
                    <?php endif; ?>
                </div>
                <div class="feuer-mannschaft-default-image-actions">
                    <button type="button" class="button" data-default-image-select><?php echo $default_profile_image_id ? 'Foto ersetzen' : 'Foto auswählen'; ?></button>
                    <button type="button" class="button button-link-delete" data-default-image-remove <?php disabled($default_profile_image_id, 0); ?>>Entfernen</button>
                </div>
                <p class="description">Wird verwendet, wenn ein Profil kein eigenes Foto hat.</p>
            </div>
        </div>
    </div>

    <div class="feuer-settings-field feuer-settings-field-wide">
        <label>Freigegebene Kategorien</label>
        <p class="description">Diese Kategorien können Redakteure Mannschaftsprofilen zuordnen.</p>
        <?php if (empty($choices)) : ?>
            <p>Es wurden noch keine WordPress-Kategorien gefunden.</p>
        <?php else : ?>
            <div class="feuer-mannschaft-settings-category-list">
                <?php foreach ($choices as $choice) : ?>
                    <?php $term = $choice['term']; $depth = (int) $choice['depth']; ?>
                    <label class="feuer-mannschaft-category-item">
                        <input type="checkbox" name="<?php echo esc_attr(Feuer_Mannschaft_Plugin::OPTION_KEY); ?>[allowed_categories][]" value="<?php echo esc_attr($term->term_id); ?>" <?php checked(in_array((int) $term->term_id, $allowed, true)); ?>>
                        <span><?php echo wp_kses_post(str_repeat('&mdash; ', $depth)); ?><?php echo esc_html($term->name); ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="feuer-mannschaft-settings-section feuer-settings-section">
    <header class="feuer-settings-section-heading">
        <div><span class="feuer-settings-kicker">02 · Seitenaufbau</span><h2>Standardvorlagen</h2></div>
        <p>Gelten für Listen und Profile ohne abweichende Vorlage im Shortcode.</p>
    </header>
    <div class="feuer-settings-grid feuer-settings-grid-two">
        <div class="feuer-settings-field">
            <label for="feuer_mannschaft_archive_template">Vorlage für Mannschaftslisten</label>
            <select id="feuer_mannschaft_archive_template" name="<?php echo esc_attr(Feuer_Mannschaft_Plugin::OPTION_KEY); ?>[archive_template]">
                <?php foreach ($archive_templates as $slug => $template) : ?>
                    <option value="<?php echo esc_attr($slug); ?>" <?php selected($settings['archive_template'], $slug); ?>><?php echo esc_html($template['label']); ?></option>
                <?php endforeach; ?>
            </select>
            <p class="description">Ein <code>template</code>-Attribut im Shortcode hat Vorrang.</p>
        </div>
        <div class="feuer-settings-field">
            <label for="feuer_mannschaft_single_template">Vorlage für Profile</label>
            <select id="feuer_mannschaft_single_template" name="<?php echo esc_attr(Feuer_Mannschaft_Plugin::OPTION_KEY); ?>[single_template]" data-profile-template>
                <?php foreach ($single_templates as $slug => $template) : ?>
                    <option value="<?php echo esc_attr($slug); ?>" <?php selected($settings['single_template'], $slug); ?>><?php echo esc_html($template['label']); ?></option>
                <?php endforeach; ?>
            </select>
            <p class="description">Wird beim direkten Aufruf eines Mannschaftsprofils verwendet.</p>
        </div>
    </div>
</section>

<section class="feuer-mannschaft-settings-section feuer-settings-section feuer-settings-visual-section">
    <header class="feuer-settings-section-heading">
        <div><span class="feuer-settings-kicker">03 · Listen</span><h2>Teamkarten gestalten</h2></div>
        <p>Wähle ein Kartenlayout und passe Bildformat, Typografie und sichtbare Informationen an.</p>
    </header>
    <div class="feuer-settings-visual-layout">
        <div class="feuer-settings-grid">
            <div class="feuer-settings-field feuer-settings-field-wide">
                <label for="feuer_mannschaft_card_preset">Kartenstil</label>
                <select id="feuer_mannschaft_card_preset" name="<?php echo esc_attr(Feuer_Mannschaft_Plugin::OPTION_KEY); ?>[card_preset]">
                    <?php foreach ($card_styles as $value => $label) : ?>
                        <option value="<?php echo esc_attr($value); ?>" <?php selected($settings['card_preset'], $value); ?>><?php echo esc_html($label); ?></option>
                    <?php endforeach; ?>
                </select>
                <p class="description">Die hellen Varianten zeigen Name und Funktion in einer Fläche unter dem Foto.</p>
            </div>

            <div class="feuer-settings-field">
                <label for="feuer_mannschaft_card_image_width">Bildbreite</label>
                <div class="feuer-settings-unit-input"><input type="number" min="180" max="1400" step="10" id="feuer_mannschaft_card_image_width" name="<?php echo esc_attr(Feuer_Mannschaft_Plugin::OPTION_KEY); ?>[card_image_width]" value="<?php echo esc_attr($settings['card_image_width']); ?>"><span>px</span></div>
            </div>
            <div class="feuer-settings-field">
                <label for="feuer_mannschaft_card_image_height">Bildhöhe</label>
                <div class="feuer-settings-unit-input"><input type="number" min="180" max="1600" step="10" id="feuer_mannschaft_card_image_height" name="<?php echo esc_attr(Feuer_Mannschaft_Plugin::OPTION_KEY); ?>[card_image_height]" value="<?php echo esc_attr($settings['card_image_height']); ?>"><span>px</span></div>
                <p class="description">Legt das Seitenverhältnis fest. Die horizontale Karte verwendet ihr eigenes Format.</p>
            </div>

            <div class="feuer-settings-field">
                <label for="feuer_mannschaft_card_radius">Eckenradius</label>
                <div class="feuer-settings-unit-input"><input type="number" min="0" max="40" id="feuer_mannschaft_card_radius" name="<?php echo esc_attr(Feuer_Mannschaft_Plugin::OPTION_KEY); ?>[card_radius]" value="<?php echo esc_attr($settings['card_radius']); ?>"><span>px</span></div>
            </div>
            <div class="feuer-settings-field">
                <label for="feuer_mannschaft_card_name_size">Namensgröße</label>
                <div class="feuer-settings-unit-input"><input type="number" min="16" max="42" id="feuer_mannschaft_card_name_size" name="<?php echo esc_attr(Feuer_Mannschaft_Plugin::OPTION_KEY); ?>[card_name_size]" value="<?php echo esc_attr($settings['card_name_size']); ?>"><span>px</span></div>
            </div>

            <fieldset class="feuer-settings-field feuer-settings-field-wide feuer-card-overlay-toggle">
                <legend>Fotoeffekt</legend>
                <label class="feuer-settings-check"><input type="checkbox" id="feuer_mannschaft_card_gradient_enabled" name="<?php echo esc_attr(Feuer_Mannschaft_Plugin::OPTION_KEY); ?>[card_gradient_enabled]" value="1" <?php checked(! empty($settings['card_gradient_enabled'])); ?>> Farbverlauf auf dem Foto anzeigen</label>
                <label class="feuer-settings-check"><input type="checkbox" id="feuer_mannschaft_card_overlay_enabled" name="<?php echo esc_attr(Feuer_Mannschaft_Plugin::OPTION_KEY); ?>[card_overlay_enabled]" value="1" <?php checked(! empty($settings['card_overlay_enabled'])); ?>> Fotoabdunklung anzeigen</label>
                <p class="description">Beide Effekte lassen sich unabhängig voneinander ein- und ausschalten.</p>
            </fieldset>

            <div class="feuer-settings-field feuer-settings-field-wide feuer-settings-range-field" data-control-row="overlay">
                <label for="feuer_mannschaft_card_overlay_opacity">Stärke der Abdunklung</label>
                <div class="feuer-settings-range-line"><input type="range" min="0" max="100" id="feuer_mannschaft_card_overlay_opacity" name="<?php echo esc_attr(Feuer_Mannschaft_Plugin::OPTION_KEY); ?>[card_overlay_opacity]" value="<?php echo esc_attr($settings['card_overlay_opacity']); ?>" aria-describedby="feuer_mannschaft_card_overlay_help"><output id="feuer_mannschaft_card_overlay_value" for="feuer_mannschaft_card_overlay_opacity"><?php echo esc_html($settings['card_overlay_opacity']); ?>%</output></div>
                <p class="description" id="feuer_mannschaft_card_overlay_help">Regelt nur die Abdunklung, solange sie aktiviert ist. 0&nbsp;% blendet sie ebenfalls vollständig aus.</p>
            </div>

            <div class="feuer-settings-field" data-control-row="position">
                <label for="feuer_mannschaft_card_text_position">Textposition</label>
                <select id="feuer_mannschaft_card_text_position" name="<?php echo esc_attr(Feuer_Mannschaft_Plugin::OPTION_KEY); ?>[card_text_position]"><?php foreach (array('top' => 'Oben', 'center' => 'Mitte', 'bottom' => 'Unten') as $value => $label) : ?><option value="<?php echo esc_attr($value); ?>" <?php selected($settings['card_text_position'], $value); ?>><?php echo esc_html($label); ?></option><?php endforeach; ?></select>
                <p class="description">Nur für Fotokarten mit Text über dem Foto.</p>
            </div>

            <fieldset class="feuer-settings-field">
                <legend>Sichtbare Angaben</legend>
                <label class="feuer-settings-check"><input type="checkbox" name="<?php echo esc_attr(Feuer_Mannschaft_Plugin::OPTION_KEY); ?>[card_show_rank]" value="1" <?php checked(! empty($settings['card_show_rank'])); ?>> Dienstgrad</label>
                <label class="feuer-settings-check"><input type="checkbox" name="<?php echo esc_attr(Feuer_Mannschaft_Plugin::OPTION_KEY); ?>[card_show_function]" value="1" <?php checked(! empty($settings['card_show_function'])); ?>> Funktion</label>
            </fieldset>

            <div class="feuer-settings-field">
                <label for="feuer_mannschaft_card_grayscale_strength">Graustärke</label>
                <div class="feuer-settings-unit-input"><input type="number" min="0" max="100" step="1" id="feuer_mannschaft_card_grayscale_strength" name="<?php echo esc_attr(Feuer_Mannschaft_Plugin::OPTION_KEY); ?>[card_grayscale_strength]" value="<?php echo esc_attr($settings['card_grayscale_strength']); ?>"><span>%</span></div>
                <p class="description">Nur für Graustufen-Vorlagen.</p>
            </div>
            <div class="feuer-settings-field">
                <label for="feuer_mannschaft_excluded_title_color">Farbe ausgeschlossener Namen</label>
                <input type="color" id="feuer_mannschaft_excluded_title_color" name="<?php echo esc_attr(Feuer_Mannschaft_Plugin::OPTION_KEY); ?>[excluded_title_color]" value="<?php echo esc_attr($settings['excluded_title_color']); ?>">
                <p class="description">Für Profile mit deaktiviertem Gesamtzähler.</p>
            </div>
        </div>

        <aside class="feuer-card-settings-preview" data-card-settings-preview aria-label="Vorschau der Teamkarte">
            <div class="feuer-card-settings-preview-heading"><strong>Vorschau der Teamkarte</strong><span>Live</span></div>
            <div class="feuer-card-settings-preview-image">
                <div class="feuer-card-settings-preview-photo" aria-hidden="true"></div>
                <span class="feuer-card-settings-preview-content"><small data-preview-rank>Hauptbrandmeister</small><strong>Max Mustermann</strong><span data-preview-function>Gruppenführer</span></span>
            </div>
            <p class="description">Die Vorschau zeigt Änderungen sofort. Auf der Website erscheinen sie nach dem Speichern.</p>
        </aside>
    </div>
</section>

<section class="feuer-mannschaft-settings-section feuer-settings-section feuer-settings-visual-section">
    <header class="feuer-settings-section-heading">
        <div><span class="feuer-settings-kicker">04 · Profile</span><h2>Profilbild und Galerie</h2></div>
        <p>Lege Bildhöhe und Verhalten der Profilgalerie fest.</p>
    </header>
    <div class="feuer-settings-visual-layout feuer-settings-profile-layout">
        <div class="feuer-settings-grid feuer-settings-grid-two">
            <div class="feuer-settings-field">
                <label for="feuer_mannschaft_single_image_height">Profilbildhöhe</label>
                <div class="feuer-settings-unit-input"><input type="number" min="240" max="2000" step="10" id="feuer_mannschaft_single_image_height" name="<?php echo esc_attr(Feuer_Mannschaft_Plugin::OPTION_KEY); ?>[single_image_height]" value="<?php echo esc_attr($settings['single_image_height']); ?>"><span>px</span></div>
            </div>
            <div class="feuer-settings-field">
                <label for="feuer_mannschaft_slider_autoplay">Bildwechsel</label>
                <div class="feuer-settings-unit-input"><input type="number" min="0" max="30000" step="500" id="feuer_mannschaft_slider_autoplay" name="<?php echo esc_attr(Feuer_Mannschaft_Plugin::OPTION_KEY); ?>[slider_autoplay]" value="<?php echo esc_attr($settings['slider_autoplay']); ?>"><span>ms</span></div>
                <p class="description">0 deaktiviert den automatischen Bildwechsel.</p>
            </div>
            <div class="feuer-settings-field feuer-settings-field-wide">
                <label class="feuer-settings-check"><input type="checkbox" name="<?php echo esc_attr(Feuer_Mannschaft_Plugin::OPTION_KEY); ?>[enable_gallery]" value="1" <?php checked((int) $settings['enable_gallery'], 1); ?>> Galerie und Slider in Profilen anzeigen</label>
            </div>
        </div>

        <aside class="feuer-profile-settings-preview" data-profile-settings-preview data-template="<?php echo esc_attr($settings['single_template']); ?>" aria-label="Vorschau des Profils">
            <div class="feuer-profile-settings-preview-heading"><strong>Vorschau des Profils</strong><span data-profile-preview-label><?php echo esc_html($single_templates[$settings['single_template']]['label'] ?? 'Standardprofil'); ?></span></div>
            <div class="feuer-profile-settings-preview-body">
                <div class="feuer-profile-settings-preview-photo" data-profile-preview-photo aria-hidden="true"></div>
                <div class="feuer-profile-settings-preview-copy"><span class="feuer-profile-settings-preview-role">Gruppenführer</span><strong>Max Mustermann</strong><span>Freiwillige Feuerwehr</span></div>
            </div>
        </aside>
    </div>
</section>

<section class="feuer-mannschaft-settings-section feuer-settings-section feuer-settings-section-compact">
    <div class="feuer-settings-compact-row">
        <div><span class="feuer-settings-kicker">05 · Leistung</span><h2>Cache automatisch leeren</h2><p>Aktualisiert Listen und Profile nach Änderungen an Profilen, Galerien und Einstellungen.</p></div>
        <label class="feuer-settings-check"><input type="checkbox" name="<?php echo esc_attr(Feuer_Mannschaft_Plugin::OPTION_KEY); ?>[enable_cache_purge]" value="1" <?php checked(! empty($settings['enable_cache_purge'])); ?>> Automatisch leeren</label>
    </div>
</section>

<details class="feuer-settings-shortcodes">
    <summary>Kurzcodes anzeigen</summary>
    <p>Kurzcodes können direkt in Seiten, Beiträge oder Templates eingefügt werden. Ein eigenes <code>template</code>-Attribut überschreibt die Standardvorlage.</p>
    <div class="feuer-mannschaft-shortcode-help">
        <div class="feuer-mannschaft-shortcode-item"><strong>Komplette Mannschaftsliste</strong><code>[feuer_mannschaft]</code><code>[feuer_mannschaft_liste]</code></div>
        <div class="feuer-mannschaft-shortcode-item"><strong>Einzelnes Profil</strong><code>[feuer_mannschaft_profil id="123"]</code><code>[feuer_mannschaft_profile slug="max-mustermann"]</code></div>
        <div class="feuer-mannschaft-shortcode-item"><strong>Name mit Profil-Link</strong><code>[feuer_mannschaft_person id="123"]</code><code>[feuer_mannschaft_person_link id="123"]</code></div>
        <div class="feuer-mannschaft-shortcode-item"><strong>Anzahl aktiver Profile</strong><code>[feuer_mannschaft_anzahl]</code><code>[feuer_mannschaft_count text="Aktive Mitglieder: %d"]</code></div>
        <div class="feuer-mannschaft-shortcode-item"><strong>Profilkarten</strong><code>[feuer_mannschaft_karten random="1" count="4"]</code><code>[feuer_mannschaft_cards name="Max Mustermann" template="cards"]</code></div>
    </div>
</details>

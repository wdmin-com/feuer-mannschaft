<?php

if (! defined('ABSPATH')) {
    exit;
}

$default_profile_image_id = isset($settings['default_profile_image_id']) ? absint($settings['default_profile_image_id']) : 0;
$default_profile_preview  = $default_profile_image_id
    ? wp_get_attachment_image($default_profile_image_id, array(240, 240), false, array('class' => 'feuer-mannschaft-default-image-preview-image'))
    : '';
?>
<table class="form-table" role="presentation">
    <tbody>
        <tr>
            <th scope="row"><label for="feuer_mannschaft_watermark_text">Watermark</label></th>
            <td>
                <input type="text" class="regular-text" id="feuer_mannschaft_watermark_text" name="<?php echo esc_attr(Feuer_Mannschaft_Plugin::OPTION_KEY); ?>[watermark_text]" value="<?php echo esc_attr($settings['watermark_text']); ?>">
                <p class="description">Dieser Text wird unten im Bild direkt nach Vorname und Nachname eingeblendet.</p>
            </td>
        </tr>

        <tr>
            <th scope="row"><label for="feuer_mannschaft_default_profile_image_id">Standard-Profilfoto bei fehlendem Foto</label></th>
            <td>
                <div class="feuer-mannschaft-default-image-picker" data-feuer-mannschaft-default-image-picker>
                    <input type="hidden" id="feuer_mannschaft_default_profile_image_id" name="<?php echo esc_attr(Feuer_Mannschaft_Plugin::OPTION_KEY); ?>[default_profile_image_id]" value="<?php echo esc_attr($default_profile_image_id); ?>" data-default-image-input>
                    <div class="feuer-mannschaft-default-image-preview" data-default-image-preview>
                        <?php if ($default_profile_preview) : ?>
                            <?php echo $default_profile_preview; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        <?php else : ?>
                            <p class="description">Noch kein Standardfoto ausgewaehlt.</p>
                        <?php endif; ?>
                    </div>
                    <div class="feuer-mannschaft-default-image-actions">
                        <button type="button" class="button" data-default-image-select><?php echo $default_profile_image_id ? 'Standardfoto ersetzen' : 'Standardfoto auswaehlen'; ?></button>
                        <button type="button" class="button button-link-delete" data-default-image-remove <?php disabled($default_profile_image_id, 0); ?>>Standardfoto entfernen</button>
                    </div>
                    <p class="description">Dieses Bild wird automatisch angezeigt, wenn einem Profil noch kein eigenes Foto zugeordnet wurde.</p>
                </div>
            </td>
        </tr>

        <tr>
            <th scope="row">Freigegebene Kategorien</th>
            <td>
                <p class="description">Nur diese WordPress-Kategorien stehen in der Mannschaftsakte zur Auswahl. Jede Akte wird als normaler Beitrag gespeichert.</p>
                <?php if (empty($choices)) : ?>
                    <p>Es wurden noch keine WordPress-Kategorien gefunden.</p>
                <?php else : ?>
                    <div class="feuer-mannschaft-settings-category-list">
                        <?php foreach ($choices as $choice) : ?>
                            <?php
                            $term  = $choice['term'];
                            $depth = (int) $choice['depth'];
                            ?>
                            <label class="feuer-mannschaft-category-item">
                                <input type="checkbox" name="<?php echo esc_attr(Feuer_Mannschaft_Plugin::OPTION_KEY); ?>[allowed_categories][]" value="<?php echo esc_attr($term->term_id); ?>" <?php checked(in_array((int) $term->term_id, $allowed, true)); ?>>
                                <span><?php echo wp_kses_post(str_repeat('&mdash; ', $depth)); ?><?php echo esc_html($term->name); ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </td>
        </tr>

        <tr>
            <th scope="row"><label for="feuer_mannschaft_archive_template">Standardvorlage für Listen</label></th>
            <td>
                <select id="feuer_mannschaft_archive_template" name="<?php echo esc_attr(Feuer_Mannschaft_Plugin::OPTION_KEY); ?>[archive_template]">
                    <?php foreach ($archive_templates as $slug => $template) : ?>
                        <option value="<?php echo esc_attr($slug); ?>" <?php selected($settings['archive_template'], $slug); ?>>
                            <?php echo esc_html($template['label']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <p class="description">Diese Vorlage wird verwendet, wenn kein Template direkt im Shortcode angegeben wird.</p>
            </td>
        </tr>

        <tr>
            <th scope="row"><label for="feuer_mannschaft_single_template">Standardvorlage für Profile</label></th>
            <td>
                <select id="feuer_mannschaft_single_template" name="<?php echo esc_attr(Feuer_Mannschaft_Plugin::OPTION_KEY); ?>[single_template]">
                    <?php foreach ($single_templates as $slug => $template) : ?>
                        <option value="<?php echo esc_attr($slug); ?>" <?php selected($settings['single_template'], $slug); ?>>
                            <?php echo esc_html($template['label']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <p class="description">Diese Vorlage wird beim direkten Aufruf eines Feuer-Mannschaft-Profils verwendet.</p>
            </td>
        </tr>

        <tr>
            <th scope="row"><label for="feuer_mannschaft_card_image_width">Kartenbild Breite</label></th>
            <td>
                <input type="number" class="small-text" min="180" step="10" id="feuer_mannschaft_card_image_width" name="<?php echo esc_attr(Feuer_Mannschaft_Plugin::OPTION_KEY); ?>[card_image_width]" value="<?php echo esc_attr($settings['card_image_width']); ?>"> px
            </td>
        </tr>

        <tr>
            <th scope="row"><label for="feuer_mannschaft_card_image_height">Kartenbild Höhe</label></th>
            <td>
                <input type="number" class="small-text" min="180" step="10" id="feuer_mannschaft_card_image_height" name="<?php echo esc_attr(Feuer_Mannschaft_Plugin::OPTION_KEY); ?>[card_image_height]" value="<?php echo esc_attr($settings['card_image_height']); ?>"> px
            </td>
        </tr>

        <tr>
            <th scope="row"><label for="feuer_mannschaft_single_image_height">Profilbild Höhe</label></th>
            <td>
                <input type="number" class="small-text" min="240" step="10" id="feuer_mannschaft_single_image_height" name="<?php echo esc_attr(Feuer_Mannschaft_Plugin::OPTION_KEY); ?>[single_image_height]" value="<?php echo esc_attr($settings['single_image_height']); ?>"> px
            </td>
        </tr>

        <tr>
            <th scope="row"><label for="feuer_mannschaft_slider_autoplay">Slider Autoplay</label></th>
            <td>
                <input type="number" class="small-text" min="0" step="500" id="feuer_mannschaft_slider_autoplay" name="<?php echo esc_attr(Feuer_Mannschaft_Plugin::OPTION_KEY); ?>[slider_autoplay]" value="<?php echo esc_attr($settings['slider_autoplay']); ?>"> ms
            </td>
        </tr>

        <tr>
            <th scope="row">Galerie aktivieren</th>
            <td>
                <label>
                    <input type="checkbox" name="<?php echo esc_attr(Feuer_Mannschaft_Plugin::OPTION_KEY); ?>[enable_gallery]" value="1" <?php checked((int) $settings['enable_gallery'], 1); ?>>
                    Galerie und Slider im Frontend verwenden
                </label>
            </td>
        </tr>
    </tbody>
</table>

<p>Vorlagen werden automatisch aus den Ordnern <code>templates/uebersicht</code> und <code>templates/profil</code> geladen.</p>

<div class="feuer-mannschaft-settings-section">
    <h2>Kurzcode-Übersicht</h2>
    <p>Diese Kurzcodes verwenden im Plugin durchgängig den Präfix <code>feuer_mannschaft</code> und können direkt in Seiten, Beiträge oder Templates eingefügt werden.</p>

    <div class="feuer-mannschaft-shortcode-help">
        <div class="feuer-mannschaft-shortcode-item">
            <strong>Komplette Mannschaftsliste</strong>
            <code>[feuer_mannschaft]</code>
            <code>[feuer_mannschaft_liste]</code>
            <p class="description">Zeigt die freigegebene Mannschaftsübersicht mit der Standardvorlage an.</p>
        </div>

        <div class="feuer-mannschaft-shortcode-item">
            <strong>Ein einzelnes Profil</strong>
            <code>[feuer_mannschaft_profil id="123"]</code>
            <code>[feuer_mannschaft_profile slug="max-mustermann"]</code>
            <p class="description">Zeigt ein einzelnes Feuer-Mannschaft-Profil direkt auf der Seite an.</p>
        </div>

        <div class="feuer-mannschaft-shortcode-item">
            <strong>Nur Name mit Profil-Link</strong>
            <code>[feuer_mannschaft_person id="123"]</code>
            <code>[feuer_mannschaft_person_link id="123"]</code>
            <p class="description">Gibt nur den Namen des Teilnehmers als Link zur Teilnehmerakte aus.</p>
        </div>

        <div class="feuer-mannschaft-shortcode-item">
            <strong>Anzahl aktiver Profile</strong>
            <code>[feuer_mannschaft_anzahl]</code>
            <code>[feuer_mannschaft_count text="Aktive Mitglieder: %d"]</code>
            <p class="description">Zeigt die Anzahl aktiver Feuer-Mannschaft-Profile an.</p>
        </div>

        <div class="feuer-mannschaft-shortcode-item">
            <strong>Profilkarten</strong>
            <code>[feuer_mannschaft_karten random="1" count="4"]</code>
            <code>[feuer_mannschaft_cards name="Max Mustermann" template="cards"]</code>
            <p class="description">Zeigt ausgewählte oder zufällige Profilkarten an.</p>
        </div>
    </div>
</div>

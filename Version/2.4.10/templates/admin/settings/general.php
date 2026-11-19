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
        <tr class="feuer-mannschaft-settings-group"><th colspan="2">Listen und Profile</th></tr>
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
                <p class="description">Standardmäßig wird „Team Raster“ verwendet. Eine direkt im Shortcode angegebene Vorlage hat Vorrang.</p>
            </td>
        </tr>

        <tr>
            <th scope="row"><label for="feuer_mannschaft_single_template">Standardvorlage für Profile</label></th>
            <td>
                <select id="feuer_mannschaft_single_template" name="<?php echo esc_attr(Feuer_Mannschaft_Plugin::OPTION_KEY); ?>[single_template]" data-profile-template>
                    <?php foreach ($single_templates as $slug => $template) : ?>
                        <option value="<?php echo esc_attr($slug); ?>" <?php selected($settings['single_template'], $slug); ?>>
                            <?php echo esc_html($template['label']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <p class="description">Diese Vorlage wird beim direkten Aufruf eines Mannschaft-Profils verwendet.</p>
            </td>
        </tr>

        <tr class="feuer-mannschaft-settings-group"><th colspan="2">Bildformat der Karten</th></tr>
        <tr>
            <th scope="row"><label for="feuer_mannschaft_card_image_width">Kartenbild Breite</label></th>
            <td>
                <input type="number" class="small-text" min="180" max="1400" step="10" id="feuer_mannschaft_card_image_width" name="<?php echo esc_attr(Feuer_Mannschaft_Plugin::OPTION_KEY); ?>[card_image_width]" value="<?php echo esc_attr($settings['card_image_width']); ?>"> px
                <p class="description">Legt zusammen mit der Höhe das Bildformat der Karten fest. Bei der horizontalen Karte wird das Format durch das Layout bestimmt.</p>
            </td>
        </tr>

        <tr class="feuer-mannschaft-settings-group"><th colspan="2">Kartendesign</th></tr>
        <tr><th scope="row"><label for="feuer_mannschaft_card_preset">Kartenstil</label></th><td>
            <select id="feuer_mannschaft_card_preset" name="<?php echo esc_attr(Feuer_Mannschaft_Plugin::OPTION_KEY); ?>[card_preset]">
                <?php foreach (array('classic'=>'Klassisch (bisherig)','minimal'=>'Minimal (bisherig)','dark'=>'Dunkel (bisherig)','official'=>'Offiziell (bisherig)','portrait-light'=>'Klassische helle Porträt-Karte','compact-horizontal'=>'Kompakte Horizontal-Karte','editorial'=>'Editorial-Karte') as $value=>$label) : ?><option value="<?php echo esc_attr($value); ?>" <?php selected($settings['card_preset'], $value); ?>><?php echo esc_html($label); ?></option><?php endforeach; ?>
            </select>
            <p class="description">Die helle Porträt- und Editorial-Karte zeigen Text unter dem Foto. Bildabdunklung und Textposition werden nur bei Fotokarten mit Text auf dem Bild verwendet.</p>
        </td></tr>
        <tr><th scope="row"><label for="feuer_mannschaft_card_overlay_opacity">Bildabdunklung</label></th><td><input type="range" min="0" max="100" id="feuer_mannschaft_card_overlay_opacity" name="<?php echo esc_attr(Feuer_Mannschaft_Plugin::OPTION_KEY); ?>[card_overlay_opacity]" value="<?php echo esc_attr($settings['card_overlay_opacity']); ?>" aria-describedby="feuer_mannschaft_card_overlay_help"> <output id="feuer_mannschaft_card_overlay_value" for="feuer_mannschaft_card_overlay_opacity"><?php echo esc_html($settings['card_overlay_opacity']); ?></output> %<p class="description" id="feuer_mannschaft_card_overlay_help">Wirkt auf klassische Fotokarten mit Text über dem Foto.</p></td></tr>
        <tr><th scope="row"><label for="feuer_mannschaft_card_radius">Eckenradius</label></th><td><input type="number" min="0" max="40" id="feuer_mannschaft_card_radius" name="<?php echo esc_attr(Feuer_Mannschaft_Plugin::OPTION_KEY); ?>[card_radius]" value="<?php echo esc_attr($settings['card_radius']); ?>"> px</td></tr>
        <tr><th scope="row"><label for="feuer_mannschaft_card_name_size">Namensgröße</label></th><td><input type="number" min="16" max="42" id="feuer_mannschaft_card_name_size" name="<?php echo esc_attr(Feuer_Mannschaft_Plugin::OPTION_KEY); ?>[card_name_size]" value="<?php echo esc_attr($settings['card_name_size']); ?>"> px</td></tr>
        <tr><th scope="row"><label for="feuer_mannschaft_card_text_position">Textposition auf dem Foto</label></th><td><select id="feuer_mannschaft_card_text_position" name="<?php echo esc_attr(Feuer_Mannschaft_Plugin::OPTION_KEY); ?>[card_text_position]"><?php foreach(array('top'=>'Oben','center'=>'Mitte','bottom'=>'Unten') as $value=>$label): ?><option value="<?php echo esc_attr($value); ?>" <?php selected($settings['card_text_position'],$value); ?>><?php echo esc_html($label); ?></option><?php endforeach; ?></select><p class="description">Gilt nur für klassische Fotokarten.</p></td></tr>
        <tr><th scope="row">Sichtbare Kartenfelder</th><td><fieldset><legend class="screen-reader-text">Sichtbare Kartenfelder</legend><label><input type="checkbox" name="<?php echo esc_attr(Feuer_Mannschaft_Plugin::OPTION_KEY); ?>[card_show_rank]" value="1" <?php checked(!empty($settings['card_show_rank'])); ?>> Dienstgrad</label><br><label><input type="checkbox" name="<?php echo esc_attr(Feuer_Mannschaft_Plugin::OPTION_KEY); ?>[card_show_function]" value="1" <?php checked(!empty($settings['card_show_function'])); ?>> Funktion</label></fieldset></td></tr>

        <tr>
            <th scope="row"><label for="feuer_mannschaft_card_image_height">Kartenbild Höhe</label></th>
            <td>
                <input type="number" class="small-text" min="180" max="1600" step="10" id="feuer_mannschaft_card_image_height" name="<?php echo esc_attr(Feuer_Mannschaft_Plugin::OPTION_KEY); ?>[card_image_height]" value="<?php echo esc_attr($settings['card_image_height']); ?>"> px
            </td>
        </tr>

        <tr>
            <th scope="row"><label for="feuer_mannschaft_card_grayscale_strength">Graustufen fuer Karten</label></th>
            <td>
                <input type="number" class="small-text" min="0" max="100" step="1" id="feuer_mannschaft_card_grayscale_strength" name="<?php echo esc_attr(Feuer_Mannschaft_Plugin::OPTION_KEY); ?>[card_grayscale_strength]" value="<?php echo esc_attr($settings['card_grayscale_strength']); ?>"> %
                <p class="description">Wirkt nur auf Graustufen-Vorlagen wie <code>Team Raster Graustufen</code> und steuert, wie stark die Portraitbilder ausgebleicht werden.</p>
            </td>
        </tr>

        <tr>
            <th scope="row"><label for="feuer_mannschaft_excluded_title_color">Farbe fuer ausgeschlossene Profilnamen</label></th>
            <td>
                <input type="color" id="feuer_mannschaft_excluded_title_color" name="<?php echo esc_attr(Feuer_Mannschaft_Plugin::OPTION_KEY); ?>[excluded_title_color]" value="<?php echo esc_attr($settings['excluded_title_color']); ?>">
                <p class="description">Diese Farbe wird nur fuer den Namen von Profilen verwendet, bei denen <code>Im Gesamtzaehler nicht mitzaehlen</code> aktiviert ist.</p>
            </td>
        </tr>

        <tr>
            <th scope="row">Cache automatisch leeren</th>
            <td>
                <label>
                    <input type="checkbox" name="<?php echo esc_attr(Feuer_Mannschaft_Plugin::OPTION_KEY); ?>[enable_cache_purge]" value="1" <?php checked(! empty($settings['enable_cache_purge'])); ?>>
                    Cache nach Profilaenderungen automatisch leeren
                </label>
                <p class="description">Leert WordPress-Objektcache, Seiten mit Mannschaft-Shortcodes und unterstuetzte Cache-Plugins nach Profil-, Galerie-, Einstellungs- und Restore-Aenderungen.</p>
            </td>
        </tr>

        <tr class="feuer-mannschaft-settings-group"><th colspan="2">Profilbilder und Galerie</th></tr>
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
                <p class="description">Steuert den automatischen Bildwechsel in den Profilgalerien. <code>0</code> deaktiviert das automatische Weiterblättern.</p>
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

<div class="feuer-card-settings-preview" data-card-settings-preview>
    <div class="feuer-card-settings-preview-image">
        <div class="feuer-card-settings-preview-photo" aria-hidden="true"></div>
        <span class="feuer-card-settings-preview-content"><small data-preview-rank>Hauptbrandmeister</small><strong>Max Mustermann</strong><span data-preview-function>Gruppenführer</span></span>
    </div>
    <p class="description">Live-Vorschau. Änderungen hier werden erst nach „Änderungen speichern“ auf der Website veröffentlicht.</p>
</div>

<div class="feuer-profile-settings-preview" data-profile-settings-preview data-template="<?php echo esc_attr($settings['single_template']); ?>">
    <div class="feuer-profile-settings-preview-photo" data-profile-preview-photo aria-hidden="true"></div>
    <div class="feuer-profile-settings-preview-copy">
        <span class="feuer-profile-settings-preview-template" data-profile-preview-label><?php echo esc_html($single_templates[$settings['single_template']]['label'] ?? 'Standardprofil'); ?></span>
        <span class="feuer-profile-settings-preview-role">Gruppenführer</span>
        <strong>Max Mustermann</strong>
        <span>Freiwillige Feuerwehr</span>
    </div>
    <p class="description">Profilvorschau: Vorlage und Bildhöhe werden hier direkt angezeigt.</p>
</div>

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
            <p class="description">Zeigt ein einzelnes Mannschaft-Profil direkt auf der Seite an.</p>
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
            <p class="description">Zeigt die Anzahl aktiver Mannschaft-Profile an.</p>
        </div>

        <div class="feuer-mannschaft-shortcode-item">
            <strong>Profilkarten</strong>
            <code>[feuer_mannschaft_karten random="1" count="4"]</code>
            <code>[feuer_mannschaft_cards name="Max Mustermann" template="cards"]</code>
            <p class="description">Zeigt ausgewählte oder zufällige Profilkarten an.</p>
        </div>
    </div>
</div>

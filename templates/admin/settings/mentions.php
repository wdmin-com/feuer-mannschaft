<?php

if (! defined('ABSPATH')) {
    exit;
}
?>
<section class="feuer-mannschaft-settings-section feuer-settings-section feuer-settings-mentions-section">
    <header class="feuer-settings-section-heading">
        <div><span class="feuer-settings-kicker">Profilinhalte</span><h2><span class="dashicons dashicons-format-chat feuer-admin-section-icon" aria-hidden="true"></span>Erwähnungen</h2></div>
        <p>Steuere, welche Beiträge mit Teilnehmerprofilen verknüpft werden können.</p>
    </header>
<table class="form-table" role="presentation">
    <tbody>
        <tr>
            <th scope="row">Bereich aktivieren</th>
            <td>
                <label>
                    <input type="checkbox" name="<?php echo esc_attr(Feuer_Mannschaft_Plugin::OPTION_KEY); ?>[enable_mentions]" value="1" <?php checked((int) $settings['enable_mentions'], 1); ?>>
                    Abschnitt "Erwähnungen" auf den Profilseiten anzeigen
                </label>
                <p class="description">Der Abschnitt wird oberhalb der Navigationsbuttons angezeigt und nur eingeblendet, wenn es tatsächliche Erwähnungen gibt.</p>
            </td>
        </tr>
        <tr>
            <th scope="row">Post Types mit Erwähnungen</th>
            <td>
                <div class="feuer-mannschaft-settings-category-list">
                    <?php foreach ($public_post_types as $post_type => $label) : ?>
                        <label class="feuer-mannschaft-category-item">
                            <input type="checkbox" name="<?php echo esc_attr(Feuer_Mannschaft_Plugin::OPTION_KEY); ?>[mention_post_types][]" value="<?php echo esc_attr($post_type); ?>" <?php checked(in_array($post_type, $settings['mention_post_types'], true)); ?>>
                            <span><?php echo esc_html($label); ?> <code><?php echo esc_html($post_type); ?></code></span>
                        </label>
                    <?php endforeach; ?>
                </div>
                <p class="description">
                    <?php if ($acf_available) : ?>
                        ACF ist aktiv. Das Feld <code>mentioned_persons</code> wird automatisch als Relationship-Feld bereitgestellt.
                    <?php else : ?>
                        ACF wurde nicht gefunden. Der Plugin-Fallback zeigt ein Mehrfachauswahl-Feld in der Seitenleiste.
                    <?php endif; ?>
                </p>
            </td>
        </tr>
    </tbody>
</table>
</section>

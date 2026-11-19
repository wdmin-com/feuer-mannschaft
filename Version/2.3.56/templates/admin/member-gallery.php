<?php

if (! defined('ABSPATH')) {
    exit;
}

$default_image_preview = ! empty($default_image_id)
    ? wp_get_attachment_image($default_image_id, 'medium', false, array('class' => 'feuer-mannschaft-gallery-preview-image'))
    : '';
?>
<div class="feuer-mannschaft-gallery-box">
    <p class="description">
        Laden Sie ein oder mehrere Fotos hoch. Alle Dateien werden im Ordner <code>uploads/feuer-mannschaft</code> gespeichert.
        Beim Upload wird unten ein Text-Wasserzeichen mit dem Namen des Teilnehmers<?php echo $settings['watermark_text'] ? ' und dem konfigurierten Zusatztext' : ''; ?> eingeblendet.
    </p>
    <p class="description">
        Wenn hier kein eigenes Foto hinterlegt ist, wird im Frontend automatisch das Standard-Profilfoto aus den Einstellungen verwendet.
    </p>

    <p>
        <label for="feuer_mannschaft_gallery_upload"><strong>Fotos hochladen <?php echo $name ? 'für ' . esc_html($name) : ''; ?></strong></label><br>
        <input type="file" id="feuer_mannschaft_gallery_upload" name="feuer_mannschaft_gallery[]" accept="image/jpeg,image/png,image/gif,image/webp" multiple>
    </p>

    <?php if (empty($gallery) && $default_image_preview) : ?>
        <div class="feuer-mannschaft-gallery-grid">
            <div class="feuer-mannschaft-gallery-item">
                <div class="feuer-mannschaft-gallery-image">
                    <?php echo $default_image_preview; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </div>

                <?php if ($default_image_active) : ?>
                    <p class="description">Dieses Standard-Profilfoto ist aktuell auch als Beitragsbild diesem Profil zugeordnet.</p>
                    <label>
                        <input type="checkbox" name="feuer_mannschaft_remove_default_image" value="1">
                        Standardfoto fuer dieses Profil entfernen
                    </label>
                <?php elseif ($default_image_opted_out) : ?>
                    <p class="description">Das Standard-Profilfoto wurde fuer dieses Profil manuell entfernt.</p>
                    <label>
                        <input type="checkbox" name="feuer_mannschaft_restore_default_image" value="1">
                        Standardfoto wieder fuer dieses Profil verwenden
                    </label>
                <?php else : ?>
                    <p class="description">Beim Speichern wird dieses Standard-Profilfoto diesem Profil automatisch als Beitragsbild zugeordnet.</p>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if (empty($gallery)) : ?>
        <p class="description"><?php echo $default_image_preview ? 'Noch keine eigenen Fotos hinterlegt.' : 'Noch keine Fotos hinterlegt.'; ?></p>
    <?php else : ?>
        <div class="feuer-mannschaft-gallery-grid">
            <?php foreach ($gallery as $attachment_id) : ?>
                <div class="feuer-mannschaft-gallery-item">
                    <div class="feuer-mannschaft-gallery-image">
                        <?php
                        echo wp_get_attachment_image(
                            $attachment_id,
                            'medium',
                            false,
                            array('class' => 'feuer-mannschaft-gallery-preview-image')
                        ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        ?>
                    </div>
                    <label>
                        <input type="checkbox" name="feuer_mannschaft_remove_gallery[]" value="<?php echo esc_attr($attachment_id); ?>">
                        Foto entfernen
                    </label>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

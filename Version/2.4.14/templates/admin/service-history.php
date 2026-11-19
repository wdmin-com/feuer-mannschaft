<?php
if (! defined('ABSPATH')) {
    exit;
}
$events = ! empty($events) ? $events : array(array('date' => '', 'type' => 'entry', 'title' => '', 'description' => ''));
?>
<div class="feuer-mannschaft-service-history" data-service-history>
    <p class="description">Erfassen Sie Stationen des Dienstverlaufs. Die Einträge werden im Profil chronologisch angezeigt.</p>
    <div data-service-history-rows>
        <?php foreach ($events as $index => $event) : ?>
            <div class="feuer-mannschaft-service-history-row" data-service-history-row>
                <input type="date" name="feuer_mannschaft_service_history[<?php echo esc_attr($index); ?>][date]" value="<?php echo esc_attr($event['date']); ?>" aria-label="Datum">
                <select name="feuer_mannschaft_service_history[<?php echo esc_attr($index); ?>][type]" aria-label="Art des Ereignisses">
                    <?php foreach ($event_types as $type => $label) : ?>
                        <option value="<?php echo esc_attr($type); ?>" <?php selected($event['type'], $type); ?>><?php echo esc_html($label); ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="text" name="feuer_mannschaft_service_history[<?php echo esc_attr($index); ?>][title]" value="<?php echo esc_attr($event['title']); ?>" placeholder="Titel" aria-label="Titel">
                <textarea name="feuer_mannschaft_service_history[<?php echo esc_attr($index); ?>][description]" rows="2" placeholder="Optionale Beschreibung" aria-label="Beschreibung"><?php echo esc_textarea($event['description']); ?></textarea>
                <button type="button" class="button-link-delete" data-service-history-remove>Entfernen</button>
            </div>
        <?php endforeach; ?>
    </div>
    <button type="button" class="button" data-service-history-add>Eintrag hinzufügen</button>
    <template data-service-history-template>
        <div class="feuer-mannschaft-service-history-row" data-service-history-row>
            <input type="date" name="feuer_mannschaft_service_history[__INDEX__][date]" aria-label="Datum">
            <select name="feuer_mannschaft_service_history[__INDEX__][type]" aria-label="Art des Ereignisses">
                <?php foreach ($event_types as $type => $label) : ?><option value="<?php echo esc_attr($type); ?>"><?php echo esc_html($label); ?></option><?php endforeach; ?>
            </select>
            <input type="text" name="feuer_mannschaft_service_history[__INDEX__][title]" placeholder="Titel" aria-label="Titel">
            <textarea name="feuer_mannschaft_service_history[__INDEX__][description]" rows="2" placeholder="Optionale Beschreibung" aria-label="Beschreibung"></textarea>
            <button type="button" class="button-link-delete" data-service-history-remove>Entfernen</button>
        </div>
    </template>
</div>

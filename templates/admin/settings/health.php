<?php

if (! defined('ABSPATH')) {
    exit;
}
?>
<section class="feuer-mannschaft-settings-section" aria-labelledby="feuer-mannschaft-health-title">
    <h2 id="feuer-mannschaft-health-title" class="feuer-admin-icon-heading"><span class="dashicons dashicons-heart feuer-admin-section-icon" aria-hidden="true"></span>Systemdiagnose</h2>
    <p>Diese Prüfungen sind lesend und ändern keine Einstellungen oder Dateien.</p>
    <table class="widefat striped">
        <thead>
            <tr><th scope="col">Prüfung</th><th scope="col">Status</th><th scope="col">Details</th></tr>
        </thead>
        <tbody>
            <?php foreach ($health_checks as $check) : ?>
                <tr>
                    <th scope="row"><?php echo esc_html($check['label']); ?></th>
                    <td><span class="notice-inline <?php echo $check['ok'] ? 'notice-success' : 'notice-error'; ?>"><?php echo $check['ok'] ? esc_html__('OK', 'feuer-mannschaft') : esc_html__('Prüfen', 'feuer-mannschaft'); ?></span></td>
                    <td><?php echo esc_html($check['message']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>

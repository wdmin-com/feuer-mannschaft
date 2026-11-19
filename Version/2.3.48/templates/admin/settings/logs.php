<?php

if (! defined('ABSPATH')) {
    exit;
}
?>
<div class="feuer-mannschaft-settings-section">
    <h2>Aktivitätsprotokoll</h2>
    <p>Standardmäßig wird die komplette Liste angezeigt. Über den Filter können Sie Aktionen je Administrator eingrenzen.</p>

    <form method="get" action="<?php echo esc_url(admin_url('admin.php')); ?>" class="feuer-mannschaft-log-filter">
        <input type="hidden" name="page" value="feuer-mannschaft-settings">
        <input type="hidden" name="tab" value="logs">
        <select name="log_user">
            <option value="0">Alle Administratoren</option>
            <?php foreach ($log_users as $user) : ?>
                <option value="<?php echo esc_attr($user->ID); ?>" <?php selected($selected_log_user, (int) $user->ID); ?>>
                    <?php echo esc_html($user->display_name); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php submit_button('Filtern', 'secondary', 'submit', false); ?>
    </form>

    <?php if (empty($logs)) : ?>
        <p>Es wurden noch keine Logeinträge gefunden.</p>
    <?php else : ?>
        <table class="widefat striped">
            <thead>
                <tr>
                    <th>Zeit</th>
                    <th>Administrator</th>
                    <th>Aktion</th>
                    <th>Objekt</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log) : ?>
                    <?php $log_user = $log->user_id ? get_user_by('id', (int) $log->user_id) : null; ?>
                    <tr>
                        <td><?php echo esc_html(wp_date(get_option('date_format') . ' ' . get_option('time_format'), strtotime($log->created_at))); ?></td>
                        <td><?php echo esc_html($log_user ? $log_user->display_name : 'System'); ?></td>
                        <td><?php echo esc_html($logger->get_action_label($log->action)); ?></td>
                        <td>
                            <strong><?php echo esc_html($log->entity_label ?: '-'); ?></strong><br>
                            <small><?php echo esc_html($log->entity_type); ?><?php echo $log->entity_id ? ' #' . esc_html((string) $log->entity_id) : ''; ?></small>
                        </td>
                        <td><pre class="feuer-mannschaft-log-details"><?php echo esc_html($logger->format_details_for_display($log->details_array)); ?></pre></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

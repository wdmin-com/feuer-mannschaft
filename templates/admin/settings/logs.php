<?php

if (! defined('ABSPATH')) {
    exit;
}
?>
<div class="feuer-mannschaft-settings-section">
    <h2 class="feuer-admin-icon-heading"><span class="dashicons dashicons-media-text feuer-admin-section-icon" aria-hidden="true"></span>Aktivitätsprotokoll</h2>
    <p>Die Einträge werden seitenweise angezeigt. Über den Filter können Sie Aktionen je Administrator eingrenzen.</p>

    <form method="get" action="<?php echo esc_url(admin_url('admin.php')); ?>" class="feuer-mannschaft-log-filter">
        <input type="hidden" name="page" value="feuer-mannschaft-settings">
        <input type="hidden" name="tab" value="logs">
        <label for="fm-log-user">Administrator</label>
        <select id="fm-log-user" name="log_user">
            <option value="0">Alle Administratoren</option>
            <?php foreach ($log_users as $user) : ?>
                <option value="<?php echo esc_attr($user->ID); ?>" <?php selected($selected_log_user, (int) $user->ID); ?>>
                    <?php echo esc_html($user->display_name); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <label for="fm-log-action">Aktion</label>
        <select id="fm-log-action" name="log_action">
            <option value=""><?php echo esc_html__('Alle Aktionen', 'feuer-mannschaft'); ?></option>
            <?php foreach ($log_actions as $action) : ?>
                <option value="<?php echo esc_attr($action); ?>" <?php selected($selected_log_action, $action); ?>><?php echo esc_html($logger->get_action_label($action)); ?></option>
            <?php endforeach; ?>
        </select>
        <label for="fm-log-date-from">Von</label>
        <input type="date" id="fm-log-date-from" name="log_date_from" value="<?php echo esc_attr($log_date_from); ?>">
        <label for="fm-log-date-to">Bis</label>
        <input type="date" id="fm-log-date-to" name="log_date_to" value="<?php echo esc_attr($log_date_to); ?>">
        <?php submit_button('Filtern', 'secondary', 'submit', false); ?>
    </form>

    <?php
    $export_url = wp_nonce_url(
        add_query_arg(
            array_merge(
                array('action' => 'feuer_mannschaft_export_logs'),
                array_filter(
                    array(
                        'log_user' => $selected_log_user,
                        'log_action' => $selected_log_action,
                        'log_date_from' => $log_date_from,
                        'log_date_to' => $log_date_to,
                    ),
                    function ($value) {
                        return '' !== $value && 0 !== $value;
                    }
                )
            ),
            admin_url('admin-post.php')
        ),
        'feuer_mannschaft_export_logs'
    );
    ?>
    <p><a class="button button-secondary" href="<?php echo esc_url($export_url); ?>"><?php echo esc_html__('Gefilterte Einträge als CSV exportieren (max. 10.000)', 'feuer-mannschaft'); ?></a></p>

    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" class="feuer-mannschaft-log-retention">
        <input type="hidden" name="action" value="feuer_mannschaft_save_log_retention">
        <?php wp_nonce_field('feuer_mannschaft_save_log_retention'); ?>
        <label for="fm-log-retention"><?php echo esc_html__('Protokoll-Aufbewahrung', 'feuer-mannschaft'); ?></label>
        <select id="fm-log-retention" name="log_retention_days">
            <?php foreach (array(0 => 'Unbegrenzt', 90 => '90 Tage', 180 => '180 Tage', 365 => '1 Jahr', 730 => '2 Jahre') as $days => $label) : ?>
                <option value="<?php echo esc_attr($days); ?>" <?php selected($log_retention_days, $days); ?>><?php echo esc_html($label); ?></option>
            <?php endforeach; ?>
        </select>
        <?php submit_button(__('Aufbewahrung speichern', 'feuer-mannschaft'), 'secondary', 'submit', false); ?>
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

    <?php if ($log_total > $log_page_size) : ?>
        <div class="tablenav bottom">
            <div class="tablenav-pages">
                <?php
                echo wp_kses_post(
                    paginate_links(
                        array(
                            'base'      => add_query_arg('log_page', '%#%'),
                            'format'    => '',
                            'current'   => $log_page,
                            'total'     => (int) ceil($log_total / $log_page_size),
                            'add_args'  => array_filter(array('page' => 'feuer-mannschaft-settings', 'tab' => 'logs', 'log_user' => $selected_log_user, 'log_action' => $selected_log_action, 'log_date_from' => $log_date_from, 'log_date_to' => $log_date_to), function ($value) { return '' !== $value && 0 !== $value; }),
                            'prev_text' => __('&laquo; Zurück', 'feuer-mannschaft'),
                            'next_text' => __('Weiter &raquo;', 'feuer-mannschaft'),
                        )
                    )
                );
                ?>
            </div>
        </div>
    <?php endif; ?>
</div>

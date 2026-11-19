<?php

if (! defined('ABSPATH')) {
    exit;
}

class Feuer_Mannschaft_Logger {
    const SCHEMA_OPTION  = 'feuer_mannschaft_log_schema_version';
    const SCHEMA_VERSION = '2026-04-15-1';
    const TABLE_SUFFIX = 'feuer_mannschaft_logs';

    private $wpdb;
    private $table_name;

    public function __construct() {
        global $wpdb;

        $this->wpdb       = $wpdb;
        $this->table_name = $wpdb->prefix . self::TABLE_SUFFIX;
    }

    public function maybe_install() {
        $installed_version = (string) get_option(self::SCHEMA_OPTION, '');

        if (self::SCHEMA_VERSION === $installed_version && $this->has_required_schema()) {
            return;
        }

        $this->install_table();
    }

    public function install_table() {
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        dbDelta($this->get_schema_sql());

        if ($this->has_required_schema()) {
            update_option(self::SCHEMA_OPTION, self::SCHEMA_VERSION, false);
            return true;
        }

        return false;
    }

    private function get_schema_sql() {
        $charset_collate = $this->wpdb->get_charset_collate();

        return "CREATE TABLE {$this->table_name} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            created_at datetime NOT NULL,
            user_id bigint(20) unsigned NOT NULL DEFAULT 0,
            action varchar(100) NOT NULL DEFAULT '',
            entity_type varchar(100) NOT NULL DEFAULT '',
            entity_id bigint(20) unsigned NOT NULL DEFAULT 0,
            entity_label varchar(255) NOT NULL DEFAULT '',
            details longtext NULL,
            ip_address varchar(45) NOT NULL DEFAULT '',
            PRIMARY KEY  (id),
            KEY created_at (created_at),
            KEY user_action (user_id, action),
            KEY entity_lookup (entity_type, entity_id)
        ) {$charset_collate};";
    }

    private function has_required_schema() {
        if (! $this->table_exists($this->table_name)) {
            return false;
        }

        $quoted_table = $this->quote_table_name($this->table_name);
        $columns      = $this->wpdb->get_results("SHOW COLUMNS FROM {$quoted_table}");

        if (! is_array($columns) || empty($columns)) {
            return false;
        }

        $column_names      = array();
        $required_columns  = array('id', 'created_at', 'user_id', 'action', 'entity_type', 'entity_id', 'entity_label', 'details', 'ip_address');

        foreach ($columns as $column) {
            if (! is_object($column) || empty($column->Field)) {
                continue;
            }

            $column_names[] = (string) $column->Field;
        }

        foreach ($required_columns as $required_column) {
            if (! in_array($required_column, $column_names, true)) {
                return false;
            }
        }

        $indexes = $this->wpdb->get_results("SHOW INDEX FROM {$quoted_table}");
        if (! is_array($indexes) || empty($indexes)) {
            return false;
        }

        $index_names      = array();
        $required_indexes = array('PRIMARY', 'created_at', 'user_action', 'entity_lookup');

        foreach ($indexes as $index) {
            if (! is_object($index) || empty($index->Key_name)) {
                continue;
            }

            $index_names[] = (string) $index->Key_name;
        }

        foreach ($required_indexes as $required_index) {
            if (! in_array($required_index, $index_names, true)) {
                return false;
            }
        }

        return true;
    }

    public function log($action, $entity_type, $entity_id = 0, $entity_label = '', $details = array(), $user_id = 0, $created_at = '') {
        $action      = sanitize_key($action);
        $entity_type = sanitize_key($entity_type);
        $entity_id   = absint($entity_id);
        $entity_label = sanitize_text_field($entity_label);
        $user_id     = $user_id > 0 ? absint($user_id) : get_current_user_id();
        $created_at  = $created_at ? gmdate('Y-m-d H:i:s', strtotime((string) $created_at)) : current_time('mysql', true);
        $details     = $this->normalize_details($details);
        $ip_address  = isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : '';

        if ('' === $action || '' === $entity_type) {
            return false;
        }

        if (! $this->table_exists($this->table_name)) {
            $this->install_table();

            if (! $this->table_exists($this->table_name)) {
                return false;
            }
        }

        return false !== $this->wpdb->insert(
            $this->table_name,
            array(
                'created_at'   => $created_at,
                'user_id'      => $user_id,
                'action'       => $action,
                'entity_type'  => $entity_type,
                'entity_id'    => $entity_id,
                'entity_label' => $entity_label,
                'details'      => wp_json_encode($details, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'ip_address'   => $ip_address,
            ),
            array('%s', '%d', '%s', '%s', '%d', '%s', '%s', '%s')
        );
    }

    public function get_logs($args = array()) {
        if (! $this->table_exists($this->table_name)) {
            return array();
        }

        $defaults = array(
            'user_id' => 0,
            'limit'   => 0,
        );

        $args   = wp_parse_args($args, $defaults);
        $where  = '1=1';
        $params = array();

        if (! empty($args['user_id'])) {
            $where    .= ' AND user_id = %d';
            $params[] = absint($args['user_id']);
        }

        $sql = 'SELECT * FROM ' . $this->quote_table_name($this->table_name) . " WHERE {$where} ORDER BY created_at DESC, id DESC";

        if (! empty($args['limit'])) {
            $params[] = max(1, (int) $args['limit']);
            $sql     .= ' LIMIT %d';
        }

        if (! empty($params)) {
            $sql = $this->wpdb->prepare($sql, $params);
        }

        $rows = $this->wpdb->get_results($sql);

        if (empty($rows)) {
            return array();
        }

        foreach ($rows as $row) {
            $row->details_array = $this->decode_details($row->details);
        }

        return $rows;
    }

    public function get_user_choices() {
        if (! $this->table_exists($this->table_name)) {
            return array();
        }

        $user_ids = $this->wpdb->get_col(
            'SELECT DISTINCT user_id FROM ' . $this->quote_table_name($this->table_name) . ' WHERE user_id > 0 ORDER BY user_id ASC'
        );

        if (empty($user_ids)) {
            return array();
        }

        $users = get_users(
            array(
                'include' => array_map('absint', $user_ids),
                'orderby' => 'display_name',
                'order'   => 'ASC',
            )
        );

        return is_array($users) ? $users : array();
    }

    public function get_action_label($action) {
        $labels = array(
            'member_created'      => 'Teilnehmer erstellt',
            'member_updated'      => 'Teilnehmer geändert',
            'member_archived'     => 'Teilnehmer archiviert',
            'member_unarchived'   => 'Teilnehmer aktiviert',
            'member_deleted'      => 'Teilnehmer gelöscht',
            'settings_updated'    => 'Einstellungen geändert',
            'backup_created'      => 'Archiv erstellt',
            'backup_uploaded'     => 'Archiv hochgeladen',
            'backup_restored'     => 'Archiv wiederhergestellt',
            'backup_deleted'      => 'Archiv gelöscht',
            'backup_downloaded'   => 'Archiv heruntergeladen',
            'field_values_reset'  => 'Listenfelder zurückgesetzt',
            'mentions_updated'    => 'Verknüpfungen geändert',
        );

        return isset($labels[$action]) ? $labels[$action] : ucwords(str_replace('_', ' ', (string) $action));
    }
    public function format_details_for_display($details) {
        $details = $this->normalize_details($details);

        if (empty($details)) {
            return '';
        }

        $lines = array();

        foreach ($details as $detail) {
            if (is_array($detail) && isset($detail['field'])) {
                $before = isset($detail['before']) ? (string) $detail['before'] : '';
                $after  = isset($detail['after']) ? (string) $detail['after'] : '';

                if ('' !== $before || '' !== $after) {
                    $lines[] = sprintf(
                        '%1$s: %2$s -> %3$s',
                        sanitize_text_field((string) $detail['field']),
                        '' !== $before ? $before : 'leer',
                        '' !== $after ? $after : 'leer'
                    );
                    continue;
                }
            }

            if (is_array($detail) && isset($detail['message'])) {
                $lines[] = sanitize_text_field((string) $detail['message']);
                continue;
            }

            if (is_string($detail) && '' !== trim($detail)) {
                $lines[] = sanitize_text_field($detail);
            }
        }

        return implode("\n", $lines);
    }

    public function get_table_name() {
        return $this->table_name;
    }

    private function table_exists($table_name) {
        $table = $this->wpdb->get_var(
            $this->wpdb->prepare('SHOW TABLES LIKE %s', $table_name)
        );

        return $table === $table_name;
    }

    private function quote_table_name($table_name) {
        return '`' . str_replace('`', '``', (string) $table_name) . '`';
    }

    private function normalize_details($details) {
        if (is_string($details)) {
            $details = array(
                array(
                    'message' => $details,
                ),
            );
        }

        if (! is_array($details)) {
            return array();
        }

        return array_values(array_filter($details));
    }

    private function decode_details($details) {
        if (! is_string($details) || '' === $details) {
            return array();
        }

        $decoded = json_decode($details, true);

        return is_array($decoded) ? $decoded : array();
    }
}

<?php

if (! defined('ABSPATH')) {
    exit;
}

class Feuer_Mannschaft_Usage_Tracker {
    const CRON_HOOK       = 'feuer_mannschaft_usage_registry_sync';
    const STATUS_OPTION   = 'feuer_mannschaft_usage_registry_status';
    const SITES_TRANSIENT = 'feuer_mannschaft_usage_registry_sites';

    private static $instance = null;

    public static function instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct() {
    }

    public function register_hooks() {
        add_action('init', array($this, 'maybe_schedule_sync'));
        add_action(self::CRON_HOOK, array($this, 'scheduled_sync'));
        add_action('admin_post_feuer_mannschaft_usage_sync', array($this, 'handle_manual_sync'));
    }

    public function activate() {
        $this->maybe_schedule_sync();
        $this->sync_site('active', true);
    }

    public function deactivate() {
        $this->sync_site('inactive', true);
        $this->clear_scheduled_sync();
    }

    public function maybe_schedule_sync() {
        $settings = $this->get_settings();

        if (! $this->is_sync_enabled($settings)) {
            $this->clear_scheduled_sync();
            return;
        }

        if (! wp_next_scheduled(self::CRON_HOOK)) {
            wp_schedule_event(time() + 300, 'daily', self::CRON_HOOK);
        }
    }

    public function scheduled_sync() {
        $this->sync_site('active');
    }

    public function handle_manual_sync() {
        if (! current_user_can('manage_options')) {
            wp_die(esc_html__('Du hast keine Berechtigung fuer diese Aktion.', 'feuer-mannschaft'));
        }

        check_admin_referer('feuer_mannschaft_usage_sync');

        $success = $this->sync_site('active', true);
        $status  = get_option(self::STATUS_OPTION, array());
        $message = isset($status['last_message']) ? (string) $status['last_message'] : '';

        wp_safe_redirect(
            add_query_arg(
                array(
                    'page'  => 'feuer-mannschaft-settings',
                    'tab'   => 'usage',
                    'feuer_mannschaft_settings_notice'  => $success ? 'usage_sync_success' : 'usage_sync_failed',
                    'feuer_mannschaft_settings_message' => rawurlencode($message),
                ),
                admin_url('admin.php')
            )
        );
        exit;
    }

    public function get_status_summary($settings = null) {
        $settings = is_array($settings) ? $settings : $this->get_settings();
        $status   = get_option(self::STATUS_OPTION, array());
        $next_run = wp_next_scheduled(self::CRON_HOOK);

        return array(
            'enabled'        => ! empty($settings['enable_usage_registry']),
            'configured'     => $this->is_sync_enabled($settings),
            'endpoint_url'   => $settings['usage_registry_url'],
            'site_name'      => get_bloginfo('name'),
            'site_url'       => trailingslashit(home_url('/')),
            'last_status'    => isset($status['last_status']) ? (string) $status['last_status'] : 'never_synced',
            'last_message'   => isset($status['last_message']) ? (string) $status['last_message'] : '',
            'last_synced_at' => isset($status['last_synced_at']) ? (string) $status['last_synced_at'] : '',
            'last_http_code' => isset($status['last_http_code']) ? (int) $status['last_http_code'] : 0,
            'next_sync_at'   => $next_run ? gmdate('c', (int) $next_run) : '',
            'has_read_key'   => '' !== $settings['usage_registry_read_key'],
            'has_write_key'  => '' !== $settings['usage_registry_write_key'],
        );
    }

    public function get_registered_sites($settings = null) {
        $settings = is_array($settings) ? $settings : $this->get_settings();

        if (! $this->can_fetch_sites($settings)) {
            return array(
                'sites' => array(),
                'error' => 'Leseschluessel oder Endpoint fehlen. Die zentrale Site-Liste wird erst nach Konfiguration geladen.',
            );
        }

        $cached = get_transient(self::SITES_TRANSIENT);
        if (is_array($cached) && isset($cached['sites'])) {
            return $cached;
        }

        $response = wp_safe_remote_get(
            add_query_arg(array('action' => 'list'), $settings['usage_registry_url']),
            array(
                'timeout' => 12,
                'headers' => array(
                    'Accept'                         => 'application/json',
                    'X-Feuer-Mannschaft-Read-Key'   => $settings['usage_registry_read_key'],
                ),
            )
        );

        if (is_wp_error($response)) {
            return array(
                'sites' => array(),
                'error' => $response->get_error_message(),
            );
        }

        $code = (int) wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        $data = json_decode((string) $body, true);

        if (200 !== $code || ! is_array($data) || empty($data['success'])) {
            return array(
                'sites' => array(),
                'error' => isset($data['message']) ? sanitize_text_field((string) $data['message']) : 'Die zentrale Site-Liste konnte nicht geladen werden.',
            );
        }

        $sites = isset($data['sites']) && is_array($data['sites']) ? array_values($data['sites']) : array();

        usort(
            $sites,
            function ($left, $right) {
                return strcmp(
                    isset($right['last_seen_at']) ? (string) $right['last_seen_at'] : '',
                    isset($left['last_seen_at']) ? (string) $left['last_seen_at'] : ''
                );
            }
        );

        $result = array(
            'sites' => $sites,
            'error' => '',
        );

        set_transient(self::SITES_TRANSIENT, $result, 15 * MINUTE_IN_SECONDS);

        return $result;
    }

    public function sync_site($status = 'active', $force = false) {
        $settings = $this->get_settings();

        if (! $force && ! $this->is_sync_enabled($settings)) {
            return false;
        }

        if (! $this->validate_write_config($settings)) {
            $this->update_status(
                array(
                    'last_status'    => 'configuration_error',
                    'last_message'   => 'Write Key oder Registry Endpoint fehlen.',
                    'last_http_code' => 0,
                )
            );
            return false;
        }

        $response = wp_safe_remote_post(
            $settings['usage_registry_url'],
            array(
                'timeout'     => 12,
                'headers'     => array(
                    'Accept'                         => 'application/json',
                    'Content-Type'                   => 'application/json',
                    'X-Feuer-Mannschaft-Write-Key'  => $settings['usage_registry_write_key'],
                ),
                'body'        => wp_json_encode($this->build_payload($status)),
                'data_format' => 'body',
            )
        );

        if (is_wp_error($response)) {
            $this->update_status(
                array(
                    'last_status'    => 'request_error',
                    'last_message'   => $response->get_error_message(),
                    'last_http_code' => 0,
                )
            );
            return false;
        }

        $code = (int) wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        $data = json_decode((string) $body, true);

        if (200 !== $code || ! is_array($data) || empty($data['success'])) {
            $this->update_status(
                array(
                    'last_status'    => 'remote_error',
                    'last_message'   => isset($data['message']) ? sanitize_text_field((string) $data['message']) : 'Die Site-Registry hat keine erfolgreiche Antwort geliefert.',
                    'last_http_code' => $code,
                )
            );
            return false;
        }

        $this->update_status(
            array(
                'last_status'    => sanitize_key($status),
                'last_message'   => isset($data['message']) ? sanitize_text_field((string) $data['message']) : 'Site-Registry erfolgreich aktualisiert.',
                'last_http_code' => $code,
            )
        );

        delete_transient(self::SITES_TRANSIENT);

        return true;
    }

    private function build_payload($status) {
        return array(
            'action'         => 'register',
            'plugin_slug'    => 'feuer-mannschaft',
            'site_url'       => trailingslashit(home_url('/')),
            'site_name'      => get_bloginfo('name'),
            'plugin_version' => FEUER_MANNSCHAFT_VERSION,
            'wp_version'     => get_bloginfo('version'),
            'php_version'    => PHP_VERSION,
            'locale'         => get_locale(),
            'is_multisite'   => is_multisite() ? 1 : 0,
            'status'         => in_array($status, array('active', 'inactive'), true) ? $status : 'active',
            'synced_at'      => gmdate('c'),
        );
    }

    private function update_status($status_update) {
        $status = get_option(
            self::STATUS_OPTION,
            array(
                'last_status'    => 'never_synced',
                'last_message'   => '',
                'last_synced_at' => '',
                'last_http_code' => 0,
            )
        );

        $status['last_status']    = isset($status_update['last_status']) ? sanitize_key((string) $status_update['last_status']) : $status['last_status'];
        $status['last_message']   = isset($status_update['last_message']) ? sanitize_text_field((string) $status_update['last_message']) : $status['last_message'];
        $status['last_http_code'] = isset($status_update['last_http_code']) ? (int) $status_update['last_http_code'] : $status['last_http_code'];
        $status['last_synced_at'] = gmdate('c');

        update_option(self::STATUS_OPTION, $status, false);
    }

    private function can_fetch_sites($settings) {
        return ! empty($settings['usage_registry_url']) && '' !== (string) $settings['usage_registry_read_key'];
    }

    private function is_sync_enabled($settings) {
        return ! empty($settings['enable_usage_registry']) && $this->validate_write_config($settings);
    }

    private function validate_write_config($settings) {
        return ! empty($settings['usage_registry_url']) && '' !== (string) $settings['usage_registry_write_key'];
    }

    private function clear_scheduled_sync() {
        $timestamp = wp_next_scheduled(self::CRON_HOOK);

        while ($timestamp) {
            wp_unschedule_event($timestamp, self::CRON_HOOK);
            $timestamp = wp_next_scheduled(self::CRON_HOOK);
        }
    }

    private function get_settings() {
        $defaults = array(
            'enable_usage_registry'   => 1,
            'usage_registry_url'      => defined('FEUER_MANNSCHAFT_USAGE_REGISTRY_URL') ? FEUER_MANNSCHAFT_USAGE_REGISTRY_URL : '',
            'usage_registry_write_key'=> '',
            'usage_registry_read_key' => '',
        );

        $saved = get_option(Feuer_Mannschaft_Plugin::OPTION_KEY, array());
        $saved = is_array($saved) ? $saved : array();
        $settings = wp_parse_args($saved, $defaults);

        $settings['enable_usage_registry']    = ! empty($settings['enable_usage_registry']) ? 1 : 0;
        $settings['usage_registry_url']       = $this->sanitize_registry_url(isset($settings['usage_registry_url']) ? $settings['usage_registry_url'] : '');
        $settings['usage_registry_write_key'] = $this->sanitize_registry_key(isset($settings['usage_registry_write_key']) ? $settings['usage_registry_write_key'] : '');
        $settings['usage_registry_read_key']  = $this->sanitize_registry_key(isset($settings['usage_registry_read_key']) ? $settings['usage_registry_read_key'] : '');

        return $settings;
    }

    private function sanitize_registry_url($url) {
        $url = esc_url_raw(trim((string) $url));

        if ('' === $url && defined('FEUER_MANNSCHAFT_USAGE_REGISTRY_URL')) {
            return FEUER_MANNSCHAFT_USAGE_REGISTRY_URL;
        }

        return $url;
    }

    private function sanitize_registry_key($value) {
        return trim(sanitize_text_field((string) $value));
    }
}

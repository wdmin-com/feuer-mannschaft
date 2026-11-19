<?php

if (! defined('ABSPATH')) {
    exit;
}

class Feuer_Mannschaft_Updater {
    const METADATA_TRANSIENT = 'feuer_mannschaft_remote_update_metadata';

    private static $instance = null;
    private $plugin_basename = '';
    private $plugin_slug     = 'feuer-mannschaft';
    private $last_metadata_error = '';
    private $last_metadata_url   = '';

    public static function instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct() {
        $this->plugin_basename = plugin_basename(FEUER_MANNSCHAFT_PLUGIN_FILE);

        add_filter('pre_set_site_transient_update_plugins', array($this, 'filter_update_transient'));
        add_filter('site_transient_update_plugins', array($this, 'filter_update_transient'));
        add_filter('plugins_api', array($this, 'filter_plugin_information'), 20, 3);
        add_filter('auto_update_plugin', array($this, 'disable_automatic_update'), 10, 2);
        add_filter('upgrader_pre_download', array($this, 'verify_package_download'), 10, 4);
        add_action('upgrader_process_complete', array($this, 'purge_update_cache'), 10, 2);
        add_action('admin_notices', array($this, 'render_update_notice'));
    }

    public function filter_update_transient($transient) {
        if (! is_object($transient)) {
            $transient = new stdClass();
        }

        $metadata = $this->get_remote_metadata();
        if (empty($metadata) || empty($metadata['version'])) {
            return $transient;
        }

        $plugin_update = $this->build_update_payload($metadata);
        $installed_version = FEUER_MANNSCHAFT_VERSION;

        if (! empty($transient->checked) && isset($transient->checked[$this->plugin_basename])) {
            $installed_version = (string) $transient->checked[$this->plugin_basename];
        }

        if (version_compare($metadata['version'], $installed_version, '>')) {
            $transient->response[$this->plugin_basename] = $plugin_update;
            unset($transient->no_update[$this->plugin_basename]);
        } else {
            $transient->no_update[$this->plugin_basename] = $plugin_update;
            unset($transient->response[$this->plugin_basename]);
        }

        return $transient;
    }

    public function filter_plugin_information($result, $action, $args) {
        if ('plugin_information' !== $action || ! is_object($args) || empty($args->slug) || $this->plugin_slug !== $args->slug) {
            return $result;
        }

        $metadata = $this->get_remote_metadata();
        if (empty($metadata) || empty($metadata['version'])) {
            return $result;
        }

        $sections = array(
            'description'  => isset($metadata['sections']['description']) ? wp_kses_post($metadata['sections']['description']) : '',
            'installation' => isset($metadata['sections']['installation']) ? wp_kses_post($metadata['sections']['installation']) : '',
            'changelog'    => isset($metadata['sections']['changelog']) ? wp_kses_post($metadata['sections']['changelog']) : '',
        );

        $info = new stdClass();
        $info->name          = isset($metadata['name']) && '' !== trim((string) $metadata['name']) ? (string) $metadata['name'] : 'Mannschaft';
        $info->slug          = $this->plugin_slug;
        $info->plugin        = $this->plugin_basename;
        $info->version       = (string) $metadata['version'];
        $info->author        = isset($metadata['author']) ? wp_kses_post($metadata['author']) : '<a href="https://wdmin.com">Walter Faerber</a>';
        $info->author_profile = isset($metadata['author_profile']) ? esc_url_raw((string) $metadata['author_profile']) : 'https://wdmin.com';
        $info->homepage      = isset($metadata['homepage']) ? esc_url_raw((string) $metadata['homepage']) : FEUER_MANNSCHAFT_UPDATE_BASE_URL;
        $info->download_link = $this->get_package_url($metadata);
        $info->requires      = isset($metadata['requires']) ? (string) $metadata['requires'] : '';
        $info->requires_php  = isset($metadata['requires_php']) ? (string) $metadata['requires_php'] : '8.1';
        $info->tested        = isset($metadata['tested']) ? (string) $metadata['tested'] : '';
        $info->last_updated  = isset($metadata['last_updated']) ? (string) $metadata['last_updated'] : '';
        $info->sections      = $sections;
        $info->banners       = array();
        $info->icons         = array();

        return $info;
    }

    public function disable_automatic_update($update, $item) {
        if (! $this->is_own_update_item($item)) {
            return $update;
        }

        return function_exists('feuer_mannschaft_plugin') && feuer_mannschaft_plugin()->are_automatic_updates_enabled();
    }

    public function verify_package_download($reply, $package, $upgrader, $hook_extra) {
        unset($upgrader);

        if (! empty($reply) || ! $this->is_own_upgrade_context($hook_extra)) {
            return $reply;
        }

        $metadata = $this->get_remote_metadata();
        if (empty($metadata['package_sha256'])) {
            return new WP_Error(
                'feuer_mannschaft_update_checksum_missing',
                __('Das Mannschaft-Update wurde abgebrochen, weil keine ZIP-Pruefsumme veroeffentlicht wurde.', 'feuer-mannschaft')
            );
        }

        $expected_package = $this->get_package_url($metadata);
        if (esc_url_raw((string) $package) !== esc_url_raw((string) $expected_package)) {
            return new WP_Error(
                'feuer_mannschaft_update_package_mismatch',
                __('Das Mannschaft-Update wurde abgebrochen, weil die Paket-URL nicht zu den veroeffentlichten Metadaten passt.', 'feuer-mannschaft')
            );
        }

        if (! function_exists('download_url')) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
        }

        $downloaded_file = download_url($package, 300);
        if (is_wp_error($downloaded_file)) {
            return $downloaded_file;
        }

        $actual_hash   = hash_file('sha256', $downloaded_file);
        $expected_hash = strtolower((string) $metadata['package_sha256']);

        if (! is_string($actual_hash) || ! hash_equals($expected_hash, strtolower($actual_hash))) {
            wp_delete_file($downloaded_file);

            return new WP_Error(
                'feuer_mannschaft_update_checksum_failed',
                __('Das Mannschaft-Update wurde abgebrochen, weil die ZIP-Pruefsumme nicht mit den veroeffentlichten Metadaten uebereinstimmt.', 'feuer-mannschaft')
            );
        }

        return $downloaded_file;
    }

    public function purge_update_cache($upgrader, $options) {
        if (! is_array($options) || empty($options['type']) || 'plugin' !== $options['type']) {
            return;
        }

        delete_site_transient(self::METADATA_TRANSIENT);
        delete_site_transient('update_plugins');
    }

    public function get_update_status($force = false) {
        if ($force) {
            delete_site_transient(self::METADATA_TRANSIENT);
            delete_site_transient('update_plugins');
        }

        $metadata = $this->get_remote_metadata();

        if ($force && function_exists('wp_update_plugins')) {
            wp_update_plugins();
        }

        $remote_version = ! empty($metadata['version']) ? (string) $metadata['version'] : '';

        return array(
            'metadata'          => $metadata,
            'installed_version' => FEUER_MANNSCHAFT_VERSION,
            'remote_version'    => $remote_version,
            'has_update'        => '' !== $remote_version && version_compare($remote_version, FEUER_MANNSCHAFT_VERSION, '>'),
            'package_url'       => ! empty($metadata) ? $this->get_package_url($metadata) : '',
            'package_sha256'    => ! empty($metadata['package_sha256']) ? (string) $metadata['package_sha256'] : '',
            'metadata_url'      => $this->last_metadata_url,
            'metadata_error'    => $this->last_metadata_error,
        );
    }

    public function render_update_notice() {
        if (! current_user_can('update_plugins') || wp_doing_ajax()) {
            return;
        }

        $screen = function_exists('get_current_screen') ? get_current_screen() : null;
        if ($screen && 'plugins' === $screen->base) {
            return;
        }

        $metadata = $this->get_remote_metadata();
        if (empty($metadata) || empty($metadata['version']) || ! version_compare($metadata['version'], FEUER_MANNSCHAFT_VERSION, '>')) {
            return;
        }

        $plugins_url = admin_url('plugins.php');
        printf(
            '<div class="notice notice-info"><p>%s <a href="%s">%s</a></p></div>',
            esc_html(sprintf('Fuer Mannschaft ist Version %1$s verfuegbar. Installiert ist aktuell %2$s.', $metadata['version'], FEUER_MANNSCHAFT_VERSION)),
            esc_url($plugins_url),
            esc_html__('Zur Plugin-Uebersicht', 'feuer-mannschaft')
        );
    }

    private function get_remote_metadata() {
        $cached = get_site_transient(self::METADATA_TRANSIENT);
        if (is_array($cached)) {
            $this->last_metadata_error = '';
            $this->last_metadata_url   = FEUER_MANNSCHAFT_UPDATE_METADATA_URL;

            return $cached;
        }

        $this->last_metadata_error = '';
        $this->last_metadata_url   = '';

        foreach ($this->get_metadata_urls() as $metadata_url) {
            $this->last_metadata_url = $metadata_url;
            $response = wp_safe_remote_get(
                $metadata_url,
                array(
                    'timeout' => 10,
                    'headers' => array(
                        'Accept' => 'application/json',
                    ),
                )
            );

            if (is_wp_error($response)) {
                $this->last_metadata_error = $response->get_error_message();
                continue;
            }

            $status_code = (int) wp_remote_retrieve_response_code($response);
            if (200 !== $status_code) {
                $this->last_metadata_error = sprintf('HTTP %d fuer %s', $status_code, $metadata_url);
                continue;
            }

            $body = wp_remote_retrieve_body($response);
            if ('' === trim((string) $body)) {
                $this->last_metadata_error = sprintf('Leere update.json unter %s', $metadata_url);
                continue;
            }

            $data = json_decode($body, true);
            if (! is_array($data)) {
                $this->last_metadata_error = sprintf('Ungueltiges JSON unter %s', $metadata_url);
                continue;
            }

            $metadata = $this->normalize_metadata($data);
            if (empty($metadata)) {
                $this->last_metadata_error = sprintf('Ungueltige Update-Metadaten unter %s', $metadata_url);
                continue;
            }

            $this->last_metadata_error = '';
            set_site_transient(self::METADATA_TRANSIENT, $metadata, 6 * HOUR_IN_SECONDS);

            return $metadata;
        }

        if ('' === $this->last_metadata_error) {
            $this->last_metadata_error = 'Keine Update-Metadaten verfuegbar.';
        }

        return array();
    }

    private function normalize_metadata($data) {
        $version = isset($data['version']) ? sanitize_text_field((string) $data['version']) : '';
        if ('' === $version) {
            return array();
        }

        $sections = array();
        if (isset($data['sections']) && is_array($data['sections'])) {
            foreach ($data['sections'] as $section_key => $section_content) {
                $section_key = sanitize_key((string) $section_key);
                if ('' === $section_key) {
                    continue;
                }

                $sections[$section_key] = wp_kses_post((string) $section_content);
            }
        }

        $package_sha256 = isset($data['package_sha256']) ? $this->normalize_sha256((string) $data['package_sha256']) : '';
        $download_url   = isset($data['download_url']) ? esc_url_raw((string) $data['download_url']) : FEUER_MANNSCHAFT_UPDATE_PACKAGE_URL;

        if (! $this->is_allowed_package_url($download_url)) {
            return array();
        }

        return array(
            'name'           => isset($data['name']) ? sanitize_text_field((string) $data['name']) : 'Mannschaft',
            'version'        => $version,
            'homepage'       => isset($data['homepage']) ? esc_url_raw((string) $data['homepage']) : FEUER_MANNSCHAFT_UPDATE_BASE_URL,
            'download_url'   => $download_url,
            'package_sha256' => $package_sha256,
            'author'         => isset($data['author']) ? (string) $data['author'] : '<a href="https://wdmin.com">Walter Faerber</a>',
            'author_profile' => isset($data['author_profile']) ? esc_url_raw((string) $data['author_profile']) : 'https://wdmin.com',
            'requires'       => isset($data['requires']) ? sanitize_text_field((string) $data['requires']) : '',
            'tested'         => isset($data['tested']) ? sanitize_text_field((string) $data['tested']) : '',
            'requires_php'   => isset($data['requires_php']) ? sanitize_text_field((string) $data['requires_php']) : '8.1',
            'last_updated'   => isset($data['last_updated']) ? sanitize_text_field((string) $data['last_updated']) : '',
            'sections'       => $sections,
        );
    }

    private function build_update_payload($metadata) {
        $payload = new stdClass();
        $payload->slug = $this->plugin_slug;
        $payload->plugin = $this->plugin_basename;
        $payload->new_version = $metadata['version'];
        $payload->url = isset($metadata['homepage']) ? $metadata['homepage'] : FEUER_MANNSCHAFT_UPDATE_BASE_URL;
        $payload->package = $this->get_package_url($metadata);
        $payload->requires = isset($metadata['requires']) ? $metadata['requires'] : '';
        $payload->tested = isset($metadata['tested']) ? $metadata['tested'] : '';
        $payload->requires_php = isset($metadata['requires_php']) ? $metadata['requires_php'] : '8.1';

        return $payload;
    }

    private function is_own_upgrade_context($hook_extra) {
        if (! is_array($hook_extra) || empty($hook_extra['plugin'])) {
            return false;
        }

        return $this->plugin_basename === (string) $hook_extra['plugin'];
    }

    private function is_own_update_item($item) {
        if (is_object($item)) {
            if (! empty($item->plugin) && $this->plugin_basename === (string) $item->plugin) {
                return true;
            }

            return ! empty($item->slug) && $this->plugin_slug === (string) $item->slug;
        }

        if (is_array($item)) {
            if (! empty($item['plugin']) && $this->plugin_basename === (string) $item['plugin']) {
                return true;
            }

            return ! empty($item['slug']) && $this->plugin_slug === (string) $item['slug'];
        }

        return false;
    }

    private function normalize_sha256($value) {
        $value = strtolower(trim((string) $value));

        return preg_match('/\A[a-f0-9]{64}\z/', $value) ? $value : '';
    }

    private function get_metadata_urls() {
        $urls = array(
            FEUER_MANNSCHAFT_UPDATE_METADATA_URL,
            'https://wdmin.com/plugins/feuer-mannschaft/update.json',
            'https://walterfaerber.eu/plugins/feuer-mannschaft/update.json',
        );

        return array_values(array_unique(array_filter(array_map('esc_url_raw', $urls))));
    }

    private function get_package_url($metadata) {
        if (isset($metadata['download_url']) && '' !== trim((string) $metadata['download_url']) && $this->is_allowed_package_url($metadata['download_url'])) {
            return $metadata['download_url'];
        }

        return FEUER_MANNSCHAFT_UPDATE_PACKAGE_URL;
    }

    private function is_allowed_package_url($url) {
        $url      = esc_url_raw((string) $url);
        $expected = esc_url_raw(FEUER_MANNSCHAFT_UPDATE_BASE_URL);

        if ('' === $url) {
            return false;
        }

        $url_host      = strtolower((string) wp_parse_url($url, PHP_URL_HOST));
        $expected_host = strtolower((string) wp_parse_url($expected, PHP_URL_HOST));
        $url_path      = (string) wp_parse_url($url, PHP_URL_PATH);
        $expected_path = trailingslashit((string) wp_parse_url($expected, PHP_URL_PATH));

        return 'https' === strtolower((string) wp_parse_url($url, PHP_URL_SCHEME))
            && $url_host === $expected_host
            && 0 === strpos($url_path, $expected_path)
            && 'zip' === strtolower(pathinfo($url_path, PATHINFO_EXTENSION));
    }
}

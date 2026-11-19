<?php

if (! defined('ABSPATH')) {
    exit;
}

class Feuer_Mannschaft_Updater {
    const METADATA_TRANSIENT = 'feuer_mannschaft_remote_update_metadata';

    private static $instance = null;
    private $plugin_basename = '';
    private $plugin_slug     = 'feuer-mannschaft';

    public static function instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct() {
        $this->plugin_basename = plugin_basename(FEUER_MANNSCHAFT_PLUGIN_FILE);

        add_filter('pre_set_site_transient_update_plugins', array($this, 'filter_update_transient'));
        add_filter('plugins_api', array($this, 'filter_plugin_information'), 20, 3);
        add_action('upgrader_process_complete', array($this, 'purge_update_cache'), 10, 2);
        add_action('admin_notices', array($this, 'render_update_notice'));
    }

    public function filter_update_transient($transient) {
        if (! is_object($transient)) {
            $transient = new stdClass();
        }

        if (empty($transient->checked) || ! isset($transient->checked[$this->plugin_basename])) {
            return $transient;
        }

        $metadata = $this->get_remote_metadata();
        if (empty($metadata) || empty($metadata['version'])) {
            return $transient;
        }

        $plugin_update = $this->build_update_payload($metadata);

        if (version_compare($metadata['version'], FEUER_MANNSCHAFT_VERSION, '>')) {
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
        $info->name          = isset($metadata['name']) && '' !== trim((string) $metadata['name']) ? (string) $metadata['name'] : 'Feuer-Mannschaft';
        $info->slug          = $this->plugin_slug;
        $info->plugin        = $this->plugin_basename;
        $info->version       = (string) $metadata['version'];
        $info->author        = isset($metadata['author']) ? wp_kses_post($metadata['author']) : '<a href="https://wdmin.com">Walter Faerber</a>';
        $info->author_profile = isset($metadata['author_profile']) ? esc_url_raw((string) $metadata['author_profile']) : 'https://wdmin.com';
        $info->homepage      = isset($metadata['homepage']) ? esc_url_raw((string) $metadata['homepage']) : FEUER_MANNSCHAFT_UPDATE_BASE_URL;
        $info->download_link = $this->get_package_url($metadata);
        $info->requires      = isset($metadata['requires']) ? (string) $metadata['requires'] : '';
        $info->requires_php  = isset($metadata['requires_php']) ? (string) $metadata['requires_php'] : '8.0';
        $info->tested        = isset($metadata['tested']) ? (string) $metadata['tested'] : '';
        $info->last_updated  = isset($metadata['last_updated']) ? (string) $metadata['last_updated'] : '';
        $info->sections      = $sections;
        $info->banners       = array();
        $info->icons         = array();

        return $info;
    }

    public function purge_update_cache($upgrader, $options) {
        if (! is_array($options) || empty($options['type']) || 'plugin' !== $options['type']) {
            return;
        }

        delete_site_transient(self::METADATA_TRANSIENT);
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
            esc_html(sprintf('Fuer Feuer-Mannschaft ist Version %1$s verfuegbar. Installiert ist aktuell %2$s.', $metadata['version'], FEUER_MANNSCHAFT_VERSION)),
            esc_url($plugins_url),
            esc_html__('Zur Plugin-Uebersicht', 'feuer-mannschaft')
        );
    }

    private function get_remote_metadata() {
        $cached = get_site_transient(self::METADATA_TRANSIENT);
        if (is_array($cached)) {
            return $cached;
        }

        $response = wp_safe_remote_get(
            FEUER_MANNSCHAFT_UPDATE_METADATA_URL,
            array(
                'timeout' => 10,
                'headers' => array(
                    'Accept' => 'application/json',
                ),
            )
        );

        if (is_wp_error($response)) {
            return array();
        }

        $status_code = (int) wp_remote_retrieve_response_code($response);
        if (200 !== $status_code) {
            return array();
        }

        $body = wp_remote_retrieve_body($response);
        if ('' === trim((string) $body)) {
            return array();
        }

        $data = json_decode($body, true);
        if (! is_array($data)) {
            return array();
        }

        $metadata = $this->normalize_metadata($data);
        if (empty($metadata)) {
            return array();
        }

        set_site_transient(self::METADATA_TRANSIENT, $metadata, 6 * HOUR_IN_SECONDS);

        return $metadata;
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

        return array(
            'name'           => isset($data['name']) ? sanitize_text_field((string) $data['name']) : 'Feuer-Mannschaft',
            'version'        => $version,
            'homepage'       => isset($data['homepage']) ? esc_url_raw((string) $data['homepage']) : FEUER_MANNSCHAFT_UPDATE_BASE_URL,
            'download_url'   => isset($data['download_url']) ? esc_url_raw((string) $data['download_url']) : FEUER_MANNSCHAFT_UPDATE_PACKAGE_URL,
            'author'         => isset($data['author']) ? (string) $data['author'] : '<a href="https://wdmin.com">Walter Faerber</a>',
            'author_profile' => isset($data['author_profile']) ? esc_url_raw((string) $data['author_profile']) : 'https://wdmin.com',
            'requires'       => isset($data['requires']) ? sanitize_text_field((string) $data['requires']) : '',
            'tested'         => isset($data['tested']) ? sanitize_text_field((string) $data['tested']) : '',
            'requires_php'   => isset($data['requires_php']) ? sanitize_text_field((string) $data['requires_php']) : '8.0',
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
        $payload->requires_php = isset($metadata['requires_php']) ? $metadata['requires_php'] : '8.0';

        return $payload;
    }

    private function get_package_url($metadata) {
        if (isset($metadata['download_url']) && '' !== trim((string) $metadata['download_url'])) {
            return $metadata['download_url'];
        }

        return FEUER_MANNSCHAFT_UPDATE_PACKAGE_URL;
    }
}

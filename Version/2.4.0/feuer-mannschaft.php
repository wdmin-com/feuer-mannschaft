<?php
/**
 * Plugin Name: Feuer-Mannschaft
 * Plugin URI: https://wdmin.com/plugins/feuer-mannschaft/
 * Description: Mannschaft verwaltet Teilnehmerprofile als eigenen WordPress-Inhaltstyp mit Fotoverwaltung, Kategorien, Archivstatus und Frontend-Ausgabe.
 * Version: 2.4.0
 * Author: Walter Faerber
 * Author URI: https://wdmin.com/
 * Requires at least: 7.1
 * Tested up to: 7.1.3
 * Requires PHP: 8.1
 * Text Domain: feuer-mannschaft
 */

if (! defined('ABSPATH')) {
    exit;
}

define('FEUER_MANNSCHAFT_VERSION', '2.4.0');
define('FEUER_MANNSCHAFT_PLUGIN_FILE', __FILE__);
define('FEUER_MANNSCHAFT_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('FEUER_MANNSCHAFT_PLUGIN_URL', plugin_dir_url(__FILE__));
define('FEUER_MANNSCHAFT_UPDATE_BASE_URL', 'https://wdmin.com/plugins/feuer-mannschaft/');
define('FEUER_MANNSCHAFT_UPDATE_METADATA_URL', FEUER_MANNSCHAFT_UPDATE_BASE_URL . 'update.json');
define('FEUER_MANNSCHAFT_UPDATE_PACKAGE_URL', FEUER_MANNSCHAFT_UPDATE_BASE_URL . 'release/latest/feuer-mannschaft.zip');
function feuer_mannschaft_is_supported_php() {
    return version_compare(PHP_VERSION, '8.1', '>=');
}

function feuer_mannschaft_render_php_notice() {
    if (! current_user_can('activate_plugins')) {
        return;
    }

    printf(
        '<div class="notice notice-error"><p>%s</p></div>',
        esc_html(sprintf('Mannschaft benoetigt PHP 8.1 oder hoeher. Aktuell installiert: %s.', PHP_VERSION))
    );
}

if (! feuer_mannschaft_is_supported_php()) {
    add_action('admin_notices', 'feuer_mannschaft_render_php_notice');
    return;
}

require_once FEUER_MANNSCHAFT_PLUGIN_DIR . 'includes/class-feuer-mannschaft-logger.php';
require_once FEUER_MANNSCHAFT_PLUGIN_DIR . 'includes/class-feuer-mannschaft-mentions.php';
require_once FEUER_MANNSCHAFT_PLUGIN_DIR . 'includes/class-feuer-mannschaft-backup-manager.php';
require_once FEUER_MANNSCHAFT_PLUGIN_DIR . 'includes/class-feuer-mannschaft-updater.php';
require_once FEUER_MANNSCHAFT_PLUGIN_DIR . 'includes/class-feuer-mannschaft-plugin.php';
require_once FEUER_MANNSCHAFT_PLUGIN_DIR . 'includes/class-feuer-mannschaft-toolkit.php';
require_once FEUER_MANNSCHAFT_PLUGIN_DIR . 'includes/class-feuer-mannschaft-complete.php';

function feuer_mannschaft_plugin() {
    return Feuer_Mannschaft_Plugin::instance();
}

function feuer_mannschaft_updater() {
    return Feuer_Mannschaft_Updater::instance();
}

function feuer_mannschaft_activate() {
    if (! feuer_mannschaft_is_supported_php()) {
        deactivate_plugins(plugin_basename(__FILE__));

        wp_die(
            esc_html(sprintf('Mannschaft benoetigt PHP 8.1 oder hoeher. Aktuell installiert: %s.', PHP_VERSION)),
            esc_html__('PHP-Version nicht unterstuetzt', 'feuer-mannschaft')
        );
    }

    Feuer_Mannschaft_Plugin::activate();
}

function feuer_mannschaft_deactivate() {
    Feuer_Mannschaft_Plugin::deactivate();
}

function feuer_mannschaft_cleanup_removed_usage_registry() {
    if (get_option('feuer_mannschaft_usage_registry_removed')) {
        return;
    }

    $timestamp = wp_next_scheduled('feuer_mannschaft_usage_registry_sync');
    while ($timestamp) {
        wp_unschedule_event($timestamp, 'feuer_mannschaft_usage_registry_sync');
        $timestamp = wp_next_scheduled('feuer_mannschaft_usage_registry_sync');
    }

    delete_option('feuer_mannschaft_usage_registry_status');
    delete_transient('feuer_mannschaft_usage_registry_sites');

    $settings = get_option(Feuer_Mannschaft_Plugin::OPTION_KEY, array());
    if (is_array($settings)) {
        unset(
            $settings['enable_usage_registry'],
            $settings['usage_registry_url'],
            $settings['usage_registry_write_key'],
            $settings['usage_registry_read_key']
        );

        update_option(Feuer_Mannschaft_Plugin::OPTION_KEY, $settings, false);
    }

    update_option('feuer_mannschaft_usage_registry_removed', time(), false);
}

register_activation_hook(__FILE__, 'feuer_mannschaft_activate');
register_deactivation_hook(__FILE__, 'feuer_mannschaft_deactivate');

feuer_mannschaft_updater();
feuer_mannschaft_plugin();
Feuer_Mannschaft_Toolkit::instance();
Feuer_Mannschaft_Complete::instance();
feuer_mannschaft_cleanup_removed_usage_registry();

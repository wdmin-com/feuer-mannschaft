<?php
/**
 * Plugin Name: Feuer-Mannschaft
 * Plugin URI: https://ff-lurup.de
 * Description: Feuer-Mannschaft verwaltet Teilnehmerprofile als reguläre WordPress-Beiträge mit Fotoverwaltung, Kategorien, Archivstatus und Frontend-Ausgabe.
 * Version: 2.3.15
 * Author: Walter Färber
 * Author URI: https://ff-lurup.de
 * Requires PHP: 8.0
 * Text Domain: feuer-mannschaft
 */

if (!defined('ABSPATH')) {
    exit;
}

define('FEUER_MANNSCHAFT_VERSION', '2.3.15');
define('FEUER_MANNSCHAFT_PLUGIN_FILE', __FILE__);
define('FEUER_MANNSCHAFT_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('FEUER_MANNSCHAFT_PLUGIN_URL', plugin_dir_url(__FILE__));

function feuer_mannschaft_is_supported_php() {
    return version_compare(PHP_VERSION, '8.0', '>=');
}

function feuer_mannschaft_render_php_notice() {
    if (! current_user_can('activate_plugins')) {
        return;
    }

    printf(
        '<div class="notice notice-error"><p>%s</p></div>',
        esc_html(sprintf('Feuer-Mannschaft benötigt PHP 8.0 oder höher. Aktuell installiert: %s.', PHP_VERSION))
    );
}

if (! feuer_mannschaft_is_supported_php()) {
    add_action('admin_notices', 'feuer_mannschaft_render_php_notice');
    return;
}

require_once FEUER_MANNSCHAFT_PLUGIN_DIR . 'includes/class-feuer-mannschaft-logger.php';
require_once FEUER_MANNSCHAFT_PLUGIN_DIR . 'includes/class-feuer-mannschaft-mentions.php';
require_once FEUER_MANNSCHAFT_PLUGIN_DIR . 'includes/class-feuer-mannschaft-backup-manager.php';
require_once FEUER_MANNSCHAFT_PLUGIN_DIR . 'includes/class-feuer-mannschaft-plugin.php';

function feuer_mannschaft_plugin() {
    return Feuer_Mannschaft_Plugin::instance();
}

function feuer_mannschaft_activate() {
    if (! feuer_mannschaft_is_supported_php()) {
        deactivate_plugins(plugin_basename(__FILE__));

        wp_die(
            esc_html(sprintf('Feuer-Mannschaft benötigt PHP 8.0 oder höher. Aktuell installiert: %s.', PHP_VERSION)),
            esc_html__('PHP-Version nicht unterstützt', 'feuer-mannschaft')
        );
    }

    Feuer_Mannschaft_Plugin::activate();
}

function feuer_mannschaft_deactivate() {
    Feuer_Mannschaft_Plugin::deactivate();
}

register_activation_hook(__FILE__, 'feuer_mannschaft_activate');
register_deactivation_hook(__FILE__, 'feuer_mannschaft_deactivate');

feuer_mannschaft_plugin();




<?php
/**
 * Plugin Name: MantraCal
 * Plugin URI:  https://webreform.de
 * Description: Ein einfaches Kalender-Plugin für WordPress.
 * Version:     1.0.0
 * Author:      Thomas Kirschnick
 * Author URI:  https://webreform.de
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: mantracal
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

define('MANTRACAL_VERSION', '1.0.0');

/**
 * The code that runs during plugin activation.
 */
function activate_mantracal() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-mantracal-activator.php';
    MantraCal_Activator::activate();
}
register_activation_hook(__FILE__, 'activate_mantracal');

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_mantracal() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-mantracal-deactivator.php';
    MantraCal_Deactivator::deactivate();
}
register_deactivation_hook(__FILE__, 'deactivate_mantracal');

/**
 * Load dependencies and initialize the plugin.
 */
function run_mantracal() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-mantracal-cpt.php';
    require_once plugin_dir_path(__FILE__) . 'includes/class-mantracal-shortcodes.php';

    MantraCal_CPT::register();
    MantraCal_Shortcodes::register();
}
add_action('plugins_loaded', 'run_mantracal');

/**
 * Load plugin textdomain for translations.
 */
function mantracal_load_textdomain() {
    load_plugin_textdomain('mantracal', false, basename(dirname(__FILE__)) . '/languages');
}
add_action('plugins_loaded', 'mantracal_load_textdomain');

/**
 * Enqueue scripts and styles.
 */
function mantracal_enqueue_scripts() {
    wp_enqueue_style('mantracal-calendar', plugin_dir_url(__FILE__) . 'assets/css/mantracal-calendar.css', array(), MANTRACAL_VERSION);
    wp_enqueue_script('mantracal-calendar', plugin_dir_url(__FILE__) . 'assets/js/mantracal-calendar.js', array('jquery'), MANTRACAL_VERSION, true);
}
add_action('wp_enqueue_scripts', 'mantracal_enqueue_scripts');
<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://genosha.com.ar
 * @since             1.0.1
 * @package           Tar_Google_Ads_Manager
 *
 * @wordpress-plugin
 * Plugin Name:       Ads Manager
 * Plugin URI:        https://genosha.com.ar
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.3.8
 * Author:            Juan Eduardo Iriart
 * Author URI:        https://genosha.com.ar
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       tar-manager
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 */
define( 'TAR_GOOGLE_ADS_MANAGER_VERSION', '1.3.8' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-tar-google-ads-manager-activator.php
 */
function activate_tar_google_ads_manager() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-tar-google-ads-manager-activator.php';
	Tar_Google_Ads_Manager_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-tar-google-ads-manager-deactivator.php
 */
function deactivate_tar_google_ads_manager() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-tar-google-ads-manager-deactivator.php';
	Tar_Google_Ads_Manager_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_tar_google_ads_manager' );
register_deactivation_hook( __FILE__, 'deactivate_tar_google_ads_manager' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-tar-google-ads-manager.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_tar_google_ads_manager() {

	$plugin = new Tar_Google_Ads_Manager();
	$plugin->run();

}
run_tar_google_ads_manager();

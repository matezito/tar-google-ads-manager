<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://genosha.com.ar
 * @since      1.0.0
 *
 * @package    Tar_Google_Ads_Manager
 * @subpackage Tar_Google_Ads_Manager/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Tar_Google_Ads_Manager
 * @subpackage Tar_Google_Ads_Manager/includes
 * @author     Juan Eduardo Iriart <juan.e@genosha.com.ar>
 */
class Tar_Google_Ads_Manager_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'tar-google-ads-manager',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}

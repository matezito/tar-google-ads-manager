<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://genosha.com.ar
 * @since      1.0.0
 *
 * @package    Tar_Google_Ads_Manager
 * @subpackage Tar_Google_Ads_Manager/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Tar_Google_Ads_Manager
 * @subpackage Tar_Google_Ads_Manager/includes
 * @author     Juan Eduardo Iriart <juan.e@genosha.com.ar>
 */
class Tar_Google_Ads_Manager_Deactivator
{

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate()
	{
		self::delete_tables();
	}

	public static function delete_tables()
	{
		global $wpdb;
		$table_name = $wpdb->prefix . 'tar_ads_manager_ads' . ',' . $wpdb->prefix . 'tar_ads_manager_ads_sizes';
		$sql = 'DROP TABLE IF EXISTS ' . $table_name;
		$wpdb->query($sql);
	}
}

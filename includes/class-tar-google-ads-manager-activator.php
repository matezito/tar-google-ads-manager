<?php

/**
 * Fired during plugin activation
 *
 * @link       https://genosha.com.ar
 * @since      1.0.0
 *
 * @package    Tar_Google_Ads_Manager
 * @subpackage Tar_Google_Ads_Manager/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Tar_Google_Ads_Manager
 * @subpackage Tar_Google_Ads_Manager/includes
 * @author     Juan Eduardo Iriart <juan.e@genosha.com.ar>
 */
class Tar_Google_Ads_Manager_Activator
{

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate()
	{
		self::create_table_ads();
		self::create_table_ads_size();
	}
	/** 
	 * Create Tables
	 */
	public static function create_tables($table, $sql)
	{
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		$table = $wpdb->prefix . $table;

		$sql = 'CREATE TABLE IF NOT EXISTS ' . $table . $sql . $charset_collate;

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}
	/**
	 * Ads table
	 */
	public static function create_table_ads()
	{
		$ads_table = 'tar_ads_manager_ads';

		$sql =  ' ( `ID` INT NOT NULL AUTO_INCREMENT , `uniqid` VARCHAR(100) NOT NULL , `name` VARCHAR(150) NOT NULL , `group` VARCHAR(150) NULL , `code` VARCHAR(150) NOT NULL , `fluid` INT(1) NULL , `active` INT(1) NOT NULL DEFAULT 1 , `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , `updated_at` TIMESTAMP NOT NULL, `user_id` INT NOT NULL , PRIMARY KEY (`ID`))';

		self::create_tables($ads_table, $sql);
	}
	/**
	 * Ads size table
	 */
	public static function create_table_ads_size()
	{
		$ads_sizes_table = 'tar_ads_manager_ads_sizes';

		$sql = '( `id` INT NOT NULL AUTO_INCREMENT , `ad_id` INT NOT NULL , `ad_width` INT NOT NULL , `ad_height` INT NOT NULL , PRIMARY KEY (`id`))';

		self::create_tables($ads_sizes_table, $sql);
	}
}

<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://genosha.com.ar
 * @since      1.0.0
 *
 * @package    Tar_Google_Ads_Manager
 * @subpackage Tar_Google_Ads_Manager/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Tar_Google_Ads_Manager
 * @subpackage Tar_Google_Ads_Manager/admin
 * @author     Juan Eduardo Iriart <juan.e@genosha.com.ar>
 */
class Tar_Google_Ads_Manager_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		
		$this->require_class_list();

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name.'-css', plugin_dir_url( __FILE__ ) . 'css/manager-admin.css', array(), '', 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name.'-js', plugin_dir_url( __FILE__ ) . 'js/manager-admin.js', array( 'jquery' ), '', false );
		

	}

	public function require_class_list()
	{
		/**
		 * Database utils
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-ads-list-manager.php';
		ADDB();
		/** 
		 * menu
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-ads-admin-menu.php';
	}

}

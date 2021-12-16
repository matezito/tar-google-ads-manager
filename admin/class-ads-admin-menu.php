<?php


class AD_Admin_Menu {


    public function __construct()
    {
       add_action( 'admin_menu', [$this,'admin_menu_ads'] );
    }

    public function admin_menu_ads()
    {
        add_menu_page(
            __( 'Tar Ads Manager', 'tar-manager' ),
            __( 'ADS Manager', 'tar-manager' ),
            'manage_options',
            'tar_manager',
            [$this,'show_ads'],
           'dashicons-money-alt',
            6
        );
    }

    public function show_ads()
    {
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/tar-google-ads-manager-admin-display.php';
    }
}

$admin_menu = new AD_Admin_Menu();
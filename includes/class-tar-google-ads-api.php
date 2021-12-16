<?php

class AD_Api{

    private $ad_table = 'tar_ads_manager_ads';
    private $ad_table_size = 'tar_ads_manager_ads_sizes';

    public function __construct()
    {
        add_action('rest_api_init',[$this,'register_router_ads']); //wp-json/manager/v1/items
        add_action('rest_api_init',[$this,'register_router_ads_single']); //wp-json/manager/v1/item/{id}
        add_action('rest_api_init',[$this,'register_router_ads_sizes']); //wp-json/manager/v1/item-sizes/{id}
    }

    public function get_all_ads()
    {
        $sql = ADDB()->get_data_by_field($this->ad_table,'*','ORDER BY ID DESC');

        if(!$sql || empty($sql)){
            return (object)[];
        }

        return $sql;
    }

    public function get_all_ads_active()
    {
        $sql = ADDB()->get_data_by_field($this->ad_table,'*','WHERE active = 1 ORDER BY ID DESC');

        if(!$sql || empty($sql)){
            return (object)[];
        }

        return $sql;
    }

    public function register_router_ads()
    {
        register_rest_route( 'manager/v1', '/items/', array(
            'methods' => 'GET',
            'callback' => [$this,'get_all_ads_active'],
            'permission_callback' => ''
          ) );
    }

    public function get_ad($data)
    {
        $sql = ADDB()->get_data($data['id'],'ID',$this->ad_table);

        if(!$sql || empty($sql)){
            return (object)[];
        }

        return $sql;
    }

    public function register_router_ads_single()
    {
        register_rest_route( 'manager/v1', '/item/(?P<id>\d+)', array(
            'methods' => 'GET',
            'callback' => [$this,'get_ad'],
            'permission_callback' => ''
          ) );
    }

    public function get_ad_sizes($data)
    {
        $sql = ADDB()->get_data($data['id'],'ad_id',$this->ad_table_size);

        if(!$sql || empty($sql)){
            return (object)[];
        }

        return $sql;
    }

    public function register_router_ads_sizes()
    {
        register_rest_route( 'manager/v1', '/item-sizes/(?P<id>\d+)', array(
            'methods' => 'GET',
            'callback' => [$this,'get_ad_sizes'],
            'permission_callback' => ''
          ) );
    }

}

$api = new AD_Api();
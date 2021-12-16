<?php

class AD_List_Manger
{

    private $ad_table = 'tar_ads_manager_ads';
    private $ad_table_size = 'tar_ads_manager_ads_sizes';

    public $messages = [];
    public $per_page = 12;

    private $nonce = 'manager-nonce';
    private $action = 'manager-ajax-action';
    private $url;

    public function __construct()
    {
        $this->url = admin_url('admin-ajax.php');

        add_action('current_screen', [$this, 'code_danger']);

        add_action('admin_enqueue_scripts', [$this, 'manager_ajax_js']);

        add_action('manager_messages_header', [$this, 'detect_ad_block']);
        add_action('manager_messages_header', [$this, 'show_flash_session']);

        add_action('wp_ajax_nopriv_' . $this->action, [$this, 'delete_size_ajax']);
        add_action('wp_ajax_' . $this->action, [$this, 'delete_size_ajax']);

        $this->add_new_ad();
        $this->import_csv();
        $this->edit_ad();
        $this->delete_item();
        $this->options();
    }
    /**
     * Simple detetec adblock
     */
    public function detect_ad_block()
    {
        echo '<script>
            (function($){
                $(document).ready(function(){
                    if(!$(".add_row").is(":visible")){
                        $("#manager-body").prepend("<div class=\"block-message\">Remember to disable the ad block or you will have problems loading the ads.</div>");
                    }
                });
            })(jQuery);
        </script>';
    }
    /**
     * ajax
     */
    public function manager_ajax_js()
    {
        wp_enqueue_script('manager_ajax_js', plugin_dir_url(__FILE__) . 'js/manager-ajax.js', array('jquery'), '1.0', true);
        $this->delete_size_ajax_vars();
    }

    /**
     * manager localize scripts
     */
    public function manager_localize_script($var_data, $data)
    {
        $fields = [
            'url'    => $this->url,
            '_ajax_nonce'  => wp_create_nonce($this->nonce),
            'action' => $this->action,
            'sending' => __('Checking...', 'tar-manager')
        ];

        $fields = array_merge($fields, $data);

        wp_localize_script('manager_ajax_js', $var_data, $fields);
    }
    /***
     * Set Flash messages
     */
    public function set_flash_session($class, $msg)
    {
        /**
         * Init sessions if not
         */
        if (!session_id()) {
            session_start();
        }
        /**
         * Create session if not exist
         */
        if (!isset($_SESSION['flash_messages'])) {
            $_SESSION['flash_messages'] = ["hola"];
        }

        $_SESSION['flash_messages'] = [
            'name' => $class,
            'msg' => $msg
        ];

        return $_SESSION['flash_messages'];
    }
    /**
     * Show Flash Messages
     */
    public function show_flash_session()
    {
        if (isset($_SESSION['flash_messages']) && !empty($_SESSION['flash_messages'])) {

            echo '<div class="notice notice-' . $_SESSION['flash_messages']['name'] . ' is-dismissible">
                    <p>' . $_SESSION['flash_messages']['msg'] . '</p>
                </div>';
        }
        unset($_SESSION['flash_messages']);
    }

    private function insert_data($table, $data = [], $replace = [])
    {
        global $wpdb;
        $table_name = $wpdb->prefix . $table;
        $wpdb->insert($table_name, $data, $replace);
        return $this->last_id();
    }

    private function last_id()
    {
        global $wpdb;
        return $wpdb->insert_id;
    }

    private function update_data($table, $data, $where, $data_format = [], $where_format = [])
    {
        global $wpdb;
        $table_name = $wpdb->prefix . $table;
        $result = $wpdb->update($table_name, $data, $where, $data_format, $where_format);
        return $result;
    }

    private function delete_data($table, $where, $where_format)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . $table;
        $result = $wpdb->delete($table_name, $where, $where_format);
        return $result;
    }

    private function debug_db()
    {
        global $wpdb;
        if ($wpdb->last_error !== '') :
            echo $wpdb->last_query;
        endif;
    }

    public function get_all_data($table, $order_by, $limit, $offset = '')
    {
        global $wpdb;
        $table_name = $wpdb->prefix . $table;
        $results = $wpdb->get_results("SELECT * FROM " . $table_name . " " . $order_by . " " . $limit . " OFFSET " . $offset, OBJECT);
        return $results;
    }

    public function get_data_by_field($table, $fields, $order_by)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . $table;
        $results = $wpdb->get_results("SELECT " . $fields . " FROM " . $table_name . " " . $order_by, OBJECT);
        return $results;
    }

    public function count_data($table, $by)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . $table;
        $results = $wpdb->get_var("SELECT count(" . $by . ") FROM " . $table_name);
        return $results;
    }


    public function get_data($ad_id, $where, $table)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . $table;
        $results = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM " . $table_name . " WHERE " . $where . "=%d", $ad_id)
        );
        return $results;
    }
    public function get_data_row($ad_id, $where, $table)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . $table;
        $results = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM " . $table_name . " WHERE " . $where . "=%d", $ad_id)
        );
        return $results;
    }
    public function get_data_row_multiple($where,$table)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . $table;
        $results = $wpdb->get_row("SELECT * FROM " . $table_name . " WHERE " . $where);
        return $results;
    }
    /**
     * Actions: ADD
     */
    public function add_new_ad()
    {
        if (isset($_POST['add_new_ad'])) {
            $name = sanitize_text_field($_POST['ad_name']);
            $code = sanitize_text_field($_POST['ad_code']);
            $group = sanitize_text_field($_POST['ad_group']);
            $fluid = isset($_POST['ad_fluid']) ? 1 : 0;
            $active = isset($_POST['ad_active']) ? 1 : 0;

            $timestamp = date('Y-m-d H:m:i');

            $user_id = intval($_POST['ad_user_id']);

            $data = [
                'uniqid' => uniqid(),
                'name' => $name,
                'group' => $group,
                'code' => $code,
                'fluid' => $fluid,
                'active' => $active,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
                'user_id' => $user_id
            ];

            $new = $this->insert_data($this->ad_table, $data, ['%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d']);
            if ($new) {

                $width = $_POST['ad_width'];
                $height = $_POST['ad_height'];

                for ($i = 0; $i < count($width); $i++) {
                    $sizes = [
                        'ad_id' => $new,
                        'ad_width' => $width[$i],
                        'ad_height' => $height[$i]
                    ];

                    $this->insert_data($this->ad_table_size, $sizes, ['%d', '%d', '%d']);
                }

                $this->set_flash_session('success', __('Ad saved successfully', 'tar-manager'));
                header('Location: ' . admin_url('/admin.php?page=tar_manager'));
                exit();
            } else {
                $this->set_flash_session('error', __('ERROR'));
                header('Location: ' . admin_url('/admin.php?page=tar_manager'));
                exit();
            }
        }
    }
    /**
     * Import
     */
    public function empty_data($field)
    {
        for ($i = 0; $i <= 7; $i++) {
            // Make sure that the key exists, isn't null or an empty string
            if (!isset($data[$i]) || $data[$i] === '') {
                return true;
            }
        }

        return false;
    }
    public function import_csv()
    {
        if (isset($_FILES['import-file'])) {

            if (!function_exists('wp_get_current_user')) {
                include(ABSPATH . "wp-includes/pluggable.php");
            }

            if (!function_exists('wp_handle_upload')) {
                require_once(ABSPATH . 'wp-admin/includes/file.php');
            }

            $timestamp = date('Y-m-d H:m:i');

            $uploadedfile = $_FILES['import-file'];

            $upload_overrides = array(
                'test_form' => false
            );

            $movefile = wp_handle_upload($uploadedfile, $upload_overrides);

            if ($movefile && !isset($movefile['error'])) {

                $file = fopen($movefile['file'], "r");
                fgetcsv($file);

                while (($column = fgetcsv($file, 10000, ";")) !== FALSE) {
                    $name = sanitize_text_field($column[0]);
                    $group = sanitize_text_field($column[1]);
                    $code = sanitize_text_field($column[2]);
                    $fluid = $column[3];
                    $active = $column[4];
                    $user_id = wp_get_current_user()->ID;

                    $empty_filesop = array_filter(array_map('trim', $column));
                    if (!empty($empty_filesop)) {


                        $data = [
                            'uniqid' => uniqid(),
                            'name' => $name,
                            'group' => $group,
                            'code' => $code,
                            'fluid' => $fluid,
                            'active' => $active,
                            'created_at' => $timestamp,
                            'updated_at' => $timestamp,
                            'user_id' => $user_id
                        ];

                        $new = $this->insert_data($this->ad_table, $data, ['%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d']);
                        if ($new) {
                            $sizes = $column[5];
                            $sizes = explode(',', $sizes);

                            foreach ($sizes as $key => $value) {
                                $size = explode('x', $value);

                                $width = $size[0];
                                $height = $size[1];

                                $sizes = [
                                    'ad_id' => $new,
                                    'ad_width' => $width,
                                    'ad_height' => $height
                                ];

                                $this->insert_data($this->ad_table_size, $sizes, ['%d', '%d', '%d']);
                            }
                        }
                    }
                }
            }
            $this->set_flash_session('success', __('All data importa correctly', 'tar-manager'));
            header('Location: ' . admin_url('/admin.php?page=tar_manager'));
            exit();
        }
    }
    /**
     * Get all ads
     */
    public function get_all_ads()
    {

        $per_page = $this->per_page;

        $page = isset($_GET['tarpage']) ? abs((int) $_GET['tarpage']) : 0;

        if ($page > 1) {

            $offset = $page * $per_page - $per_page;
        } else {
            $offset = $page;
        }

        return $this->get_all_data($this->ad_table, 'ORDER BY ID DESC LIMIT ', $per_page, $offset);
    }
    /**
     * Paginate ads
     */
    public function show_pagination()
    {
        $per_page = $this->per_page;

        $page = isset($_GET['tarpage']) ? abs((int) $_GET['tarpage']) : 0;

        $total = $this->count_data($this->ad_table, 'ID');


        echo paginate_links(array(
            'base' => add_query_arg('tarpage', '%#%'),
            'format' => '',
            'prev_text' => __('&laquo;'),
            'next_text' => __('&raquo;'),
            'total' => ceil($total / $per_page),
            'current' => $page
        ));
    }
    /**
     * total
     */
    public function total_ads()
    {
        $total = $this->count_data($this->ad_table, 'ID');
        return $total;
    }
    /**
     * Get sizes by ad
     */
    public function get_size_by_ad($id)
    {
        return $this->get_data($id, 'ad_id', $this->ad_table_size);
    }
    /**
     * Edit the ad
     */
    public function edit_ad()
    {
        if (isset($_POST['edit_button'])) {
            $name = sanitize_text_field($_POST['edit_name']);
            $code = $_POST['edit_code'];
            $group = sanitize_text_field($_POST['edit_group']);
            $fluid = isset($_POST['edit_fluid']) ? 1 : 0;
            $active = isset($_POST['edit_active']) ? 1 : 0;
            $ad_id = intval($_POST['ad_id']);

            $timestamp = date('Y-m-d H:m:i');

            $user_id = intval($_POST['ad_user_id']);

            $data = [
                'name' => $name,
                'group' => $group,
                'code' => $code,
                'fluid' => $fluid,
                'active' => $active,
                'updated_at' => $timestamp,
                'user_id' => $user_id
            ];
            $where = ['ID' => $ad_id];

            $update = $this->update_data($this->ad_table, $data, $where, null, null);

            if ($update) {
                /**
                 * Inser new sizes
                 */
                if (isset($_POST['new_edit_width'])) {
                    $new_width = $_POST['new_edit_width'];
                    $new_height = $_POST['new_edit_height'];

                    for ($i = 0; $i < count($new_width); $i++) {
                        $sizes = [
                            'ad_id' => $ad_id,
                            'ad_width' => $new_width[$i],
                            'ad_height' => $new_height[$i]
                        ];

                        $this->insert_data($this->ad_table_size, $sizes, ['%d', '%d', '%d']);
                    }
                }
                $this->set_flash_session('success', __('Ad updated successfully', 'tar-manager'));
                header('Location: ' . admin_url('/admin.php?page=tar_manager'));
                exit();
            } else {
                $this->set_flash_session('error', __('ERROR: ad not updated'));
                header('Location: ' . admin_url('/admin.php?page=tar_manager'));
                exit();
            }
        }
    }
    /**
     * Delete size by ajax
     */
    public function delete_size_ajax_vars()
    {
        $delete_size = isset($_POST['delete_size']) ? $_POST['delete_size'] : '';
        $id_size = isset($_POST['id_size']) ? $_POST['id_size'] : '';

        $fields = [
            'delete_size' => $delete_size,
            'id_size' => $id_size,
            'success' => __('Size delete', 'tar-manager')
        ];
        return $this->manager_localize_script('ajax_delete_size', $fields);
    }

    public function delete_size_ajax()
    {
        if (isset($_POST['delete_size'])) {

            $nonce = sanitize_text_field($_POST['_ajax_nonce']);

            if (!wp_verify_nonce($nonce, $this->nonce)) {
                die(__('So sorry, wp_nonce is broken!', 'suscriptions'));
            }

            if (isset($_SESSION['flash_messages'])) {
                unset($_SESSION['flash_messages']);
            }

            $where = ['id' => $_POST['id_size']];
            $delete = $this->delete_data($this->ad_table_size, $where, null);
            if ($delete) {
                return true;
            } else {
                return;
            }
        }
    }

    public function delete_item()
    {
        if (isset($_POST['delete-button'])) {
            $ad_id = intval($_POST['ad_id']);

            $where = ['ID' => $ad_id];

            $delete = $this->delete_data($this->ad_table, $where, null);

            if ($delete) {
                $sizes = $this->get_size_by_ad($ad_id);
                foreach ($sizes as $s) {
                    $this->delete_data($this->ad_table_size, ['ad_id' => $ad_id], null);
                }
                $this->set_flash_session('success', __('Ad and sizes deleted successfully', 'tar-manager'));
                header('Location: ' . admin_url('/admin.php?page=tar_manager'));
                exit();
            } else {
                $this->set_flash_session('error', __('ERROR: ad not deleted'));
                header('Location: ' . admin_url('/admin.php?page=tar_manager'));
                exit();
            }
        }
    }

    public function code_danger()
    {
        $code = get_option('gadmnc');
        $screen = get_current_screen();
        if (!$code) {
            if ('toplevel_page_tar_manager' === $screen->base) {
                $this->set_flash_session('error', sprintf(__('NETWORK CODE MISSING!!, more info in %s', 'tar-manager'), '<a href="https://support.google.com/admanager/answer/7674889?hl=en" target="_blank">this page.</a>'));
            }
        }
    }

    public function options()
    {
        if (isset($_POST['add_nc'])) {
            if (update_option('gadmnc', sanitize_text_field($_POST['gadmnc']))) {
                $this->set_flash_session('success', __('Network Code updeted successfully', 'tar-manager'));
                header('Location: ' . admin_url('/admin.php?page=tar_manager'));
                exit();
            } else {
                $this->set_flash_session('error', __('Network Code is not updeted, sorry', 'tar-manager'));
                header('Location: ' . admin_url('/admin.php?page=tar_manager'));
                exit();
            }
        }
    }

    /**
     * Post types
     */
    public function post_types()
    {
        $args = array(
            'public'   => true,
            '_builtin' => true
        );

        $output = 'objects';
        $operator = 'and';


        $post_types = get_post_types($args, $output, $operator);
        return $post_types;
    }
}

function ADDB()
{
    return new AD_List_Manger();
}

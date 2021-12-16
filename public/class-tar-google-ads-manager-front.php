<?php


class AD_Manger_Front
{

    private $ad_table = 'tar_ads_manager_ads';
    private $ad_table_size = 'tar_ads_manager_ads_sizes';

    public function __construct()
    {

        add_filter('script_loader_tag', [$this, 'add_id_to_script'], 10, 3);
        add_action('wp_enqueue_scripts', [$this, 'script_add']);

        add_action('wp_head', [$this, 'head_script']);
    }

    public function add_id_to_script($tag, $handle, $src)
    {
        if ($handle === 'tar-ga-manager-js') {
            $tag = '<script async src="' . esc_url($src) . '"></script>';
        }

        return $tag;
    }

    public function script_add()
    {
        wp_enqueue_script('tar-ga-manager-js', 'https://securepubads.g.doubleclick.net/tag/js/gpt.js', false, false, false);
    }

    public function head_script()
    {
        $ad = ADDB()->get_data_by_field($this->ad_table, '*', 'ORDER BY ID DESC', null, null);
        $nc = get_option('gadmnc');
       
        echo "<script>
        window.googletag = window.googletag || {cmd: []};
        googletag.cmd.push(function() {";
        foreach ($ad as $item) {
            if ($item->active == '1') {
                $sizes = ADDB()->get_data($item->ID, 'ad_id', $this->ad_table_size);

                $code = $item->group !== '' ? $item->group.'/'.$item->code : $item->code;
                
                if ($item->fluid === '1' && count($sizes) === 1) {
                    $position = '';
                    foreach ($sizes as $size) {
                        $position .= "['fluid', ['" . $size->ad_width . ',' . $size->ad_height . "']]";
                    }
                    echo "googletag.defineSlot('/" . $nc . "/" . $code . "', " . $position . ", 'div-gpt-ad-" . $item->uniqid . "-0').addService(googletag.pubads());\n";
                }
                if ($item->fluid === '1' && count($sizes) >= 2) {
                    $output = '';
                    foreach ($sizes as $size) {
                        $output .= '[' . $size->ad_width . ',' . $size->ad_height . '], ';
                    }
                    $output = substr($output, 0, -2);
                    $position = "[$output, 'fluid']";
                    echo "googletag.defineSlot('/" . $nc . "/" . $code . "', " . $position . ", 'div-gpt-ad-" . $item->uniqid . "-0').addService(googletag.pubads());\n";
                }
                if ($item->fluid === '0' && count($sizes) === 1) {
                    $position = '';
                    foreach ($sizes as $size) {
                        $position .= '[' . $size->ad_width . ',' . $size->ad_height . ']';
                    }
                    echo "googletag.defineSlot('/" . $nc . "/" . $code . "', " . $position . ", 'div-gpt-ad-" . $item->uniqid . "-0').addService(googletag.pubads());\n";
                }
                if ($item->fluid === '0' && count($sizes) >= 2) {
                    $output = '';
                    foreach ($sizes as $size) {
                        $output .= '[' . $size->ad_width . ',' . $size->ad_height . '], ';
                    }
                    $output = substr($output, 0, -2);
                    $position = '[' . $output . ']';
                    echo "googletag.defineSlot('/" . $nc . "/" . $code . "', " . $position . ", 'div-gpt-ad-" . $item->uniqid . "-0').addService(googletag.pubads());\n";
                }
            }
        }
        echo "googletag.pubads().enableSingleRequest();
          googletag.enableServices();
        });
      </script>";
    }


    public function show_ad($ID)
    {
        $ad = ADDB()->get_data_row($ID,'ID',$this->ad_table);
        $sizes = ADDB()->get_data($ID, 'ad_id', $this->ad_table_size);

        $code = isset($ad->group) !== null ? $ad->group.'/'.$ad->code : $ad->code;

        $nc = get_option('gadmnc');

        if($ad !== null) {
            if(count($sizes) === 1){
                foreach($sizes as $s){
                    $style = "style='width: ".$s->ad_width."px; height: ".$s->ad_height."px;'";
                }
            } else {
                $style = "";
            }
    
            echo "<!-- /".$nc."/".$code." -->
            <div id='div-gpt-ad-".$ad->uniqid."-0' ".$style.">
              <script>
                googletag.cmd.push(function() { googletag.display('div-gpt-ad-".$ad->uniqid."-0'); });
              </script>
            </div>";
        } else {
            return;
        }        
    }

    public function show_ad_by_code($code,$group)
    {
        
        global $wpdb;
        $ad = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$wpdb->prefix.'tar_ads_manager_ads WHERE `code` = %s AND `group` = %s',[$code,$group]));
      
        $sizes = ADDB()->get_data($ad->ID, 'ad_id', $this->ad_table_size);

        $code = isset($ad->group) !== null ? $ad->group.'/'.$ad->code : $ad->code;

        $nc = get_option('gadmnc');

        if($ad) {
            echo 'hola';
            if(count($sizes) >= 1){
             
                foreach($sizes as $s){
                    $style = "style='width: ".$s->ad_width."px; height: ".$s->ad_height."px;'";
                }
            } else {
     
                $style = "";
            }
    
            echo "<!-- /".$nc."/".$code." -->
            <div id='div-gpt-ad-".$ad->uniqid."-0' ".$style.">
              <script>
                googletag.cmd.push(function() { googletag.display('div-gpt-ad-".$ad->uniqid."-0'); });
              </script>
            </div>";
        } else {
            return '';
        }        
    }
}

function ADF()
{
    return new AD_Manger_Front();
}

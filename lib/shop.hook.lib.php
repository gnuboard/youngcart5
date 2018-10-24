<?php
if (!defined('_GNUBOARD_')) exit;

function get_pretty_shop_url($url, $folder, $no='', $query_string='', $action=''){
    global $g5, $config;

    if( $folder !== 'shop' ){
        return $url;
    }

    $segments = array();
    $url = $add_query = '';

    if( $config['cf_bbs_rewrite'] ){
        $segments[0] = G5_URL;
        $segments[1] = urlencode($folder);

        if( $config['cf_bbs_rewrite'] > 1 ){
            $item = get_shop_item($no, true);
            $segments[2] = $item['it_seo_title'] ? urlencode($item['it_seo_title']).'/' : urlencode($no);
        } else {
            $segments[2] = urlencode($no);
        }

        if($query_string) {
            // If the first character of the query string is '&', replace it with '?'.
            if(substr($query_string, 0, 1) == '&') {
                $add_query = preg_replace("/\&amp;/", "?", $query_string, 1);
            } else {
                $add_query = '?'. $query_string;
            }
        }
    } else {
        $url = G5_SHOP_URL. '/item.php';
        if($no) {
            $url .= '?'. urlencode($no);
        }
        if($query_string) {
            $url .= ($no ? '?' : '&amp;'). $query_string;
        }

        $segments[0] = $url;
    }

    return implode('/', $segments).$add_query;
}

function add_shop_nginx_conf_rules($get_path_url, $base_path, $return_string=false){

    $add_rules = array();

    return implode("\n", $add_rules);

}

function add_shop_mod_rewrite_rules($get_path_url, $base_path, $return_string=false){

    $add_rules = array();
    
    $add_rules[] = 'RewriteRule ^shop/([0-9a-zA-Z_]+)$  '.G5_SHOP_DIR.'/item.php?it_id=$1&rewrite=1  [L]';

    return implode("\n", $add_rules);

}
?>
<?php
if (!defined('_GNUBOARD_')) exit;

function shop_type_url($type){
    global $config;

    if( $config['cf_bbs_rewrite'] ){
        return get_pretty_url('shop', 'type-'.$type);
    }

    return G5_SHOP_URL.'/listtype.php?type='.urlencode($type);
}

function shop_item_url($it_id){
    global $config;

    if( $config['cf_bbs_rewrite'] ){
        return get_pretty_url('shop', $it_id);
    }

    return G5_SHOP_URL.'/item.php?it_id='.urlencode($it_id);
}

function shop_category_url($ca_id){
    global $config;

    if( $config['cf_bbs_rewrite'] ){
        return get_pretty_url('shop', 'list-'.$ca_id);
    }

    return G5_SHOP_URL.'/list.php?ca_id='.urlencode($ca_id);
}

function add_pretty_shop_url($url, $folder, $no='', $query_string='', $action=''){
    global $g5, $config;

    if( $folder !== 'shop' ){
        return $url;
    }

    $segments = array();
    $url = $add_query = '';
    
    if( $config['cf_bbs_rewrite'] ){
        $segments[0] = G5_URL;
        $segments[1] = urlencode($folder);

        if( $config['cf_bbs_rewrite'] > 1 && ! preg_match('/^(list|type)\-([^\/]+)/i', $no) ){
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
        
        if( preg_match('/^list\-([^\/]+)/i', $no) ){
            $url = G5_SHOP_URL. '/list.php?ca_id='.urlencode($no);
        } else if( preg_match('/^type\-([^\/]+)/i', $no) ){
            $url = G5_SHOP_URL. '/listtype.php?type='.urlencode($no);
        } else {
            $url = G5_SHOP_URL. '/item.php?it_id='.urlencode($no);
        }

        if($query_string) {
            $url .= ($no ? '?' : '&amp;'). $query_string;
        }

        $segments[0] = $url;
    }

    return implode('/', $segments).$add_query;
}

function add_shop_nginx_conf_rules($rules, $get_path_url, $base_path, $return_string=false){

    $add_rules = array();

    $add_rules[] = "rewrite ^{$base_path}shop/list-([0-9a-z]+)$ {$base_path}".G5_SHOP_DIR."/list.php?ca_id=$1&rewrite=1 break;";
    $add_rules[] = "rewrite ^{$base_path}shop/type-([0-9a-z]+)$ {$base_path}".G5_SHOP_DIR."/listtype.php?type=$1&rewrite=1 break;";
    $add_rules[] = "rewrite ^{$base_path}shop/([0-9a-zA-Z_]+)$ {$base_path}".G5_SHOP_DIR."/item.php?it_id=$1&rewrite=1 break;";
    $add_rules[] = "rewrite ^{$base_path}shop/([^/]+)/$ {$base_path}".G5_SHOP_DIR."/item.php?it_seo_title=$1&rewrite=1 break;";

    return implode("\n", $add_rules).$rules;

}

function add_shop_mod_rewrite_rules($rules, $get_path_url, $base_path, $return_string=false){

    $add_rules = array();
    
    $add_rules[] = 'RewriteRule ^shop/list-([0-9a-z]+)$  '.G5_SHOP_DIR.'/list.php?ca_id=$1&rewrite=1  [QSA,L]';
    $add_rules[] = 'RewriteRule ^shop/type-([0-9a-z]+)$  '.G5_SHOP_DIR.'/listtype.php?type=$1&rewrite=1  [QSA,L]';
    $add_rules[] = 'RewriteRule ^shop/([0-9a-zA-Z_]+)$  '.G5_SHOP_DIR.'/item.php?it_id=$1&rewrite=1  [QSA,L]';
    $add_rules[] = 'RewriteRule ^shop/([^/]+)/$  '.G5_SHOP_DIR.'/item.php?it_seo_title=$1&rewrite=1  [QSA,L]';

    return implode("\n", $add_rules).$rules;

}

function add_shop_admin_dbupgrade($is_check){
    global $g5;

    // 내용 관리 짧은 주소
    $sql = " SHOW COLUMNS FROM `{$g5['g5_shop_item_table']}` LIKE 'it_seo_title' ";
    $row = sql_fetch($sql);

    if( !$row ){
        sql_query("ALTER TABLE `{$g5['g5_shop_item_table']}`
                    ADD `it_seo_title` varchar(200) NOT NULL DEFAULT '' AFTER `it_name`,
                    ADD INDEX `it_seo_title` (`it_seo_title`);
        ", false);

        $is_check = true;
    }

    return $is_check;

}

function shop_exist_check_seo_title($seo_title, $type, $shop_item_table, $it_id){
    
    $sql = "select it_seo_title FROM {$shop_item_table} WHERE it_seo_title = '".sql_real_escape_string($seo_title)."' AND it_id <> '$it_id' limit 1";
    $row = sql_fetch($sql, false);

    if( $row['it_seo_title'] ){
        return 'is_exists';
    }

    return '';
}

function shop_seo_title_update($it_id){
    global $g5;

    $item = get_shop_item($it_id, true);

    if( ! $item['it_seo_title'] && $item['it_name'] ){
        $it_seo_title = exist_seo_title_recursive('shop', generate_seo_title($item['it_name']), $g5['g5_shop_item_table'], $item['it_id']);

        if( $it_seo_title ){
            $sql = " update `{$g5['g5_shop_item_table']}` set it_seo_title = '{$it_seo_title}' where it_id = '{$item['it_id']}' ";
            sql_query($sql);
        }
    }
}
?>
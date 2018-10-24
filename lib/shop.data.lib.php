<?php
if (!defined('_GNUBOARD_')) exit;

function get_shop_item($it_id, $is_cache=false, $add_query=''){
    
    global $g5, $g5_object;

    $add_query_key = $add_query ? 'shop_'.md5($add_query) : '';

    $item = $is_cache ? $g5_object->get('shop', $it_id, $add_query_key) : null;

    if( !$item ){
        $sql = " select * from {$g5['g5_shop_item_table']} where it_id = '{$it_id}' $add_query ";
        $item = sql_fetch($sql);

        $g5_object->set('shop', $it_id, $item, $add_query_key);
    }

    return $item;
}

?>
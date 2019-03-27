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

function get_shop_navigation_data($is_cache, $ca_id, $ca_id2='', $ca_id3=''){
    
    $all_categories = get_shop_category_array($is_cache);

    $datas = array();
    
    if( strlen($ca_id) >= 2 && $all_categories ){
        foreach((array) $all_categories as $category1 ){
            $datas[0][] = $category1['text'];
        }
    }

    $select_ca_id = $ca_id2 ? $ca_id2 : $ca_id;
    $item_categories2 = $select_ca_id ? get_shop_category_by($is_cache, 'ca_id', $select_ca_id) : array();

    if( strlen($select_ca_id) >= 4 && $item_categories2 ){
        foreach((array) $item_categories2 as $key=>$category2 ){
            if( $key === 'text' ) continue;

            $datas[1][] = $category2['text'];
        }
    }

    $select_ca_id = $ca_id3 ? $ca_id3 : $ca_id;
    $item_categories3 = $select_ca_id ? get_shop_category_by($is_cache, 'ca_id', $select_ca_id) : array();

    if( strlen($select_ca_id) >= 6 && $item_categories3 && isset($item_categories3[substr($select_ca_id,0,4)]) ){
        $sub_categories = $item_categories3[substr($select_ca_id,0,4)];

        foreach((array) $sub_categories as $key=>$category3 ){
            if( $key === 'text' ) continue;

            $datas[2][] = $category3['text'];
        }
    }

    return $datas;
}

function get_shop_category_by($is_cache, $case, $value){
    
    if( $case === 'ca_id' ){
        $categories = get_shop_category_array($is_cache);

        $key = substr(preg_replace('/[^0-9a-z]/i', '', $value), 0, 2);
        
        if( isset($categories[$key]) ){
            return $categories[$key];
        }
    }

    return array();
}

function get_shop_category_array($is_cache=false){

    static $categories = array();
    
    $categories = apply_replace('get_shop_category_array', $categories, $is_cache);

    if( $is_cache && !empty($categories) ){
        return $categories;
    }

    $result = sql_query(get_shop_category_sql('', 2));

    for($i=0; $row=sql_fetch_array($result); $i++) {
        $categories[$row['ca_id']]['text'] = $row;
        
        if( $row['ca_id'] ){
            $result2 = sql_query(get_shop_category_sql($row['ca_id'], 4));

            for($j=0; $row2=sql_fetch_array($result2); $j++) {
                $categories[$row['ca_id']][$row2['ca_id']]['text'] = $row2;
                
                if( $row2['ca_id'] ){
                    $result3 = sql_query(get_shop_category_sql($row2['ca_id'], 6));
                    for($k=0; $row3=sql_fetch_array($result3); $k++) {
                        $categories[$row['ca_id']][$row2['ca_id']][$row3['ca_id']]['text'] = $row3;
                    }
                }   //end if
            }   //end for
        }   //end if
    }   //end for
    
    return $categories;
}

function get_shop_category_sql($ca_id, $len){
    global $g5;

    $sql = " select * from {$g5['g5_shop_category_table']}
                where ca_use = '1' ";
    if($ca_id)
        $sql .= " and ca_id like '$ca_id%' ";
    $sql .= " and length(ca_id) = '$len' order by ca_order, ca_id ";

    return $sql;
}

?>
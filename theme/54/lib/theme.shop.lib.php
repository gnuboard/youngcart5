<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

function get_wish_count($it_id)
{
    global $g5;

    $sql = " select count(*) as cnt
                from {$g5['g5_shop_wish_table']}
                where it_id = '$it_id' ";
    $row = sql_fetch($sql);

    return $row['cnt'];
}

function get_use_count($it_id)
{
    global $g5;

    $sql = " select count(*) as cnt
                from {$g5['g5_shop_item_use_table']}
                where it_id = '$it_id' ";
    $row = sql_fetch($sql);

    return $row['cnt'];
}

?>
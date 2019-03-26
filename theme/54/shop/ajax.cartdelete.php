<?php
include_once('./_common.php');

// 보관기간이 지난 상품 삭제
cart_item_clean();

$s_cart_id = get_session('ss_cart_id');

// 장바구니 상품삭제
$sql = " delete from {$g5['g5_shop_cart_table']}
            where od_id = '".$s_cart_id."'
              and it_id = '{$_POST['it_id']}' ";
sql_query($sql);

die(json_encode(array('error' => '')));
?>
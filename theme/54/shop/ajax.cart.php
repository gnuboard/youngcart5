<?php
include_once('./_common.php');

// 보관기간이 지난 상품 삭제
cart_item_clean();

$s_cart_id = get_session('ss_cart_id');

// 선택필드 초기화
$sql = " update {$g5['g5_shop_cart_table']} set ct_select = '0' where od_id = '$s_cart_id' ";
sql_query($sql);

include_once(G5_SHOP_SKIN_PATH.'/boxcart.skin.php'); // 장바구니
?>
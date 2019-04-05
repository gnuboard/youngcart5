<?php
include_once('./_common.php');

$action = isset($_REQUEST['action']) ? preg_replace('/[^a-z0-9_]/i', '', $_REQUEST['action']) : '';

switch ($action) {
    case 'refresh_cart' :

        // 보관기간이 지난 상품 삭제
        cart_item_clean();

        $s_cart_id = preg_replace('/[^a-z0-9_\-]/i', '', get_session('ss_cart_id'));

        // 선택필드 초기화
        if( $s_cart_id ){
            $sql = " update {$g5['g5_shop_cart_table']} set ct_select = '0' where od_id = '$s_cart_id' ";
            sql_query($sql);
        }

        include_once(G5_SHOP_SKIN_PATH.'/boxcart.skin.php'); // 장바구니
        break;
    case 'refresh_wish' :
        
        if( !$is_member ){
            die('');
        }

        include_once(G5_SHOP_SKIN_PATH.'/boxwish.skin.php'); // 위시리스트
        break;
     default :
}
?>
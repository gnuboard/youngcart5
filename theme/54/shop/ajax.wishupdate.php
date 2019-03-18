<?php
include_once('./_common.php');

if (!$is_member)
    die('회원 전용 서비스 입니다.');

if(!$it_id)
    die('상품 코드가 올바르지 않습니다.');

// 상품정보 체크
$sql = " select it_id from {$g5['g5_shop_item_table']} where it_id = '$it_id' ";
$row = sql_fetch($sql);

if(!$row['it_id'])
    die('상품정보가 존재하지 않습니다.');

$sql = " select wi_id from {$g5['g5_shop_wish_table']}
          where mb_id = '{$member['mb_id']}' and it_id = '$it_id' ";
$row = sql_fetch($sql);

if (!$row['wi_id']) {
    $sql = " insert {$g5['g5_shop_wish_table']}
                set mb_id = '{$member['mb_id']}',
                    it_id = '$it_id',
                    wi_time = '".G5_TIME_YMDHIS."',
                    wi_ip = '$REMOTE_ADDR' ";
    sql_query($sql);

    die('OK');
} else {
    die('위시리스트에 이미 등록된 상품입니다.');
}
?>
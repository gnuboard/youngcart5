<?php
include_once('./_common.php');

$sql = " select * from {$g5['g5_shop_item_table']} where it_id = '$it_id' ";
$it = sql_fetch($sql);

if(!$it['it_id'])
    die(json_encode(array('error' => '상품정보가 존재하지 않습니다.')));

// 상품품절체크
$is_soldout = is_soldout($it['it_id']);

// 주문가능체크
$is_orderable = true;
if(!$it['it_use'] || $it['it_tel_inq'] || $is_soldout)
    die(json_encode(array('error' => '상품을 구매할 수 없습니다.')));
?>
<?php
$item_ct_qty = 1;
if($it['it_buy_min_qty'] > 1)
    $item_ct_qty = $it['it_buy_min_qty'];

$action_url = G5_THEME_SHOP_URL.'/ajax.cartupdate.php';

$is_option   = 0;
$option_item = get_list_options($it['it_id'], $it['it_option_subject'], 0);

ob_start();
?>
<div class="sct_cartop_wr">
    <form name="fcart" method="post" action="<?php echo $action_url; ?>">
    <input type="hidden" name="it_id[]" value="<?php echo $it['it_id']; ?>">
    <input type="hidden" name="it_name[]" value="<?php echo stripslashes($it['it_name']); ?>">
    <input type="hidden" name="it_price[]" value="<?php echo get_price($it); ?>">
    <input type="hidden" name="it_stock[]" value="<?php echo get_it_stock_qty($it['it_id']); ?>">
    <input type="hidden" name="io_type[<?php echo $it['it_id']; ?>][]" value="0">
    <input type="hidden" name="io_id[<?php echo $it['it_id']; ?>][]" value="">
    <input type="hidden" name="io_value[<?php echo $it['it_id']; ?>][]" value="">
    <input type="hidden" name="io_price[<?php echo $it['it_id']; ?>][]" value="">
    <input type="hidden" name="ct_qty[<?php echo $it['it_id']; ?>][]" value="<?php echo $item_ct_qty; ?>">
    <input type="hidden" name="sw_direct" value="0">
        <?php
        if($option_item) {
            $is_option = 1;
        ?>

        <?php // 선택옵션
            echo $option_item;
        ?>

        <button type="button" class="cartopt_cart_btn">장바구니 담기</button>
        <button type="button" class="cartopt_close_btn">닫기</button>

        <?php } ?>
    </form>
</div>

<?php
$content = ob_get_contents();
ob_end_clean();

$result = array(
    'error'  => '',
    'option' => $is_option,
    'html'   => $content
);

die(json_encode($result));
?>
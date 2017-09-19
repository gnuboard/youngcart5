<?php
include_once('./_common.php');
include_once(G5_MSHOP_PATH.'/settle_inicis.inc.php');

if( ! function_exists('get_shop_order_data') ){
    //주문데이터 또는 개인결제 주문데이터 가져오기
    function get_shop_order_data($od_id, $type='item'){
        global $g5;
        
        $od_id = clean_xss_tags($od_id);

        if( $type == 'personal' ){
            $row = sql_fetch("select * from {$g5['g5_shop_personalpay_table']} where pp_id = $od_id ");
        } else {
            $row = sql_fetch("select * from {$g5['g5_shop_order_table']} where od_id = $od_id ");
        }

        return $row;
    }
}

if( ! function_exists('exists_inicis_shop_order') ){
    function exists_inicis_shop_order($oid, $pp=array(), $od_time='', $od_ip=''){

        $od_ip = $od_ip ? $od_ip : $_SERVER['REMOTE_ADDR'];

        //개인결제
        if( $pp ) {
            $hash_data = md5($pp['pp_id'].$pp['pp_price'].$pp['pp_time']);
            if( $hash_data == get_session('ss_personalpay_hash') ){
                // 개인결제번호제거
                set_session('ss_personalpay_id', '');
                set_session('ss_personalpay_hash', '');

                $uid = md5($pp['pp_id'].$pp['pp_time'].$od_ip);
                set_session('ss_personalpay_uid', $uid);
                
                goto_url(G5_SHOP_URL.'/personalpayresult.php?pp_id='.$pp['pp_id'].'&amp;uid='.$uid.'&amp;ini_noti=1');
            } else {
                goto_url(G5_SHOP_URL.'/personalpayresult.php?pp_id='.$pp['pp_id'].'&amp;ini_noti=1');
            }
        } else {    //그렇지 않으면
            if (!$od_time){
                $od_time = G5_TIME_YMDHIS;
            }

            if( $oid == get_session('ss_order_id') ){
                // orderview 에서 사용하기 위해 session에 넣고
                $uid = md5($oid.$od_time.$od_ip);
                set_session('ss_orderview_uid', $uid);
                goto_url(G5_SHOP_URL.'/orderinquiryview.php?od_id='.$oid.'&amp;uid='.$uid.'&amp;ini_noti=1');
            } else {
                goto_url(G5_SHOP_URL.'/orderinquiryview.php?od_id='.$oid.'&amp;ini_noti=1');
            }
        }
        return '';
    }
}

// 세션 초기화
set_session('P_TID',  '');
set_session('P_AMT',  '');
set_session('P_HASH', '');

$sql = " select * from {$g5['g5_shop_order_data_table']} where od_id = '$oid' ";
$row = sql_fetch($sql);

if( empty($row) ){  //이미 결제가 완료 되었다면
    if( $exist_order = get_shop_order_data($oid) ){    //상품주문
        exists_inicis_shop_order($oid, array(), $exist_order['od_time'], $exist_order['od_ip']);
        exit;
    } else if( $pp = get_shop_order_data($oid, 'personal') ){   //개인결제
        if($pp['pp_tno']){      //이미 결제가 완료되었다면
            exists_inicis_shop_order($oid, $pp, $pp['pp_time'], $pp['pp_ip']);
            exit;
        }
    }
}

$data = unserialize(base64_decode($row['dt_data']));

if(isset($data['pp_id']) && $data['pp_id']) {
    $order_action_url = G5_HTTPS_MSHOP_URL.'/personalpayformupdate.php';
    $page_return_url  = G5_SHOP_URL.'/personalpayform.php?pp_id='.$data['pp_id'];
} else {
    $order_action_url = G5_HTTPS_MSHOP_URL.'/orderformupdate.php';
    $page_return_url  = G5_SHOP_URL.'/orderform.php';
    if($_SESSION['ss_direct'])
        $page_return_url .= '?sw_direct=1';
}

$sql = " select * from {$g5['g5_shop_inicis_log_table']} where oid = '$oid' ";
$row = sql_fetch($sql);

if(!$row['oid'])
    alert('결제 정보가 존재하지 않습니다.\\n\\n올바른 방법으로 이용해 주십시오.', $page_return_url);

if($row['P_STATUS'] != '00')
    alert('오류 : '.$row['P_RMESG1'].' 코드 : '.$row['P_STATUS'], $page_return_url);

$PAY = array_map('trim', $row);

// TID, AMT 를 세션으로 주문완료 페이지 전달
$hash = md5($PAY['P_TID'].$PAY['P_MID'].$PAY['P_AMT']);
set_session('P_TID',  $PAY['P_TID']);
set_session('P_AMT',  $PAY['P_AMT']);
set_session('P_HASH', $hash);

// 로그 삭제
@sql_query(" delete from {$g5['g5_shop_inicis_log_table']} where oid = '$oid' ");

$g5['title'] = 'KG 이니시스 결제';
$g5['body_script'] = ' onload="setPAYResult();"';
include_once(G5_PATH.'/head.sub.php');

$exclude = array('res_cd', 'P_HASH', 'P_TYPE', 'P_AUTH_DT', 'P_VACT_BANK', 'P_AUTH_NO');

echo '<form name="forderform" method="post" action="'.$order_action_url.'" autocomplete="off">'.PHP_EOL;

echo make_order_field($data, $exclude);

echo '<input type="hidden" name="res_cd"      value="'.$PAY['P_STATUS'].'">'.PHP_EOL;
echo '<input type="hidden" name="P_HASH"      value="'.$hash.'">'.PHP_EOL;
echo '<input type="hidden" name="P_TYPE"      value="'.$PAY['P_TYPE'].'">'.PHP_EOL;
echo '<input type="hidden" name="P_AUTH_DT"   value="'.$PAY['P_AUTH_DT'].'">'.PHP_EOL;
echo '<input type="hidden" name="P_VACT_BANK" value="'.$PAY['P_FN_NM'].'">'.PHP_EOL;
echo '<input type="hidden" name="P_AUTH_NO"   value="'.$PAY['P_AUTH_NO'].'">'.PHP_EOL;

echo '</form>'.PHP_EOL;
?>

<div id="pay_working" style="display:none;">
     <span style="display:block; text-align:center;margin-top:120px"><img src="<?php echo G5_MOBILE_URL; ?>/shop/img/loading.gif" alt=""></span>
    <span style="display:block; text-align:center;margin-top:10px; font-size:14px">주문완료 중입니다. 잠시만 기다려 주십시오.</span>
</div>

<script type="text/javascript">
function setPAYResult() {
    setTimeout( function() {
        document.forderform.submit();
    }, 300);
}
</script>

<?php
include_once(G5_PATH.'/tail.sub.php');
?>
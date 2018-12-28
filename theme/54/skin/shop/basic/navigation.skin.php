<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가

if ($ca_id)
{
    $navigation = $bar = "";
    $len = strlen($ca_id) / 2;
    for ($i=1; $i<=$len; $i++)
    {
        $code = substr($ca_id,0,$i*2);

        $sql = " select ca_name from {$g5['g5_shop_category_table']} where ca_id = '$code' ";
        $row = sql_fetch($sql);

        $sct_here = '';
        if ($ca_id == $code) // 현재 분류와 일치하면
            $sct_here = 'sct_here';

        if ($i != $len) // 현재 위치의 마지막 단계가 아니라면
            $sct_bg = 'sct_bg';
        else $sct_bg = '';

        $navigation .= $bar.'<a href="./list.php?ca_id='.$code.'" class="'.$sct_here.' '.$sct_bg.'">'.$row['ca_name'].'</a>';
    }
}
else
    $navigation = $g5['title'];

//if ($it_id) $navigation .= " > $it[it_name]";
// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.G5_SHOP_CSS_URL.'/style.css">', 0);
?>

<!-- <div id="sct_location">
    <a href='<?php echo G5_SHOP_URL; ?>/' class="sct_bg">HOME</a>
    <?php echo $navigation; ?>
</div> -->

<div id="sct_location"> <!-- class="view_location" --> <!-- view_location는 리스트 말고 상품보기에서만 표시 -->
    <a href='<?php echo G5_SHOP_URL; ?>/' class="go_home"><span class="sound_only">메인으로</span><i class="fa fa-home" aria-hidden="true"></i></a>
    <i class="fa fa-angle-right" aria-hidden="true"></i>
    <select>
    	<option>카테고리<option>
    </select>
    <i class="fa fa-angle-right" aria-hidden="true"></i>
    <select>
    	<option>대분류<option>
    </select>
    <i class="fa fa-angle-right" aria-hidden="true"></i>
    <select>
    	<option>소분류<option>
    </select>
</div>

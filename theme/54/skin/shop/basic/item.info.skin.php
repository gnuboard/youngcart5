<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.G5_SHOP_CSS_URL.'/style.css">', 0);
?>

<script src="<?php echo G5_JS_URL; ?>/viewimageresize.js"></script>

<?php if ($default['de_rel_list_use']) { ?>
<!-- 관련상품 시작 { -->
<section id="sit_rel">
    <h2>관련상품</h2>
    <?php
    $rel_skin_file = $skin_dir.'/'.$default['de_rel_list_skin'];
    if(!is_file($rel_skin_file))
        $rel_skin_file = G5_SHOP_SKIN_PATH.'/'.$default['de_rel_list_skin'];

    $sql = " select b.* from {$g5['g5_shop_item_relation_table']} a left join {$g5['g5_shop_item_table']} b on (a.it_id2=b.it_id) where a.it_id = '{$it['it_id']}' and b.it_use='1' ";
    $list = new item_list($rel_skin_file, $default['de_rel_list_mod'], 0, $default['de_rel_img_width'], $default['de_rel_img_height']);
    $list->set_query($sql);
    echo $list->run();
    ?>
</section>
<!-- } 관련상품 끝 -->
<?php } ?>


<section id="sit_info">
	<div id="sit_tab">
	    <ul class="tab_tit">
	        <li><button type="button" rel="#sit_inf" class="selected">상품정보</button></li>
	        <li><button type="button" rel="#sit_use">사용후기 <span class="item_use_count"><?php echo $item_use_count; ?></span></button></li>
	        <li><button type="button" rel="#sit_qa">상품문의  <span class="item_qa_count"><?php echo $item_qa_count; ?></span></button></li>
	        <li><button type="button" rel="#sit_dex">배송/교환</button></li>
	    </ul>
	    <ul class="tab_con">
	
	        <!-- 상품 정보 시작 { -->
	        <li id="sit_inf">
	            <h2 class="contents_tit"><span>상품 정보</span></h2>
	
	            <?php if ($it['it_explan']) { // 상품 상세설명 ?>
	            <h3>상품 상세설명</h3>
	            <div id="sit_inf_explan">
	                <?php echo ($it['it_mobile_explan'] ? conv_content($it['it_mobile_explan'], 1) : conv_content($it['it_explan'], 1)); ?>
	            </div>
	            <?php } ?>
	
	
	            <?php
	            if ($it['it_info_value']) { // 상품 정보 고시
	                $info_data = unserialize(stripslashes($it['it_info_value']));
	                if(is_array($info_data)) {
	                    $gubun = $it['it_info_gubun'];
	                    $info_array = $item_info[$gubun]['article'];
	            ?>
	            <h3>상품 정보 고시</h3>
	            <table id="sit_inf_open">
	            <tbody>
	            <?php
	            foreach($info_data as $key=>$val) {
	                $ii_title = $info_array[$key][0];
	                $ii_value = $val;
	            ?>
	            <tr>
	                <th scope="row"><?php echo $ii_title; ?></th>
	                <td><?php echo $ii_value; ?></td>
	            </tr>
	            <?php } //foreach?>
	            </tbody>
	            </table>
	            <!-- 상품정보고시 end -->
	            <?php
	                } else {
	                    if($is_admin) {
	                        echo '<p>상품 정보 고시 정보가 올바르게 저장되지 않았습니다.<br>config.php 파일의 G5_ESCAPE_FUNCTION 설정을 addslashes 로<br>변경하신 후 관리자 &gt; 상품정보 수정에서 상품 정보를 다시 저장해주세요. </p>';
	                    }
	                }
	            } //if
	            ?>
	
	        </li>
	        <!-- 사용후기 시작 { -->
	        <li id="sit_use">
	            <h2>사용후기</h2>
	
	            <div id="itemuse"><?php include_once(G5_SHOP_PATH.'/itemuse.php'); ?></div>
	        </li>
	        <!-- } 사용후기 끝 -->
	
	        <!-- 상품문의 시작 { -->
	        <li id="sit_qa">
	            <h2>상품문의</h2>
	
	            <div id="itemqa"><?php include_once(G5_SHOP_PATH.'/itemqa.php'); ?></div>
	        </li>
	        <!-- } 상품문의 끝 -->
	        
	        <!-- 배송/교환 시작 { -->
	        <li id="sit_dex">
	            <h2>배송/교환정보</h2>
	            
	            <?php if ($default['de_baesong_content']) { // 배송정보 내용이 있다면 ?>
	            <!-- 배송 시작 { -->
	            <div id="sit_dvr">
	                <h3>배송</h3>
	                <?php echo conv_content($default['de_baesong_content'], 1); ?>
	            </div>
	            <!-- } 배송 끝 -->
	            <?php } ?>
	
	            <?php if ($default['de_change_content']) { // 교환/반품 내용이 있다면 ?>
	            <!-- 교환 시작 { -->
	            <div id="sit_ex" >
	                <h3>교환</h3>
	
	                <?php echo conv_content($default['de_change_content'], 1); ?>
	            </div>
	            <!-- } 교환 끝 -->
	            <?php } ?>
	        </li>
	        <!-- } 배송/교환  끝 -->
	    </ul>
	</div>
	<script>
	$(function (){
	    $(".tab_con>li").hide();
	    $(".tab_con>li:first").show();   
	    $(".tab_tit li button").click(function(){
	        $(".tab_tit li button").removeClass("selected");
	        $(this).addClass("selected");
	        $(".tab_con>li").hide();
	        $($(this).attr("rel")).show();
	    });
	});
	</script>
	<div id="sit_buy" class="fix">
	<div class="sit_buy_inner">
        <!-- 선택옵션 시작 { -->
        <section class="sit_side_option">
            <h3>선택옵션</h3>
            
            <select class="s_it_option">
            	<option>사이즈</option> <!-- 옵션명 -->
            	<option>라지</option>
            	<option>미디움</option>
            	<option>스몰</option>
            </select>

            <select class="s_it_option">
            	<option>색상</option> <!-- 옵션명 -->
            	<option>무지개</option>
            	<option>보라</option>
            	<option>연두</option>
            </select>
        </section>
        <!-- } 선택옵션 끝 -->

        <!-- 추가옵션 시작 { -->
        <section class="sit_side_option">
            <h3>추가옵션</h3>
            <select class="s_it_option">
            	<option>햄스터</option>
            	<option>고양이</option>
            	<option>라마</option>
            </select>
        </section>
        <!-- } 추가옵션 끝 -->

        <!-- 선택된 옵션 시작 { -->
        <section class="sit_sel_option">
            <h3>선택된 옵션</h3>
            <ul class="sit_opt_added">
                <li>
                    <div class="opt_name">
                    	<span class="sit_opt_subj">SIZE:M / COLOR:그레이</span>
                    </div>
    				<div class="opt_count">
    					<button type="button" class="sit_qty_minus"><i class="fa fa-minus" aria-hidden="true"></i><span class="sound_only">감소</span></button>
    					<input type="text" name="ct_qty[1446772772][]" value="1" class="num_input" size="5">
    					<button type="button" class="sit_qty_plus"><i class="fa fa-plus" aria-hidden="true"></i><span class="sound_only">증가</span></button>
    					<span class="sit_opt_prc">+3,000원</span>
    					<button type="button" class="sit_opt_del"><i class="fa fa-times" aria-hidden="true"></i><span class="sound_only">삭제</span></button>
    				</div>
                </li>
                <li>
                    <div class="opt_name">
                    	<span class="sit_opt_subj">SIZE:M / COLOR:그레이</span>
                    </div>
    				<div class="opt_count">
    					<button type="button" class="sit_qty_minus"><i class="fa fa-minus" aria-hidden="true"></i><span class="sound_only">감소</span></button>
    					<input type="text" name="ct_qty[1446772772][]" value="1" class="num_input" size="5">
    					<button type="button" class="sit_qty_plus"><i class="fa fa-plus" aria-hidden="true"></i><span class="sound_only">증가</span></button>
    					<span class="sit_opt_prc">+3,000원</span>
    					<button type="button" class="sit_opt_del"><i class="fa fa-times" aria-hidden="true"></i><span class="sound_only">삭제</span></button>
    				</div>
                </li>
            </ul>
        </section>
        <!-- } 선택된 옵션 끝 -->
	
		<div class="sum_section">        
	        <div class="sit_tot_price">
	        	<span>총 금액</span>
	        	<strong>34,000</strong> 원
	        </div>
			
			<div class="sit_order_btn">
				<button type="submit" onclick="document.pressed=this.value;" value="장바구니" class="sit_btn_cart">장바구니</button>
	            <button type="submit" onclick="document.pressed=this.value;" value="바로구매" class="sit_btn_buy">바로구매</button> 
	       </div>
		</div>
    </div>   
	</div>
</section>
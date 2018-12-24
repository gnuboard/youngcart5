<?php
include_once('./_common.php');

if (!$is_member)
    goto_url(G5_BBS_URL."/login.php?url=".urlencode(G5_SHOP_URL."/mypage.php"));

if (G5_IS_MOBILE) {
    include_once(G5_MSHOP_PATH.'/mypage.php');
    return;
}

// 테마에 mypage.php 있으면 include
if(defined('G5_THEME_SHOP_PATH')) {
    $theme_mypage_file = G5_THEME_SHOP_PATH.'/mypage.php';
    if(is_file($theme_mypage_file)) {
        include_once($theme_mypage_file);
        return;
        unset($theme_mypage_file);
    }
}

$g5['title'] = '마이페이지';
include_once('./_head.php');

// 쿠폰
$cp_count = 0;
$sql = " select cp_id
            from {$g5['g5_shop_coupon_table']}
            where mb_id IN ( '{$member['mb_id']}', '전체회원' )
              and cp_start <= '".G5_TIME_YMD."'
              and cp_end >= '".G5_TIME_YMD."' ";
$res = sql_query($sql);

for($k=0; $cp=sql_fetch_array($res); $k++) {
    if(!is_used_coupon($member['mb_id'], $cp['cp_id']))
        $cp_count++;
}
?>

<!-- 마이페이지 시작 { -->
<div id="smb_my">

    <!-- 회원정보 개요 시작 { -->
    <section id="smb_my_ov">
        <h2>회원정보 개요</h2>
        
        <div class="smb_me">
	        <strong class="my_ov_name"><img src="<?php echo G5_THEME_IMG_URL ;?>/no_profile.gif" alt="프로필이미지"><br><?php echo $member['mb_name']; ?></strong><br>
	        <a href="<?php echo G5_BBS_URL ?>/member_confirm.php?url=register_form.php" id="s_ol_after_info">정보수정</a>
	        <a href="<?php echo G5_BBS_URL ?>/logout.php" id="ol_after_logout">로그아웃</a>
        </div>
        
        <ul id="smb_private">
	    	<li>
	            <a href="<?php echo G5_BBS_URL ?>/point.php" target="_blank" class="win_point">
					<i class="fa fa-database" aria-hidden="true"></i>포인트
					<strong><?php echo number_format($member['mb_point']); ?></strong>
	            </a>
	        </li>
	        <li>
	        	<a href="<?php echo G5_SHOP_URL ?>/coupon.php" target="_blank" class="win_coupon">
	        		<i class="fa fa-ticket" aria-hidden="true"></i>쿠폰
	        		<strong><?php echo number_format($cp_count); ?></strong>
	        	</a>
	        </li>
	        <li>
	            <a href="<?php echo G5_BBS_URL ?>/memo.php" target="_blank" class="win_memo">
	            	<i class="fa fa-envelope-o" aria-hidden="true"></i><span class="sound_only">안 읽은 </span>쪽지
	                <strong><?php echo $memo_not_read ?></strong>
	            </a>
	        </li>
	        <li>
	            <a href="<?php echo G5_BBS_URL ?>/scrap.php" target="_blank" class="win_scrap">
	            	<i class="fa fa-thumb-tack" aria-hidden="true"></i>스크랩
	            	<strong class="scrap">0</strong>
	            </a>
	        </li>
	    </ul>
	    
        <h3>내정보</h3>
        <dl class="op_area">
            <dt>연락처</dt>
            <dd><?php echo ($member['mb_tel'] ? $member['mb_tel'] : '미등록'); ?></dd>
            <dt>E-Mail</dt>
            <dd><?php echo ($member['mb_email'] ? $member['mb_email'] : '미등록'); ?></dd>
            <dt>최종접속일시</dt>
            <dd><?php echo $member['mb_today_login']; ?></dd>
            <dt>회원가입일시</dt>
            <dd><?php echo $member['mb_datetime']; ?></dd>
            <dt id="smb_my_ovaddt">주소</dt>
            <dd id="smb_my_ovaddd"><?php echo sprintf("(%s%s)", $member['mb_zip1'], $member['mb_zip2']).' '.print_address($member['mb_addr1'], $member['mb_addr2'], $member['mb_addr3'], $member['mb_addr_jibeon']); ?></dd>
        </dl>
        
        <a href="<?php echo G5_BBS_URL; ?>/member_confirm.php?url=member_leave.php" onclick="return member_leave();" class="withdrawal">회원탈퇴</a>
    </section>
    <!-- } 회원정보 개요 끝 -->

	<div id="smb_my_list">
	    <!-- 최근 주문내역 시작 { -->
	    <section id="smb_my_od">
	        <h2>주문내역조회</h2>
	        <?php
	        // 최근 주문내역
	        define("_ORDERINQUIRY_", true);
	
	        $limit = " limit 0, 5 ";
	        include G5_SHOP_PATH.'/orderinquiry.sub.php';
	        ?>
	
	        <div class="smb_my_more">
	            <a href="./orderinquiry.php">더보기</a>
	        </div>
	    </section>
	    <!-- } 최근 주문내역 끝 -->
	
	    <!-- 최근 위시리스트 시작 { -->
	    <section id="smb_my_wish">
	        <h2>최근 위시리스트</h2>
            <ul>
            <?php
            $sql = " select *
                       from {$g5['g5_shop_wish_table']} a,
                            {$g5['g5_shop_item_table']} b
                      where a.mb_id = '{$member['mb_id']}'
                        and a.it_id  = b.it_id
                      order by a.wi_id desc
                      limit 0, 8 ";
            $result = sql_query($sql);
            for ($i=0; $row = sql_fetch_array($result); $i++)
            {
                $image = get_it_image($row['it_id'], 100, 100, true);
            ?>

            <li>
                <div class="smb_my_img"><?php echo $image; ?></div>
                <div class="smb_my_tit"><a href="./item.php?it_id=<?php echo $row['it_id']; ?>"><?php echo stripslashes($row['it_name']); ?></a></div>
                <div class="smb_my_price">500,000</div>
                <div class="smb_my_date"><?php echo $row['wi_time']; ?></div>
                <a href="./wishupdate.php?w=d&amp;wi_id=<?php echo $row['wi_id']; ?>" class="wish_del"><i class="fa fa-trash" aria-hidden="true"></i><span class="sound_only">삭제</span></a>
            </li>

            <?php
            }

            if ($i == 0)
                echo '<li class="empty_li">보관 내역이 없습니다.</li>';
            ?>
            </ul>
	
	        <div class="smb_my_more">
	            <a href="./wishlist.php">더보기</a>
	        </div>
	        
	        <div id="smb_ws_act">
		        <button type="submit" class="btn01" onclick="return fwishlist_check(document.fwishlist,'');">장바구니</button>
		        <button type="submit" class="btn02" onclick="return fwishlist_check(document.fwishlist,'direct_buy');">주문하기</button>
		    </div>
	    </section>
	    <!-- } 최근 위시리스트 끝 -->
	</div>
</div>

<script>
$(function() {
    $(".win_coupon").click(function() {
        var new_win = window.open($(this).attr("href"), "win_coupon", "left=100,top=100,width=700, height=600, scrollbars=1");
        new_win.focus();
        return false;
    });
});

function member_leave()
{
    return confirm('정말 회원에서 탈퇴 하시겠습니까?')
}
</script>
<!-- } 마이페이지 끝 -->

<?php
include_once("./_tail.php");
?>
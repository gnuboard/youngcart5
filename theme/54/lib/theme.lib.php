<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

function item_icon2($it)
{
    global $g5;

    // 품절
    if (is_soldout($it['it_id']))
        $icon .= '<span class="shop_icon_soldout"><span class="soldout_txt">SOLD OUT</span></span>';

    return $icon;
}

function memo_recv_count($mb_id)
{
    global $g5;

    if(!$mb_id)
        return 0;

    $sql = " select count(*) as cnt from {$g5['memo_table']} where me_recv_mb_id = '$mb_id' and me_read_datetime = '0000-00-00 00:00:00' ";
    $row = sql_fetch($sql);
    return $row['cnt'];
}



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


function get_it_image2($it_id, $width, $height=0, $anchor=false, $img_id='', $img_alt='', $is_crop=false)
{
    global $g5;

    if(!$it_id || !$width)
        return '';

    $sql = " select it_id, it_img1, it_img2, it_img3, it_img4, it_img5, it_img6, it_img7, it_img8, it_img9, it_img10 from {$g5['g5_shop_item_table']} where it_id = '$it_id' ";
    $row = sql_fetch($sql);

    if(!$row['it_id'])
        return '';

    for($i=1;$i<=10; $i++) {
        $file = G5_DATA_PATH.'/item/'.$row['it_img'.$i];
        if(is_file($file) && $row['it_img'.$i]) {
            $size = @getimagesize($file);
            if($size[2] < 1 || $size[2] > 3)
                continue;

            $filename = basename($file);
            $filepath = dirname($file);
            $img_width = $size[0];
            $img_height = $size[1];

            if($i == 2) break;
        }
    }

    if($img_width && !$height) {
        $height = round(($width * $img_height) / $img_width);
    }

    if($filename) {
        //thumbnail($filename, $source_path, $target_path, $thumb_width, $thumb_height, $is_create, $is_crop=false, $crop_mode='center', $is_sharpen=true, $um_value='80/0.5/3')
        $thumb = thumbnail($filename, $filepath, $filepath, $width, $height, false, $is_crop, 'center', false, $um_value='80/0.5/3');
    }

    if($thumb) {
        $file_url = str_replace(G5_PATH, G5_URL, $filepath.'/'.$thumb);
        $img = '<img src="'.$file_url.'" width="'.$width.'" height="'.$height.'" alt="'.$img_alt.'"';
    } else {
        $img = '<img src="'.G5_SHOP_URL.'/img/no_image.gif" width="'.$width.'"';
        if($height)
            $img .= ' height="'.$height.'"';
        $img .= ' alt="'.$img_alt.'"';
    }

    if($img_id)
        $img .= ' id="'.$img_id.'"';
    $img .= '>';

    if($anchor)
        $img = '<a href="'.G5_SHOP_URL.'/item.php?it_id='.$it_id.'">'.$img.'</a>';

    return $img;
}


function get_item_event_info($it_id)
{
    global $g5;

    $data = array();

    $sql = " select distinct ev_id from {$g5['g5_shop_event_item_table']} where it_id = '$it_id' ";
    $result = sql_query($sql);

    for($i=0; $row=sql_fetch_array($result); $i++) {
        // 이벤트정보
        $sql = " select ev_id, ev_subject from {$g5['g5_shop_event_table']} where ev_id = '{$row['ev_id']}' and ev_use = '1' ";
        $ev  = sql_fetch($sql);
        if(!$ev['ev_id'])
            continue;

        // 배너이미지
        $file = G5_DATA_PATH.'/event/'.$ev['ev_id'].'_m';
        if(!is_file($file))
            continue;

        $subject = $ev['ev_subject'];
        $img     = str_replace(G5_DATA_PATH, G5_DATA_URL, $file);

        $data[] = array('ev_id' => $row['ev_id'], 'subject' => $subject, 'img' => $img);
    }

    return $data;
}
?>
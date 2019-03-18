$(function () {
    $(document).on("click", ".btn_cart", function() {
        var it_id = $(this).data("it_id");
        var $opt = $(this).closest("li.sct_li").find(".sct_cartop");
        var $btn = $(this).closest("li.sct_li").find(".sct_btn");

        $(".sct_cartop").not($opt).css("display", "");

        $.ajax({
            url: g5_theme_shop_url+"/ajax.itemoption.php",
            type: "POST",
            data: {
                "it_id" : it_id
            },
            dataType: "json",
            async: true,
            cache: false,
            success: function(data, textStatus) {
                if(data.error != "") {
                    alert(data.error);
                    return false;
                }

                $opt.html(data.html);

                if(!data.option) {
                    add_cart($opt.find("form").get(0));
                    return;
                }

                $btn.css("display","none");
                $opt.css("display","block");
            }
        });
    });

    $(document).on("change", "select.it_option", function() {
        var $frm = $(this).closest("form");
        var $sel = $frm.find("select.it_option");
        var sel_count = $sel.size();
        var idx = $sel.index($(this));
        var val = $(this).val();
        var it_id = $frm.find("input[name='it_id[]']").val();

        // 선택값이 없을 경우 하위 옵션은 disabled
        if(val == "") {
            $frm.find("select.it_option:gt("+idx+")").val("").attr("disabled", true);
            return;
        }

        // 하위선택옵션로드
        if(sel_count > 1 && (idx + 1) < sel_count) {
            var opt_id = "";

            // 상위 옵션의 값을 읽어 옵션id 만듬
            if(idx > 0) {
                $frm.find("select.it_option:lt("+idx+")").each(function() {
                    if(!opt_id)
                        opt_id = $(this).val();
                    else
                        opt_id += chr(30)+$(this).val();
                });

                opt_id += chr(30)+val;
            } else if(idx == 0) {
                opt_id = val;
            }

            $.post(
                g5_shop_url + "/itemoption.php",
                { it_id: it_id, opt_id: opt_id, idx: idx, sel_count: sel_count },
                function(data) {
                    $sel.eq(idx+1).empty().html(data).attr("disabled", false);

                    // select의 옵션이 변경됐을 경우 하위 옵션 disabled
                    if(idx+1 < sel_count) {
                        var idx2 = idx + 1;
                        $frm.find("select.it_option:gt("+idx2+")").val("").attr("disabled", true);
                    }
                }
            );
        } else if((idx + 1) == sel_count) { // 선택옵션처리
            if(val == "")
                return;

            var info = val.split(",");
            // 재고체크
            if(parseInt(info[2]) < 1) {
                alert("선택하신 선택옵션상품은 재고가 부족하여 구매할 수 없습니다.");
                return false;
            }
        }
    });

    $(document).on("click", ".cartopt_cart_btn", function() {
        add_cart(this.form);
    });

    $(document).on("click", ".cartopt_close_btn", function() {
        $(this).closest(".sct_cartop").css("display","none");
        $(this).closest("li.sct_li").find(".sct_btn").css("display", "");
    });

    $(document).on("click", ".btn_wish", function() {
        add_wishitem(this);
    });
});

function add_wishitem(el)
{
    var $el   = $(el);
    var it_id = $el.data("it_id");

    if(!it_id) {
        alert("상품코드가 올바르지 않습니다.");
        return false;
    }

    $.post(
        g5_theme_shop_url + "/ajax.wishupdate.php",
        { it_id: it_id },
        function(error) {
            if(error != "OK") {
                alert(error.replace(/\\n/g, "\n"));
                return false;
            }

            alert("상품을 위시리스트에 담았습니다.");
            return;
        }
    );
}

function add_cart(frm)
{
    var $frm = $(frm);
    var $sel = $frm.find("select.it_option");
    var it_name = $frm.find("input[name^=it_name]").val();
    var it_price = parseInt($frm.find("input[name^=it_price]").val());
    var id = "";
    var value, info, sel_opt, item, price, stock, run_error = false;
    var option = sep = "";
    var count = $sel.size();

    if(count > 0) {
        $sel.each(function(index) {
            value = $(this).val();
            item  = $(this).prev("label").text();

            if(!value) {
                run_error = true;
                return false;
            }

            // 옵션선택정보
            sel_opt = value.split(",")[0];

            if(id == "") {
                id = sel_opt;
            } else {
                id += chr(30)+sel_opt;
                sep = " / ";
            }

            option += sep + item + ":" + sel_opt;
        });

        if(run_error) {
            alert(it_name+"의 "+item+"을(를) 선택해 주십시오.");
            return false;
        }

        price = value[1];
        stock = value[2];
    } else {
        price = 0;
        stock = $frm.find("input[name^=it_stock]").val();
        option = it_name;
    }

    // 금액 음수 체크
    if(it_price + parseInt(price) < 0) {
        alert("구매금액이 음수인 상품은 구매할 수 없습니다.");
        return false;
    }

    // 옵션 선택정보 적용
    $frm.find("input[name^=io_id]").val(id);
    $frm.find("input[name^=io_value]").val(option);
    $frm.find("input[name^=io_price]").val(price);

    $.ajax({
        url: frm.action,
        type: "POST",
        data: $(frm).serialize(),
        dataType: "json",
        async: true,
        cache: false,
        success: function(data, textStatus) {
            if(data.error != "") {
                alert(data.error);
                return false;
            }

            alert("상품을 장바구니에 담았습니다.");
        }
    });

    return false;
}

// php chr() 대응
if(typeof chr == "undefined") {
    function chr(code)
    {
        return String.fromCharCode(code);
    }
}
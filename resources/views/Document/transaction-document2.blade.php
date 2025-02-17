@php
    \Debugbar::disable();
    function getNumberStringKorean($num_value){
        // 1 ~ 9 한글 표시
        $arrNumberWord = array("","일","이","삼","사","오","육","칠","팔","구");
        // 10, 100, 100 자리수 한글 표시
        $arrDigitWord = array("","십","백","천");
        // 만단위 한글 표시
        $arrManWord = array("","만","억", "조");

        $num_length = strlen($num_value);
        $han_value = "";
        $man_count = 0;      // 만단위 0이 아닌 금액 카운트.

        for($i=0; $i < $num_length; $i++) {
            // 1단위의 문자로 표시.. (0은 제외)
            $strTextWord = $arrNumberWord[substr($num_value, $i, 1)];
            // 0이 아닌경우만, 십/백/천 표시
            if($strTextWord != ""){
                $man_count++;
                $strTextWord = $strTextWord . $arrDigitWord[($num_length - ($i+1)) % 4];
            }

            // 만단위마다 표시 (0인경우에도 만단위는 표시한다)
            if($man_count != 0 && ($num_length - ($i+1)) % 4 == 0){
                $man_count = 0;
                $strTextWord = $strTextWord . $arrManWord[($num_length - ($i+1)) / 4];
            }
            $han_value = $han_value . $strTextWord;
        }
        if($num_value != 0)
            $han_value = "일금 " . $han_value . "원 정";

        return $han_value;
    }
    $sum_amount = 0;

@endphp

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
    <title>거래내역서</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <style type="text/css">
        .body { margin: 0;}
        .border_out {font-family: 맑은고딕;border: 2px solid  black;}
        body, table, tr, td {font-family:맑은고딕, verdana, arial; font-size: 12px;color: #000000; border:0px;}
        .border_in {border-width:1px; border-color:#9f9f9fb3; border-style:solid none solid solid;}
        .border_basic {border: 1px solid #9f9f9fb3; }
        .border_bottom {border-width:1px; border-color:#9f9f9fb3; border-style:none none solid none ;}
        .border_top {border-width:1px; border-color:#9f9f9fb3; border-style:none solid none  none ;}
        .l_dot {border-style:dotted; border-width:0 0 0 1px; border-color:#9f9f9fb3;}
        .lb_dot {border-style:dotted; border-width:0 0 1px 1px; border-color:#9f9f9fb3;}
        .tl_dot {border-style:solid solid solid dotted; border-width:1px 0px 1px 1px; border-color:black black black #9f9f9fb3;}
        #command_bar {
            font-size: 10pt;
            background-color: #8facd4;
            border: 1px solid #8facd4;
            padding: 5px;
            margin-bottom: 10px;
        }
        #command_bar>input{
            background-color:#fff;
            border: solid 1px #ccc;
            border-radius: 3px;
            margin:5px;
            padding:5px;
        }
        .sign_area {
            position: relative;
        }
        .sign_img {
            position: absolute;
            top: 15px;
            left: 190px;
        }
        .back_gray {
            background-color:#384c6038
        }
        .back_rgray{
            background-color:#384c6012
        }

        .regular{
            font-size:12px;
            font-weight:550;
        }
        .write_div textarea {width:100%;height:100px}
        .reciept_tbl{
            border: solid 1px #ccc;
            padding:10px;
            border-collapse: collapse;
            width:100%;
        }
        .reciept_tbl th{
            border: solid 1px #ccc;
            background-color:#e5e5ec;
            -webkit-print-color-adjust:exact;
            padding:10px;
            border-collapse: collapse;
        }
        .reciept_tbl td{
            border: solid 1px #ccc;
            padding:10px;
            border-collapse: collapse;
        }

        #sch_res_detail {background:#f7f7f7;padding:30px;text-align:center;margin:0 0 10px}
        #sch_res_detail legend {margin:0;padding:0;font-size:0;line-height:0;text-indent:-9999em;overflow:hidden}
        #sch_res_detail select {border:1px solid #d0d3db;width:175px;height:45px;padding:0 5px;border-radius:3px}
        #sch_res_detail .frm_input {border:1px solid #d0d3db;width:300px;height:45px;border-radius:3px}
        #sch_res_detail .sch_wr {position:relative;display:inline-block}
        #sch_res_detail .btn_submit {padding:0 10px;height:45px;width:88px;font-size:1.083em;font-weight:bold;color:#fff;background:#434a54;border:1px solid #ccc;border-radius:3px;}
    </style>
    <style type="text/css" media="print">
        @page {
            margin: 20;  /* this affects the margin in the printer settings */
        }
        .btn_confirm{display:none;}
        .back_gray {
            background-color:#384c6038 !important; -webkit-print-color-adjust:exact;
        }
        .back_rgray{
            background-color:#384c6012!important; -webkit-print-color-adjust:exact;
        }
    </style>
    <script type="text/javascript">

        window.resizeTo(500,690);
        window.focus();

        function printNow() {
            document.getElementById('command_bar').style.display = 'none';
            window.print();
        }
    </script>
    <script src="{{ URL::asset('/assets/libs/jquery/jquery.min.js')}}"></script>
</head>
@auth
    <div class="btn_confirm write_div" >
        <form name="fsearch" action="" method="get">
            <fieldset id="sch_res_detail">
                <font color="#313fa2">* 업체명 / 이메일을 확인 후 발송해주세요! </font>
                업체명 : <input type="text" name="com_name" value="{{ $com_info -> com_name }}" id="com_name"  style="width:180px;height:30px;"  class="frm_input" size="40"  placeholder="업체명">&nbsp;&nbsp;
                이메일 : <input type="text" name="email" value="{{ $com_info -> email }}" id="email"  style="width:220px;height:30px;"  class="frm_input" size="40"  placeholder="이메일">&nbsp;&nbsp;
                <button type="button" class="btn_submit"  style="height:40px;" onclick="send_email()">메일 발송</button>
                <button type="button" class="btn_submit"  style="height:40px;width:100px;">계산서 발행</button>
                <a href ="https://tms.flabiz.kr"><button type="button" onclick="" class="btn_submit"  style="height:40px;width:100px; position: relative; right: -50px">TMS 홈으로</button></a>
            </fieldset>
        </form>
    </div>
@endauth
<body>
<div id="command_bar">
    &nbsp;※ 인쇄방향을 가로로 설정하세요. &nbsp; <input type="button" value="인쇄하기" onclick="printNow()" />
</div>

<div style='padding:10px;width:1300px;'>
    <div align="center" >
        <h1>{{ $com_info -> com_name }} 거래내역서 </h1>
        <h2>{{ $tran_year . '년 ' . $tran_month . '월' }} 주문내역 </h2>
    </div>
    <div align="right">
        <div align="left" style='width:300px;'>
            <p class="regular">회사명 : {{ $com_info -> com_name }}  </p>
            <p class="regular">사업자등록번호 : {{ $com_info -> com_business_num }}</p>
        </div>
    </div>
    <div>
        <table class='reciept_tbl'>
            <tr>
                <th>주문일</th>
                <th>배송일</th>
                <th>받는 분</th>
                <th>배송지</th>
                <th>보내는 분</th>
                <th>품목</th>
                <th>주문금액</th>
            </tr>
            @for($i=0; $i< count($orders); $i++)
            <tr height="22">
                <td class="" style="border-top-style:none" align="center">
                    @if(!empty($orders[$i]->payment_date))
                        {{ Carbon\Carbon::parse($orders[$i]->payment_date) -> format('Y-m-d')}}
                    @else
                        {{ Carbon\Carbon::parse($orders[$i]->order_time) -> format('Y-m-d')}}
                    @endif
                </td>
                <td class="" style="border-top-style:none" align="center">{{ $orders[$i] -> delivery -> delivery_date }}</td>
                <td class="" style="border-top-style:none" align="center">{{ $orders[$i] -> delivery -> receiver_name }}</td>
                <td class="" style="border-top-style:none" align="center">{{ $orders[$i] -> delivery -> delivery_address }}</td>
                <td class="right_black" style="border-top-style:none" align="center">{{ $orders[$i] -> delivery -> delivery_ribbon_left }}&nbsp; </td>
                <td class="right_black" style="border-top-style:none" align="center">{{ $orders[$i] -> dataAdd -> pr_name }}&nbsp; </td>
                <td class="right_black" style="border-top-style:none" align="right"><font></font>{{ number_format($orders[$i] -> total_amount) }}&nbsp; </td>
            </tr>
                @php $sum_amount += $orders[$i] -> total_amount @endphp
            @endfor
        </table>
        <div align="right">
            <div align="left" style='width:300px;'>
                <p class="regular" align="right">합계 :  ￦ {{ number_format($sum_amount) }} </p>
                @if($com_info -> discount > 0)
                    <p class='regular' align='right'>할인가격(할인율:{{ $com_info -> discount }}%):  ￦ {{ number_format($sum_amount*(100 - $com_info -> discount)/100) }}</p>
                @endif
            </div>
        </div>
    </div>
</div>
</body>
<script type="text/javascript">
@auth
    function send_email() {

        $.ajax({
            url : "{{ url('api/Document/transaction/Send') }}",
            type: "POST",
            data: {
                email: document.getElementById('email').value,
                com_name: document.getElementById('com_name').value,
                brand: '{{ $brand_info -> shop_name }}',
                receipt_date: "{{ $tran_year . '년 ' . $tran_month . '월' }}",
                brand_tel: '{{ $brand_info -> com_tel }}',
                data: "{{ $data }}",
                handler: "{{ Auth::user() -> name }}"
            },
            success: function(data) {
                alert(data.message)
            },
            error: function(e) {
                alert('[에러발생] 개발팀에 문의하세요.')
                console.log(e)
            }
        })
    }
    $.ajaxSetup({
        beforeSend: function () {
            var width = 0;
            var height = 0;
            var left = 0;
            var top = 0;

            width = 50;
            height = 50;
            top = ( $(window).height() - height ) / 2 + $(window).scrollTop();
            left = ( $(window).width() - width ) / 2 + $(window).scrollLeft();

            if($("#div_ajax_load_image").length != 0) {
                $("#div_ajax_load_image").css({
                    "top": top+"px",
                    "left": left+"px"
                });
                $("#div_ajax_load_image").show();
            }else {
                $('body').append('<div id="div_ajax_load_image" style="position:fixed; top:50%; left:50%; width:' + width + 'px; height:' + height + 'px; z-index:9999; filter:alpha(opacity=50); opacity:alpha*0.5; margin:auto; padding:0; "><img src="https://flasystem.flabiz.kr/assets/images/loading.gif" style="width:100px; height:100px;"></div>');
                // $('body').append('<div id="div_ajax_load_image" style="position:absolute; top:' + top + 'px; left:' + left + 'px; width:' + width + 'px; height:' + height + 'px; z-index:9999; filter:alpha(opacity=50); opacity:alpha*0.5; margin:auto; padding:0; "><img src="https://flasystem.flabiz.kr/assets/images/loading.gif" style="width:100px; height:100px;"></div>');
            }
        },
        complete: function () {
            $("#div_ajax_load_image").hide();
        }
    })
@endauth
</script>
</html>
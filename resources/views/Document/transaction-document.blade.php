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
<html lang="">
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
            background-color: #ababab;
            border: 1px solid #ababab;
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
        .list_table th{
            border-bottom: 1px solid black ;
            border-right :1px solid #9f9f9fb3;
        }
        .list_table td{
            border-bottom: 1px solid #9f9f9fb3 ;
            border-right :1px solid #9f9f9fb3;
            padding: 0 5px;
        }
        .right_black{
            border-right: 1px solid black !important;
        }
        .black_border{
            border-top: 1px solid black !important;
            border-right: 1px solid black !important;
        }
        .my-double {
            text-decoration-line: underline;
            text-decoration-style: double;
            font-size:30px;
            padding-left:230px;
        }
        .write_div textarea {width:100%;height:100px}

        /* 전체검색결과 스킨 */
        #sch_res_detail {background:#f7f7f7;padding:30px;text-align:center;margin:0 0 10px}
        #sch_res_detail legend {position:absolute;margin:0;padding:0;font-size:0;line-height:0;text-indent:-9999em;overflow:hidden}
        #sch_res_detail select {border:1px solid #d0d3db;width:175px;height:45px;padding:0 5px;border-radius:3px}
        #sch_res_detail .frm_input {border:1px solid #d0d3db;width:300px;height:45px;border-radius:3px}
        #sch_res_detail .sch_wr {position:relative;display:inline-block}
        #sch_res_detail .btn_submit {padding:0 10px;height:45px;width:88px;font-size:1.083em;font-weight:bold;color:#fff;background:#434a54;border:1px solid #ccc;}
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
                업체명 : <input type="text" name="com_name" value="{{ $com_info -> com_name }}" id="com_name"  style="width:180px;height:30px;"  class="frm_input" size="40" placeholder="업체명">&nbsp;&nbsp;
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
    &nbsp; ※ 세로방향으로 설정해주세요&nbsp; <input type="button" value="인쇄하기" onclick="printNow()" />
</div>
<div style='padding:10px;'>
    <table width="700" border="0" cellspacing="0" cellpadding="0" style="table-layout:auto">
        <tr>
            <td>
                <div><p class='my-double'>&nbsp; 거 래 내 역 서 &nbsp;</p></div>
                <table width="700" cellpadding="0" cellspacing="0" class="border_out">
                    <tr>
                        <td>
                            <table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td><table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
                                            <tr height="30">
                                                <td width="75%" class="">
                                                    <table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0" class="border_in">
                                                        <tr >
                                                            <td align="center"  colspan='2'><strong style="font-size:12px;">{{ $com_info -> com_name }}</strong></td>
                                                            <td width="25%" align="center" class="" style="font-size:12px;">귀하</td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center"><strong style="font-size:11px">거래 월</strong></td>
                                                            <td width="60%" colspan='2' align="center" class="" style="font-size:12px;">{{ $tran_year . '년 ' . $tran_month . '월' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center"><strong style="font-size:11px">TEL</strong></td>
                                                            <td width="60%" colspan='2' align="center" class="" style="font-size:12px;"></td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center"><strong style="font-size:11px">MAIL</strong></td>
                                                            <td width="60%" colspan='2' align="center" class="" style="font-size:12px;"></td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td width="55%"><div class="sign_area"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td class="border_in back_gray" width="25" align="center" ><span style="line-height:38px;font-weight: 600;">
                                                    발<br>
                                                    주<br>
                                                    처
                                                    </span>
                                                </td>
                                                <td><table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
                                                        <tr height="30">
                                                            <td width="70" align="center" class="border_in back_rgray">등록번호</td>
                                                            <td class="border_in" align="center"><span style="font-size:13px;font-weight:bold">{{ $brand_info -> shop_business_number }}</span></td>
                                                        </tr>
                                                        <tr height="30">
                                                            <td align="center" class="border_in back_rgray" style="border-top-style:none">상 호</td>
                                                            <td><table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
                                                                    <tr>
                                                                        <td class="border_in" style="border-top-style:none" align="center"><strong style="font-size:11px">{{ $brand_info -> com_name }}</strong></td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        <tr height="30">
                                                            <td align="center" class="border_in back_rgray" style="border-top-style:none">대 표 자</td>
                                                            <td class="border_in" style="border-top-style:none" align="center">{{ $brand_info -> ceo_name }}</td>
                                                        </tr>
                                                        <tr height="30">
                                                            <td align="center" class="border_in back_rgray" style="border-top-style:none">업 태</td>
                                                            <td>
                                                                <table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
                                                                    <tr>
                                                                        <td class="border_in" style="border-top-style:none" align="center">{{ $brand_info -> com_type }}</td>
                                                                        <td class="border_in back_rgray" style="border-top-style:none" width="70" align="center">업 종</td>
                                                                        <td class="border_in" style="border-top-style:none" align="center">{{ $brand_info -> com_kind }}</td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        <tr height="45">
                                                            <td align="center" class="border_in back_rgray" style="border-top-style:none">주 소</td>
                                                            <td class="border_in" style="border-top-style:none; padding: 0 5px" align="center">
                                                                {{ $brand_info -> com_address }}
                                                            </td>
                                                        </tr>
                                                        <tr height="30">
                                                            <td align="center" class="border_in back_rgray" style="border-top-style:none">전화번호</td>
                                                            <td>
                                                                <table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
                                                                    <tr>
                                                                        <td class="border_in" style="border-top-style:none" align="center">{{ $brand_info -> com_tel }}</td>
                                                                        <td class="border_in back_rgray" style="border-top-style:none" width="70" align="center">팩스번호</td>
                                                                        <td class="border_in" style="border-top-style:none" align="center">{{ $brand_info -> com_fax }}</td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </table></td>
                                            </tr>
                                            </table></div></td>
                                </tr>
                            </table></td>
                    </tr>
                    <tr>
                        <td>
                            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-top-style:none;">
                                <tr height="25">
                                    <td width="100" align="center"><strong>합계금액 : </strong></td>
                                    <td>&nbsp; <strong style="font-size:12px"><!----><?php //=getNumberStringKorean($all_pay);?> ( ￦ <!----><?php //=number_format($all_pay);?>)</strong></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <br>

    <table width="700" border="0" cellspacing="0" cellpadding="0" class="list_table border_out" style="">
        <tr height="25" style="text-align: center">
            <th class="back_rgray right_black" align="center">NO</th>
            <th class="back_rgray" align="center">주문일</th>
            <th class="back_rgray" align="center">품 명</th>
            <th class="back_rgray" align="center">배송날짜</th>
            <th class="right_black back_rgray" align="center">수 신</th>
            <th class="right_black back_rgray" align="center">수 량</th>
            <th class="right_black back_rgray" align="center">단 가</th>
            <th class="right_black back_rgray" align="center">공급가액</th>
            <th class="back_rgray" align="center">비 고</th>
        </tr>

        @for($i=0; $i< count($orders); $i++)
            <tr height="22">
                <td class="right_black" style="border-top-style:none;border-left-style:none" align="center">{{ $i+1 }}</td>
                <td class="" style="border-top-style:none; min-width: 72px" align="center">
                    @if(!empty($orders[$i]->payment_date))
                        {{ Carbon\Carbon::parse($orders[$i]->payment_date) -> format('Y-m-d')}}
                    @else
                        {{ Carbon\Carbon::parse($orders[$i]->order_time) -> format('Y-m-d')}}
                    @endif
                </td>
                <td class="" style="border-top-style:none; min-width: 48px" align="center">{{ $orders[$i] -> dataAdd -> pr_name }}</td>
                <td class="" style="border-top-style:none; min-width: 72px" align="center">{{ $orders[$i] -> delivery -> delivery_date }}</td>
                <td class="right_black" style="border-top-style:none" align="center">{{ $orders[$i] -> delivery -> delivery_ribbon_left }}</td>
                <td class="right_black" style="border-top-style:none; min-width: 30px" align="right">1</td>
                <td class="right_black" style="border-top-style:none;" align="right">{{ number_format($orders[$i] -> total_amount) }}</td>
                <td class="right_black" style="border-top-style:none;" align="right">{{ number_format($orders[$i] -> total_amount) }}</td>
                <td class="" style="border-top-style:none; min-width: 30px" align="center"></td>
            </tr>
            @php $sum_amount += $orders[$i] -> total_amount @endphp
        @endfor

        @for($j=$i; $j<25; $j++)
            <tr height="22">
                <td class="right_black" style="border-top-style:none;border-left-style:none" align="center">{{ $j + 1 }}</td>
                <td class="" style="border-top-style:none" align="center"></td>
                <td class="" style="border-top-style:none" align="center"></td>
                <td class="" style="border-top-style:none" align="center"></td>
                <td class="right_black" style="border-top-style:none" align="right">&nbsp; </td>
                <td class="right_black" style="border-top-style:none" align="right">&nbsp; </td>
                <td class="right_black" style="border-top-style:none" align="right">&nbsp; </td>
                <td class="right_black" style="border-top-style:none" align="right"> &nbsp; </td>
                <td class="" style="border-top-style:none" align="center"></td>
            </tr>
        @endfor


        <tr height="28">
            <td class="black_border back_rgray" colspan='5' align="center"><strong>합 계</strong></td>
            <td class="black_border"  align="center">{{ count($orders) }}</td>
            <td class="black_border" colspan='2' align="right">{{ number_format($sum_amount) }}</td>
            <td class="black_border"  align="center"></td>
        </tr>
        <tr height="28">
            <td class="black_border back_rgray" colspan='3' align="center"><strong>비 고</strong></td>
            <td class="black_border" colspan='7'   align="center">
                {{ $brand_info -> bank_info }}
            </td>
        </tr>
    </table>

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
@extends('layouts.master')
@inject('CommonCode', 'App\Models\CommonCode')
@inject('ComInfo', 'App\Models\CodeOfCompanyInfo')
@section('title')
    거래내역서
@endsection
@section('css')
    <link href="{{ URL::asset('/assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('/assets/css/transaction.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('/assets/libs/select2/select2.min.css') }}" rel="stylesheet">
    <script src="{{ URL::asset('/assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
@endsection
@section('content')
<script src="{{ URL::asset('/assets/libs/select2/select2.min.js') }}"></script>
<div class="row">
<div class="col-12">
<div class="card">
<div class="card-body p-0">
    <div class="accordion accordion-flush" id="accordionFlushExample">
        <div class="accordion-item">
            <h2 class="accordion-header" id="flush-headingTwo">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseTwo" aria-expanded="false" aria-controls="flush-collapseTwo">
                    <strong>검색 필터 열기</strong>
                </button>
            </h2>
            <form method="get" action="?">
                <div id="flush-collapseTwo" class="accordion-collapse collapse show" aria-labelledby="flush-headingTwo" data-bs-parent="#accordionFlushExample">
                    <div class="accordion-body">
                        <div class="input-group mb-3">
                            <div class="btn-group col-md-2 me-4">
                                <button type="button" class="btn btn-light col-md-3 waves-effect">
                                    <span id="sw_1_title">
                                        {{ request() -> sw_1_view ?? '주문자명' }}
                                    </span>
                                </button>
                                <input type="hidden" id="sw_1" name="sw_1" value="{{ request() -> sw_1 ?? 'orderer_name' }}">
                                <input type="hidden" id="sw_1_view" name="sw_1_view" value="">
                                <button type="button" class="btn btn-light dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="mdi mdi-chevron-down"></i>
                                </button>
                                <div class="dropdown-menu" style="">
                                    <a class="dropdown-item" href="javascript:select_btn('sw_1','주문번호','od_id');">주문번호</a>
                                    <a class="dropdown-item" href="javascript:select_btn('sw_1','주문자명','orderer_name');">주문자명</a>
                                    <a class="dropdown-item" href="javascript:select_btn('sw_1','주문자휴대폰','orderer_phone');">주문자휴대폰</a>
                                    <a class="dropdown-item" href="javascript:select_btn('sw_1','받는분','receiver_name');">받는분</a>
                                    <a class="dropdown-item" href="javascript:select_btn('sw_1','받는분휴대폰','receiver_phone');">받는분휴대폰</a>
                                </div>
                            </div>
                            <input class="form-control col-md-1  me-4" name="word1" type="text" id="selectedName" value="{{ request() -> word1 }}">
                            <div class="btn-group col-md-2 me-4">
                                <button type="button" class="btn btn-light col-md-3 waves-effect">
                                    <span id="sw_2_title">
                                        입금자명
                                    </span>
                                </button>
                                <input type="hidden" id="sw_2" name="sw_2" value="">
                                <input type="hidden" id="sw_2_view" name="sw_2_view" value="">
                                <button type="button" class="btn btn-light dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="mdi mdi-chevron-down"></i>
                                </button>
                                <div class="dropdown-menu" style="">
                                    <a class="dropdown-item" href="javascript:select_btn('sw_2','입금자명','deposit_name');">입금자명</a>
                                </div>
                            </div>
                            <input class="form-control col-md-2  me-4" name="word2" type="text" id="selectedName" value="{{ request() -> word2 }}">
                            <button style="border-radius:3px;" class="btn btn-secondary waves-effect waves-light col-md-2">검색하기</button>
                        </div>
                        <div class="input-group mb-3">
                            <div class="btn-group col-md-2 me-4">
                                <button type="button" class="btn btn-light col-md-3 waves-effect">
                                    <span id="date_type_title">
                                        {{ request() -> date_type_view ?? '주문접수일' }}
                                    </span>
                                </button>
                                <input type="hidden" id="date_type" name="date_type" value="{{ request() -> date_type ?? 'payment_date' }}">
                                <input type="hidden" id="date_type_view" name="date_type_view" value="{{ request() -> date_type_view ?? '주문접수일' }}">
                                <button type="button" class="btn btn-light dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="mdi mdi-chevron-down"></i>
                                </button>
                                <div class="dropdown-menu" style="">
                                    <a class="dropdown-item" href="javascript:select_btn('date_type','주문접수일','payment_date');">주문접수일</a>
                                    <a class="dropdown-item" href="javascript:select_btn('date_type','배송요청일','delivery_date');">배송요청일</a>
                                </div>
                            </div>
                            <div id="datepicker1">
                                <input type="text" class="form-control col-md-2 datepicker" id="start_date" name="start_date" value="{{ request() -> start_date }}" autocomplete="off" aria-label="start_date">
                            </div>
                            <strong class="mx-2 mt-2">~</strong>
                            <div id="datepicker2" class="mh-10">
                                <input type="text" class="form-control col-md-2 datepicker" id="end_date" name='end_date' value="{{ request() -> end_date }}" autocomplete="off" aria-label="end_date">
                            </div>
                            <button type="button" class="btn btn-light ms-4" onclick="dateSel('어제');">어제</button>
                            <button type="button" class="btn btn-light" onclick="dateSel('오늘');">오늘</button>
                            <button type="button" class="btn btn-light" onclick="dateSel('내일');">내일</button>
                            <button type="button" class="btn btn-light" onclick="dateSel('이번주');">이번주</button>
                            <button type="button" class="btn btn-light" onclick="dateSel('이번달');">이번달</button>
                            <button type="button" class="btn btn-light" onclick="dateSel('지난주');">지난주</button>
                            <button type="button" class="btn btn-light" onclick="dateSel('지난달');">지난달</button>
                            <button type="button" class="btn btn-light" onclick="dateSel('3개월');">3개월</button>
                            <button type="button" class="btn btn-light" onclick="dateSel('6개월');">6개월</button>
                            <button type="button" class="btn btn-light" onclick="dateSel('금년');">금년</button>
                            <button type="button" class="btn btn-light" onclick="dateSel('전년');">전년</button>
                        </div>
                        <div class="input-group">
                            <div class="btn-group col-md-2 me-4">
                                <button type="button" class="btn btn-light col-md-3 waves-effect">
                                    <span id="brand_title">
                                        {{ request() -> brand_view ?? '브랜드별' }}
                                    </span>
                                </button>
                                <input type="hidden" id="brand" name="brand" value="{{ request() -> brand }}">
                                <input type="hidden" id="brand_view" name="brand_view" value="{{ request() -> brand_view }}">
                                <button type="button" class="btn btn-light dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="mdi mdi-chevron-down"></i>
                                </button>
                                <div class="dropdown-menu" style="">
                                    <a class="dropdown-item" href="javascript:select_btn('brand', '꽃파는총각', 'BTCP');">꽃파는총각</a>
                                    <a class="dropdown-item" href="javascript:select_btn('brand', '칙칙폭폭플라워', 'BTCC');">칙칙폭폭플라워</a>
                                    <a class="dropdown-item" href="javascript:select_btn('brand', '사팔플라워', 'BTSP');">사팔플라워</a>
                                    <a class="dropdown-item" href="javascript:select_btn('brand', '오만플라워', 'BTOM');">오만플라워</a>
                                    <a class="dropdown-item" href="javascript:select_btn('brand', '바로플라워', 'BTBR');">바로플라워</a>
                                    <a class="dropdown-item" href="javascript:select_btn('brand', '꽃파는사람들', 'BTCS');">꽃파는사람들</a>
                                    <a class="dropdown-item" href="javascript:select_btn('brand', '플라체인', 'BTFC');">플라체인</a>
{{--                                                <a class="dropdown-item" href="javascript:select_btn('brand','BTNS');">내시플라워</a>--}}
                                </div>
                            </div>
                            <div class="btn-group col-md-2 me-4">
                                <button type="button" class="btn btn-light col-md-3 waves-effect">
                                    <span id="receipt_status_title">
                                        {{ request() -> receipt_status_view ?? '결제상태' }}
                                    </span>
                                </button>
                                <input type="hidden" id="receipt_status" name="receipt_status" value="{{ request() -> receipt_status }}">
                                <input type="hidden" id="receipt_status_view" name="receipt_status_view" value="{{ request() -> receipt_status_view }}">
                                <button type="button" class="btn btn-light dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="mdi mdi-chevron-down"></i>
                                </button>
                                <div class="dropdown-menu" style="">
                                    <a class="dropdown-item" href="javascript:select_btn('receipt_status','결제대기','PSUD');">결제대기</a>
                                    <a class="dropdown-item" href="javascript:select_btn('receipt_status','결제완료','PSDN');">결제완료</a>
                                    <a class="dropdown-item" href="javascript:select_btn('receipt_status','취소요청','PSCR');">취소요청</a>
                                    <a class="dropdown-item" href="javascript:select_btn('receipt_status','취소완료','PSCC');">취소완료</a>
                                </div>
                            </div>
                            <div class="btn-group col-md-2 me-4">
                                <button type="button" class="btn btn-light col-md-3 waves-effect">
                                    <span id="settle_title">
                                        {{ request() -> settle_view ?? '법인후불' }}
                                    </span>
                                </button>
                                <input type="hidden" id="settle" name="settle" value="{{ request() -> settle ?? 'PTDP' }}">
                                <input type="hidden" id="settle_view" name="settle_view" value="{{ request() -> settle_view ?? '법인후불' }}">
                                <button type="button" class="btn btn-light dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="mdi mdi-chevron-down"></i>
                                </button>
                                <div class="dropdown-menu" style="" >
                                    <a class="dropdown-item" href="javascript:select_btn('settle','전체','all');">전체</a>
                                    <a class="dropdown-item" href="javascript:select_btn('settle','카드','PTCD');">카드</a>
                                    <a class="dropdown-item" href="javascript:select_btn('settle','무통장','PTDP');">무통장</a>
                                    <a class="dropdown-item" href="javascript:select_btn('settle','가상계좌','PTVA');">가상계좌</a>
                                    <a class="dropdown-item" href="javascript:select_btn('settle','간편결제','PTTD');">간편결제</a>
                                    <a class="dropdown-item" href="javascript:select_btn('settle','법인후불','PTDP');">법인후불</a>
                                    <a class="dropdown-item" href="javascript:select_btn('settle','수기','PTMN');">수기</a>
                                    <a class="dropdown-item" href="javascript:select_btn('settle','외부결제','PTOP');">외부결제</a>
                                </div>
                            </div>
                            <div class="btn-group col-md-2 me-4">
                                <button type="button" class="btn btn-light col-md-3 waves-effect">
                                    <span id="delivery_status_title">
                                        {{ request() -> delivery_status_view ?? '배송완료' }}
                                    </span>
                                </button>
                                <input type="hidden" id="delivery_status" name="delivery_status" value="{{ request() -> delivery_status ?? 'DLDN' }}">
                                <input type="hidden" id="delivery_status_view" name="delivery_status_view" value="{{ request() -> delivery_status_view ?? '배송완료' }}">
                                <button type="button" class="btn btn-light dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="mdi mdi-chevron-down"></i>
                                </button>
                                <div class="dropdown-menu" style="">
                                    <a class="dropdown-item" href="javascript:select_btn('delivery_status','미배송','DLUD');">미배송</a>
                                    <a class="dropdown-item" href="javascript:select_btn('delivery_status','배송중','DLSP');">배송중</a>
                                    <a class="dropdown-item" href="javascript:select_btn('delivery_status','배송완료','DLDN');">배송완료</a>
                                    <a class="dropdown-item" href="javascript:select_btn('delivery_status','취소주문','DLCC');">취소주문</a>
                                </div>
                            </div>
                            <div class="btn-group col-md-2 me-4">
                                <button type="button" class="btn btn-light col-md-3 waves-effect">
                                    <span id="receipt_title">
                                        {{ request() -> receipt_view ?? '증빙' }}
                                    </span>
                                </button>
                                <input type="hidden" id="receipt" name="receipt" value="{{ request() -> receipt }}">
                                <input type="hidden" id="receipt_view" name="receipt_view" value="{{ request() -> receipt_view }}">
                                <button type="button" class="btn btn-light dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="mdi mdi-chevron-down"></i>
                                </button>
                                <div class="dropdown-menu" style="">
                                    <a class="dropdown-item" href="javascript:select_btn('receipt','미신청','');">미신청</a>
                                    <a class="dropdown-item" href="javascript:select_btn('receipt','미발행','');">미발행</a>
                                    <a class="dropdown-item" href="javascript:select_btn('receipt','발행','');">발행</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div><!-- card-body e -->
</div><!-- card e -->
</div><!-- col e -->
</div><!-- row e -->

<!-- 테이블 -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <table class="table table-striped table-bordered table-hover transaction" style="min-width: 1400px;">
                    <thead>
                    <tr>
                        <th class="th1"><input type="checkbox" class="m-0" aria-label="all" id="chk_all"></th>
                        <th class="th2">쇼핑몰주문번호</th>
                        <th class="th3">브랜드<br>도메인</th>
                        <th class="th4">주문일<br>배송일</th>
                        <th class="th5">주문자<br>연락처</th>
                        <th class="th6">받는분<br>연락처</th>
                        <th class="th7">보내는분<br>(리본문구)</th>
                        <th class="th8">배송지</th>
                        <th class="th9">입금자명</th>
                        <th class="th10">상품명<br>결제금액</th>
                        <th class="th11">비고</th>
                        <th class="th12">수정</th>
                    </tr>
                    </thead>
                    <tbody>
                        @if(count($orders)>0)
                            @foreach($orders as $order)
                            <tr onclick="chk_easy(event);">
                                <td>
                                    <input type="checkbox" class="m-0" name="od_id[]" aria-label="od_id">
                                    <input type="hidden" name="order_idx[]" value="{{ $order -> order_idx }}">
                                </td>
                                <!-- 번호 -->
                                <td>
                                    <p class="m-0 @if($order -> order_quantity > 1) dup_order @endif">{{ $order -> order_number }}</p>
                                </td>
                                <!-- 브랜드 / 도메인 -->
                                <td>
                                    <p class="brand_type {{$order->brand_type_code}} mb-1">{{ $ComInfo::comInfo($order->brand_type_code) }}</p>
                                    @php if(empty($order->admin_url)) { $order->admin_url = "http://" . $order->mall_code . '.flatalk.co.kr'; } @endphp
                                    <a onclick="open_admin_url('{{$order->admin_url}}')"><p class="brand_type {{$order->mall_code}}">{{$CommonCode::CodeName($order->mall_code) ?? $order->mall_code }}</p></a>
                                </td>
                                <!-- 주문일 / 배송일 -->
                                <td>
                                    <span class="simptip-position-bottom simptip-fade" tooltip="{{$order->admin_memo}}" flow="down">
                                        <a onclick="order_detail('{{$order->order_idx}}');">
                                            @if(!empty($order->payment_date))
                                                {{ Carbon\Carbon::parse($order->payment_date) -> format('Y-m-d')}}
                                            @else
                                                {{ Carbon\Carbon::parse($order->order_time) -> format('Y-m-d')}}
                                            @endif
                                            <br>
                                            <p class="deli_date">{{$order->delivery_date}}
                                                @if($order->admin_memo!='')
                                                    <i class="mdi mdi-note-text-outline memo_check"></i>
                                                @endif
                                            </p>
                                        </a>
                                    </span>
                                </td>
                                <!-- 주문자 / 연락처 -->
                                <td>
                                    <input type="text" class="input_text" name="orderer_name[]" value="{{$order->orderer_name}}" aria-label="orderer_name">
                                    {{ $order->orderer_phone }}
                                </td>
                                <!-- 받는분 / 연락처 -->
                                <td>
                                    <input type="text" class="input_text" name="receiver_name[]" value="{{$order->receiver_name}}" aria-label="receiver_name">
                                    {{ $order->receiver_phone }}
                                </td>
                                <!-- 보내는분 / 리본문구 -->
                                <td>
                                    <input type="text" class="input_text input_ribbon" name="delivery_ribbon_left[]" value="{{ $order -> delivery_ribbon_left }}" aria-label="ribbon_left">
                                </td>
                                <!-- 배송지 -->
                                <td>
                                    <input type="text" class="input_text w-100" name="delivery_address[]" value="{{ $order -> delivery_address }}" aria-label="address">
                                </td>
                                <!-- 입금자명 -->
                                <td>
                                    <input type="text" class="input_text" name="deposit_name[]" value="{{ $order -> deposit_name }}" aria-label="deposit_name">
                                </td>
                                <!-- 주문상품 / 결제금액 -->
                                <td>
                                    <input type="text" class="input_text" name="product_name[]" value="{{ $order -> pr_name }}" aria-label="product">
                                    <p class="amount">{{ number_format($order -> payment_amount) }}원</p>
                                    <input type="hidden" class="payment_amount" value="{{ $order -> payment_amount }}">
                                </td>
                                <!-- 비고 -->
                                <td>
                                    <input type="text" class="input_text">
                                </td>
                                <!-- 수정 -->
                                <td>
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="update_data(this,'{{ $order -> order_idx }}', '{{ Auth::user() -> name }}')">수정</button>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="12" class="search_none">검색한 내용이 없습니다.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div><!-- card-body e -->
        </div><!-- card e -->
    </div><!-- col e -->
</div><!-- row e -->

<div class="send_document_area">
    <div>
        <div class="order_cnt_cont">
            <span class="order_title all_order">전체주문</span>
            <span class="order_data">{{ number_format(count($orders)) }}건 / {{ number_format($totalAmount) }}원</span>
        </div>
        <div class="order_cnt_cont">
            <span class="order_title sel_order">선택주문</span>
            <span class="order_data sel_order_data">0건 / 0원</span>
        </div>
        <div class="order_cnt_cont right_btn">
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="document_hide_btn()">_</button>
        </div>
    </div>
    <div>
        <div class="transaction_send_area">
            <span class="order_title tran_title">발행업체</span>
            <select name="issuance_id" id="issuance_id" aria-label="issuance_id">
                @if($user_list -> isEmpty())
                    <option value="">업체선택</option>
                @else
                    @foreach($user_list as $user)
                        <option value="{{ $user -> id }}">{{ $user -> name }} @if(!empty($user -> name_memo)) &nbsp; <span>[{{ $user -> name_memo }}]</span>@endif</option>
                    @endforeach
                @endempty
            </select>
        </div>
        <div class="transaction_send_area ms-3">
            <span class="order_title tran_title">거래 월</span>
            <select class="form-select tran_year" id="tran_year" name="tran_year" aria-label="tran_year">
                <option value="{{ date('Y') - 1 }}">{{ date('Y') - 1 }}년</option>
                <option value="{{ date('Y') }}" selected>{{ date('Y') }}년</option>
                <option value="{{ date('Y') + 1 }}">{{ date('Y') + 1 }}년</option>
            </select>
        </div>

        <div class="transaction_send_area ms-3">
            <select class="form-select tran_month" id="tran_month" name="tran_month" aria-label="tran_month">
                @for($i=1; $i<=12; $i++)
                    <option value="{{ $i }}" @if($i == date('m')) selected @endif>{{$i."월"}}</option>
                @endfor
            </select>
        </div>
        <div class="transaction_send_area">
            <button type="button" class="btn btn-secondary" onclick="transaction_detail('1');">거래내역서 발행</button>
            <button type="button" class="btn btn-secondary ms-3" onclick="transaction_detail('2');">거래내역서2 발행</button>
            <button type="button" class="btn btn-secondary ms-3" onclick="transaction_detail('3');">거래명세서</button>
        </div>
        <div class="transaction_send_area right_btn">
            <button type="button" class="btn btn-outline-secondary scroll_btn" onclick="scroll_up()"><i class="uil-angle-up"></i></button>
        </div>
    </div>
</div>



<div class="send_document_btn d-none" onclick="document_area()">
    <button class="fixed_btn"><i class="uil-file-plus-alt fixed_btn_icon"></i><span class="fixed_btn_text">계산서 발행</span></button>
</div>


@endsection
@section('script')
<script> $('.datepicker').datepicker({ autoclose: true, todayHighlight: true }); </script>
<script src="{{ URL::asset('/assets/js/document/transaction.js') }}"></script>
<script>
    function dateSel(type){
        var start_date = '';
        var end_date = '';
        if(type=='오늘'){
            start_date ='{{$commonDate['today']}}';
            end_date ='{{$commonDate['today']}}';
        }else if(type=='어제'){
            start_date ='{{$commonDate['yesterday']}}';
            end_date ='{{$commonDate['yesterday']}}';
        }else if(type=='내일'){
            start_date ='{{$commonDate['tomorrow']}}';
            end_date ='{{$commonDate['tomorrow']}}';
        }else if(type=='이번주'){
            start_date ='{{$commonDate['week']}}';
            end_date ='{{$commonDate['today']}}';
        }else if(type=='이번달'){
            start_date ='{{$commonDate['month']}}';
            end_date ='{{$commonDate['month_e']}}';
        }else if(type=='지난주'){
            start_date ='{{$commonDate['preg_week_s']}}';
            end_date ='{{$commonDate['preg_week_e']}}';
        }else if(type=='지난달'){
            start_date ='{{$commonDate['preg_month_s']}}';
            end_date ='{{$commonDate['preg_month_e']}}';
        }else if(type=='3개월'){
            start_date ='{{$commonDate['month3']}}';
            end_date ='{{$commonDate['month_e']}}';
        }else if(type=='6개월'){
            start_date ='{{$commonDate['month6']}}';
            end_date ='{{$commonDate['month_e']}}';
        }else if(type=='금년'){
            start_date ='{{$commonDate['year']}}';
            end_date ='{{$commonDate['year_e']}}';
        }else if(type=='전년'){
            start_date ='{{$commonDate['preg_year_s']}}';
            end_date ='{{$commonDate['preg_year_e']}}';
        }
        $('#start_date').val(start_date);
        $('#end_date').val(end_date);
    }

</script>
@endsection
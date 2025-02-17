@extends('layouts.master')
@section('title')
    전체주문관리 Test2
@endsection
@section('content')
@php
    use Carbon\Carbon;
@endphp
<link href="{{ asset('/assets/css/order/order-index.css') }}" rel="stylesheet">
    @include('order.modal.order-index-modal')
    @if(session('alert'))
        <script>
            showToast('수정 완료');
        </script>
    @endif
    <div class="row">
        <div class="col-md-12">
            <div class="card card-body">
                <div class="row order_top_data_container">
                    <div class="text-center order_top_data2">
                        <i class="fas fa-stopwatch"></i>
                        <strong>미확인</strong><br>
                        <a href="?is_new=1"><strong class="font-big link-danger">{{$newOrders}}건</strong></a>
                    </div>
                    <div class="text-center order_top_data3">
                        <i class="fas fa-external-link-alt"></i>
                        <strong>미발주</strong><br>
                        @if(Auth::user()->dep=="꽃파는사람들")
                            <a href="?delivery_state_code=DLUD"><strong class="font-big link-danger">{{ $isNotBalju }}건</strong></a>
                        @else
                            <a href="?PSUD_check=1&delivery_state_code=DLUD"><strong class="font-big link-danger">{{ $isNotBalju }}건</strong></a>
                        @endif
                    </div>
                    <div class="text-center order_top_data4">
                        <i class="fas fa-store-slash"></i>
                        <strong>취소요청</strong><br>
                        <a href="?payment_state_code=PSCR"><strong class="font-big link-danger">{{$cancelRequest}}건</strong></a>
                    </div>
                    <div class="text-center order_top_data5">
                        <i class="fas fa-shopping-basket"></i>
                        <strong>오늘주문</strong><br>
                        <a href="?date_type=order_time&start_date={{date("Y-m-d")}}&end_date={{date("Y-m-d")}}"><strong class="font-big link-danger">{{$todayOrders}}건</strong></a>
                    </div>
                    <div class="text-center order_top_data6">
                        <i class="fas fa-truck"></i>
                        <strong>오늘배송</strong><br>
                        <a href="?date_type=delivery_date&start_date={{date("Y-m-d")}}&end_date={{date("Y-m-d")}}"><strong class="font-big link-danger">{{$todayDelivery}}건</strong></a>
                    </div>
                </div>
            </div><!-- end card -->
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body spacer_zero">
                    <div class="accordion accordion-flush" id="accordionFlushExample">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="flush-headingTwo">
                                <button class="accordion-button {{request()->sw_1? "":"collapsed"}}" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseTwo" aria-expanded="false" aria-controls="flush-collapseTwo">
                                    <strong>검색 필터 열기</strong>
                                </button>
                            </h2>
                            <form method="get" action="?" id="order_searchForm">
                            <div id="flush-collapseTwo" class="accordion-collapse collapse {{request()->sw_1? "show":""}}" aria-labelledby="flush-headingTwo" data-bs-parent="#accordionFlushExample">
                                <div class="accordion-body">
                                    <div class="input-group mb-3">
                                        <div class="btn-group col-md-2 me-4">
                                            <button type="button" class="btn btn-light col-md-3 waves-effect">
                                                <span id="sw_1_title">
                                                    {{request()->sw_1_view ?? "1차 조회 항목"}}
                                                </span>
                                            </button>
                                            <input type="hidden" id="sw_1" name="sw_1" value="{{request()->sw_1?? "all"}}">
                                            <input type="hidden" id="sw_1_view" name="sw_1_view" value="{{request()->sw_1_view}}">
                                            <button type="button" class="btn btn-light dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="mdi mdi-chevron-down"></i>
                                            </button>
                                            <div class="dropdown-menu" style="">
                                                <a class="dropdown-item" href="javascript:select_btn('sw_1','1차 조회 항목','all');">1차 조회 항목</a>
                                                <a class="dropdown-item" href="javascript:select_btn('sw_1','주문번호','od_id');">주문번호</a>
                                                <a class="dropdown-item" href="javascript:select_btn('sw_1','쇼핑몰주문번호','order_number');">쇼핑몰주문번호</a>
                                                <a class="dropdown-item" href="javascript:select_btn('sw_1','주문인덱스','order_idx');">주문인덱스</a>
                                                <a class="dropdown-item" href="javascript:select_btn('sw_1','주문자','orderer_name');">주문자</a>
                                                <a class="dropdown-item" href="javascript:select_btn('sw_1','주문자휴대폰','orderer_phone');">주문자휴대폰</a>
                                                <a class="dropdown-item" href="javascript:select_btn('sw_1','받는분','receiver_name');">받는분</a>
                                                <a class="dropdown-item" href="javascript:select_btn('sw_1','받는분휴대폰','receiver_phone');">받는분휴대폰</a>
                                                <a class="dropdown-item" href="javascript:select_btn('sw_1','상품명','goods_name');">상품명</a>
                                                <a class="dropdown-item" href="javascript:select_btn('sw_1','배송지주소','delivery_address');">배송지주소</a>
                                                <a class="dropdown-item" href="javascript:select_btn('sw_1','관리자메모','admin_memo');">관리자메모</a>
                                                <a class="dropdown-item" href="javascript:select_btn('sw_1','보내는분','delivery_ribbon_left');">보내는분</a>
                                                <a class="dropdown-item" href="javascript:select_btn('sw_1','배송요청사항','delivery_message');">배송요청사항</a>
                                                <a class="dropdown-item" href="javascript:select_btn('sw_1','담당자','handler');">담당자</a>
                                                <a class="dropdown-item" href="javascript:select_btn('sw_1','사업자명','rep_name');">사업자명</a>
                                                <a class="dropdown-item" href="javascript:select_btn('sw_1','Pass명','name');">Pass명</a>
                                                <a class="dropdown-item" href="javascript:select_btn('sw_1','입금자명','deposit_name');">입금자명</a>
                                            </div>
                                        </div>
                                        <input class="form-control col-md-1  me-4" name="word1" type="text" id="selectedName" value="{{request()->word1}}">
                                        <div class="btn-group col-md-2 me-4">
                                            <button type="button" class="btn btn-light col-md-3 waves-effect">
                                                <span id="sw_2_title">
                                                    {{request()->sw_2_view ?? "2차 조회 항목"}}
                                                </span>
                                            </button>
                                            <input type="hidden" id="sw_2" name="sw_2" value="{{request()->sw_2?? "all"}}">
                                            <input type="hidden" id="sw_2_view" name="sw_2_view" value="{{request()->sw_2_view}}">
                                            <button type="button" class="btn btn-light dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="mdi mdi-chevron-down"></i>
                                            </button>
                                            <div class="dropdown-menu" style="">
                                                <a class="dropdown-item" href="javascript:select_btn('sw_2','2차 조회 항목','all');">2차 조회 항목</a>
                                                <a class="dropdown-item" href="javascript:select_btn('sw_2','주문번호','od_id');">주문번호</a>
                                                <a class="dropdown-item" href="javascript:select_btn('sw_2','쇼핑몰주문번호','order_number');">쇼핑몰주문번호</a>
                                                <a class="dropdown-item" href="javascript:select_btn('sw_2','주문자','orderer_name');">주문자</a>
                                                <a class="dropdown-item" href="javascript:select_btn('sw_2','주문자휴대폰','orderer_phone');">주문자휴대폰</a>
                                                <a class="dropdown-item" href="javascript:select_btn('sw_2','받는분','receiver_name');">받는분</a>
                                                <a class="dropdown-item" href="javascript:select_btn('sw_2','받는분휴대폰','receiver_phone');">받는분휴대폰</a>
                                                <a class="dropdown-item" href="javascript:select_btn('sw_2','상품명','goods_name');">상품명</a>
                                                <a class="dropdown-item" href="javascript:select_btn('sw_2','배송지주소','delivery_address');">배송지주소</a>
                                                <a class="dropdown-item" href="javascript:select_btn('sw_2','관리자메모','admin_memo');">관리자메모</a>
                                                <a class="dropdown-item" href="javascript:select_btn('sw_2','보내는분','delivery_ribbon_left');">보내는분</a>
                                                <a class="dropdown-item" href="javascript:select_btn('sw_2','배송요청사항','delivery_message');">배송요청사항</a>
                                                <a class="dropdown-item" href="javascript:select_btn('sw_2','담당자','handler');">담당자</a>
                                                <a class="dropdown-item" href="javascript:select_btn('sw_2','사업자명','rep_name');">사업자명</a>
                                                <a class="dropdown-item" href="javascript:select_btn('sw_2','입금자명','deposit_name');">입금자명</a>
                                            </div>
                                        </div>
                                        <input class="form-control col-md-2  me-4" name="word2" type="text" id="selectedName" value="{{request()->word2}}">
                                        <button style="border-radius:3px;" class="btn btn-secondary waves-effect waves-light col-md-2">검색하기</button>
                                    </div>
                                    <div class="input-group mb-3">
                                        <div class="btn-group col-md-2 me-4">
                                            <button type="button" class="btn btn-light col-md-3 waves-effect">
                                                <span id="date_type_title">
                                                    {{request()->date_type_view ?? "주문수집일"}}
                                                </span>
                                            </button>
                                            <input type="hidden" id="date_type" name="date_type" value="{{request()->date_type ?? "create_ts"}}">
                                            <input type="hidden" id="date_type_view" name="date_type_view" value="{{request()->date_type_view ?? "주문수집일"}}">
                                            <button type="button" class="btn btn-light dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="mdi mdi-chevron-down"></i>
                                            </button>
                                            <div class="dropdown-menu" style="">
                                                <a class="dropdown-item" href="javascript:select_btn('date_type','주문수집일','create_ts');">주문수집일</a>
                                                <a class="dropdown-item" href="javascript:select_btn('date_type','배송요청일','delivery_date');">배송요청일</a>
                                                <a class="dropdown-item" href="javascript:select_btn('date_type','주문시간','order_time');">주문시간</a>
                                            </div>
                                        </div>
                                        <div id="datepicker1">
                                            <input type="date" class="form-control col-md-2" id="start_date" name="start_date" value="{{request()->start_date ?? now()->subYear()->format('Y-m-d') }}" data-date-format="yyyy-mm-dd" data-date-container='#datepicker1' data-provide="datepicker">
                                        </div>
                                        <strong class="mx-2 mt-2">~</strong>
                                        <div id="datepicker2" class="mh-10">
                                            <input type="date" class="form-control col-md-2" id="end_date" name='end_date' value="{{request()->end_date ?? $commonDate['today']}}" data-date-format="yyyy-mm-dd" data-date-container='#datepicker1' data-provide="datepicker">
                                        </div>
                                        <button type="button" class="btn btn-light ms-4" onclick="dateSel('어제');">어제</button>
                                        <button type="button" class="btn btn-light" onclick="dateSel('오늘');">오늘</button>
                                        <button type="button" class="btn btn-light" onclick="dateSel('내일');">내일</button>
                                        <button type="button" class="btn btn-light" onclick="dateSel('이번주');">이번주</button>
                                        <button type="button" class="btn btn-light" onclick="dateSel('이번달');">이번달</button>
                                        <button type="button" class="btn btn-light" onclick="dateSel('지난주');">지난주</button>
                                        <button type="button" class="btn btn-light" onclick="dateSel('지지난달');">지지난달</button>
                                        <button type="button" class="btn btn-light" onclick="dateSel('지난달');">지난달</button>
                                        <button type="button" class="btn btn-light" onclick="dateSel('3개월');">3개월</button>
                                        <button type="button" class="btn btn-light" onclick="dateSel('6개월');">6개월</button>
                                        <button type="button" class="btn btn-light" onclick="dateSel('금년');">금년</button>
                                        <button type="button" class="btn btn-light" onclick="dateSel('전년');">전년</button>
                                    </div>
                                    <div class="input-group">
                                        <div class="btn-group col-md-2 me-4">
                                            <button type="button" class="btn btn-light col-md-3 waves-effect">
                                                <span id="mall_code_title">
                                                    {{request()->mall_code_view?? "오픈마켓선택"}}
                                                </span>
                                            </button>
                                            <input type="hidden" id="mall_code" name="mall_code" value="{{request()->mall_code}}">
                                            <input type="hidden" id="mall_code_view" name="mall_code_view" value="{{request()->mall_code_view}}">
                                            <button type="button" class="btn btn-light dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="mdi mdi-chevron-down"></i>
                                            </button>
                                            <div class="dropdown-menu" style="">
                                                <a class="dropdown-item" href="javascript:select_btn('mall_code','채널선택','');">채널선택</a>
                                                <a class="dropdown-item" href="javascript:select_btn('mall_code','옥션','MLAC');">옥션</a>
                                                <a class="dropdown-item" href="javascript:select_btn('mall_code','G마켓','MLGM');">G마켓</a>
                                                <a class="dropdown-item" href="javascript:select_btn('mall_code','11번가','ML11');">11번가</a>
                                                <a class="dropdown-item" href="javascript:select_btn('mall_code','스마트스토어','MLNV');">스마트스토어</a>
                                                <a class="dropdown-item" href="javascript:select_btn('mall_code','톡스토어','MLKK');">톡스토어</a>
                                                <a class="dropdown-item" href="javascript:select_btn('mall_code','간편주문','MLMW');">간편주문</a>
                                                <a class="dropdown-item" href="javascript:select_btn('mall_code','쿠팡','MLCP');">쿠팡</a>
                                                <a class="dropdown-item" href="javascript:select_btn('mall_code','PASS','PASS');">PASS</a>
                                            </div>
                                        </div>
                                        <div class="btn-group col-md-2 me-4">
                                            <button type="button" class="btn btn-light col-md-3 waves-effect">
                                                <span id="payment_state_code_title">
                                                    {{request()->payment_state_code_view ?? "결제상태선택"}}
                                                </span>
                                            </button>
                                            <input type="hidden" id="payment_state_code" name="payment_state_code" value="{{request()->payment_state_code}}">
                                            <input type="hidden" id="payment_state_code_view" name="payment_state_code_view" value="{{request()->payment_state_code_view}}">
                                            <button type="button" class="btn btn-light dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="mdi mdi-chevron-down"></i>
                                            </button>
                                            <div class="dropdown-menu" style="">
                                                <a class="dropdown-item" href="javascript:select_btn('payment_state_code','결제상태선택','');">결제상태선택</a>
                                                <a class="dropdown-item" href="javascript:select_btn('payment_state_code','결제대기','PSUD');">결제대기</a>
                                                <a class="dropdown-item" href="javascript:select_btn('payment_state_code','결제완료','PSDN');">결제완료</a>
                                                <a class="dropdown-item" href="javascript:select_btn('payment_state_code','취소요청','PSCR');">취소요청</a>
                                                <a class="dropdown-item" href="javascript:select_btn('payment_state_code','취소완료','PSCC');">환불</a>
                                                <a class="dropdown-item" href="javascript:select_btn('payment_state_code','주문확인','PSOC');">주문확인</a>
                                            </div>
                                        </div>
                                        <div class="btn-group col-md-2 me-4">
                                            <button type="button" class="btn btn-light col-md-3 waves-effect">
                                                <span id="payment_type_code_title">
                                                    {{request()->payment_type_code_view ?? "결제수단선택"}}
                                                </span>
                                            </button>
                                            <input type="hidden" id="payment_type_code" name="payment_type_code" value="{{request()->payment_type_code}}">
                                            <input type="hidden" id="payment_type_code_view" name="payment_type_code_view" value="{{request()->payment_type_code_view}}">
                                            <button type="button" class="btn btn-light dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="mdi mdi-chevron-down"></i>
                                            </button>
                                            <div class="dropdown-menu" style="" >
                                                <a class="dropdown-item" href="javascript:select_btn('payment_type_code','결제수단선택','');">결제수단선택</a>
                                                <a class="dropdown-item" href="javascript:select_btn('payment_type_code','카드','PTCD');">카드</a>
                                                <a class="dropdown-item" href="javascript:select_btn('payment_type_code','무통장','PTDB');">무통장</a>
                                                <a class="dropdown-item" href="javascript:select_btn('payment_type_code','가상계좌','PTVA');">가상계좌</a>
                                                <a class="dropdown-item" href="javascript:select_btn('payment_type_code','간편결제','PTTD');">간편결제</a>
                                                <a class="dropdown-item" href="javascript:select_btn('payment_type_code','법인미수','PTDP');">법인미수</a>
                                                <a class="dropdown-item" href="javascript:select_btn('payment_type_code','수기','PTMN');">수기</a>
                                                <a class="dropdown-item" href="javascript:select_btn('payment_type_code','외부결제','PTOP');">외부결제</a>
                                            </div>
                                        </div>
                                        <div class="btn-group col-md-2 me-4">
                                            <button type="button" class="btn btn-light col-md-3 waves-effect">
                                                <span id="delivery_state_code_title">
                                                    {{request()->delivery_state_code_view ?? "배송상태선택" }}
                                                </span>
                                            </button>
                                            <input type="hidden" id="delivery_state_code" name="delivery_state_code" value="{{request()->delivery_state_code}}">
                                            <input type="hidden" id="delivery_state_code_view" name="delivery_state_code_view" value="{{request()->delivery_state_code_view}}">
                                            <button type="button" class="btn btn-light dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="mdi mdi-chevron-down"></i>
                                            </button>
                                            <div class="dropdown-menu" style="">
                                                <a class="dropdown-item" href="javascript:select_btn('delivery_state_code','배송상태선택','');">배송상태선택</a>
                                                <a class="dropdown-item" href="javascript:select_btn('delivery_state_code','미배송','DLUD');">미배송</a>
                                                <a class="dropdown-item" href="javascript:select_btn('delivery_state_code','배송중','DLSP');">배송중</a>
                                                <a class="dropdown-item" href="javascript:select_btn('delivery_state_code','배송완료','DLDN');">배송완료</a>
                                                <a class="dropdown-item" href="javascript:select_btn('delivery_state_code','취소주문','DLCC');">취소주문</a>
                                            </div>
                                        </div>
                                        <div class="btn-group col-md-2 me-4">
                                            <button type="button" class="btn btn-light col-md-3 waves-effect">
                                                <span id="receipt_title">
                                                    {{request()->receipt_view ?? "증빙선택"}}
                                                </span>
                                            </button>
                                            <input type="hidden" id="receipt" name="receipt" value="{{request()->receipt}}">
                                            <input type="hidden" id="receipt_view" name="receipt_view" value="{{request()->receipt_view}}">
                                            <button type="button" class="btn btn-light dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="mdi mdi-chevron-down"></i>
                                            </button>
                                            <div class="dropdown-menu" style="">
                                                <a class="dropdown-item" href="javascript:select_btn('receipt','증빙선택','');">증빙선택</a>
                                                <a class="dropdown-item" href="javascript:select_btn('receipt','미신청','');">미신청</a>
                                                <a class="dropdown-item" href="javascript:select_btn('receipt','미발행','');">미발행</a>
                                                <a class="dropdown-item" href="javascript:select_btn('receipt','발행','');">발행</a>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="form-check form-switch me-4">
                                                <input type="checkbox" class="form-check-input" name="cancel_check" value="1" @checked(request()->cancel_check) id="cancel_check">
                                                <label class="form-check-label" for="cancel_check">취소주문포함</label>
                                            </div>
{{--                                            <div class="form-check form-switch me-4">--}}
{{--                                                <input type="checkbox" class="form-check-input" name="PSUD_check" value="1" @checked(request()->PSUD_check) id="customSwitch2">--}}
{{--                                                <label class="form-check-label" for="customSwitch2">결제대기제외</label>--}}
{{--                                            </div>--}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </form>
                        </div>
                    </div>
                </div><!-- end card-body -->
            </div><!-- end card -->
        </div><!-- end col -->
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="order_index_btns">
                        <div class="front_btns">
                            @if($orders_count!==0)
                            <p class="count_text"><span>{{number_format($orders_count)}}</span> 건<span class="ms-2">{{number_format($sum_amount)}}</span> 원</p>
                            @endif
                            <select class="form-select select_perPage" id="perPage" aria-label="perPage">
                                <option value="20"  @if(session('perPage')) {{session('perPage') === "20" ? "selected" : ""}}@endif>20개씩</option>
                                <option value="50"  @if(session('perPage')) {{session('perPage') === "50" ? "selected" : ""}}@endif>50개씩</option>
                                <option value="100" @if(session('perPage')) {{session('perPage') === "100" ? "selected" : ""}}@endif>100개씩</option>
                                <option value="300" @if(session('perPage')) {{session('perPage') === "300" ? "selected" : ""}}@endif>300개씩</option>
                            </select>
                            <button type="button" class="btn btn-outline-warning btn-sm" onclick="operate_orders('highlite')">하이라이트</button>
                            <button type="button" class="btn btn-outline-warning btn-sm" onclick="operate_orders('highlite-off')">하이라이트 제거</button>
                            <button type="button" class="btn btn-outline-warning btn-sm" onclick="location.href='{{url("order/ecommerce_orders")."?is_highlight=1"}}'">하이라이트만 보기</button>
                            @if(Auth::user()->auth > 5 || Auth::user()->auth === 10)
                            <button type="button" class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#deposit_completed">일괄입금</button>
                            @endif
                            @if(Auth::user()->auth > 3)
                            <button type="button" class="btn btn-outline-danger btn-sm ms-2" onclick="operate_orders('remove')">주문제거</button>
                            @endif
                        </div>
                        <div class="end_btns">
                            <button type="button" class="btn btn-outline-info btn-sm" onclick="window.open('{{url('order/detail-list/')}}', '_blank');">주문상세 검색</button>
                            <button type="button" class="btn btn-outline-success btn-sm" onclick="excel_popup()">엑셀 일괄 다운로드</button>
                            <button type="button" class="btn btn-outline-success btn-sm" onclick="excel_download()">엑셀 다운로드</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <input type="hidden" name="handler" id="handler" value="{{Auth::user()->name}}">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    {{--datatable-buttons--}}
                    <table class="table table-striped table-bordered " style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                        <tr>
                            <th>
                                <label class="checkboxLabel">
                                    <input type="checkbox" name="checkAll">
                                </label>
                            </th>
                            <th>번호</th>
                            <th style="width: 7%">브랜드<br>채널</th>
                            <th style="width: 7%">수집일<br>배송일</th>
                            <th>주문자<br>연락처</th>
                            <th>받는분<br>연락처</th>
                            <th>주문상품<br>합계금액</th>
                            <th>배송지<br>보내는분</th>
                            <th style="width: 7%">결제수단</th>
                            <th style="width: 6%">결제상태<br>배송상태</th>
                            <th>담당자</th>
                            <th>전송</th>
                            <th>기타</th>
                        </tr>
                        </thead>
                        <tbody>

                        @if($orders->items())
                            @foreach($orders as $order)
                                @php
                                    $today = Carbon::now()->format('Y-m-d');
                                    $tomorrow = Carbon::now()->addDay()-> format('Y-m-d');
                                    $delivery_date = Carbon::parse($order->delivery_date)->format('Y-m-d');
                                @endphp

                                <tr class="ordersTable_tr {{$order->is_highlight? "is_highlight": ""}} {{$order->is_view? '' : "not_view"}}" >
                                    <!-- 번호 -->
                                    <td class="center" style="padding: 0;">
                                        <label class="checkboxLabel">
                                            <input type="checkbox" name="order_idx" value="{{$order->order_idx}}" data-paytype="{{$order->payment_type_code}}">
                                        </label>
                                    </td>
                                    <td class="center" style="vertical-align: center;">
                                        @if(Auth::user() -> auth > 9)
                                            <a href="javascript:order_log_popup('{{ $order -> od_id }}');" class="text-dark">{{$order->order_idx}}</a>
                                        @else
                                            {{$order->order_idx}}
                                        @endif
                                        @if($order -> order_quantity > 1)
                                            <p class="new_order" id="order_quantity">({{$order -> sub_index()}}/{{$order -> order_quantity}})</p>
                                        @endif
                                        @if($order->is_new)
                                            <p class="new_order" id="new_order{{$order->order_idx}}">미확인</p>
                                        @endif
                                    </td>
                                    <!-- 브랜드 -->
                                    <td class="center">
                                        <p class="brand_type {{$order->brand_type_code}}">{{ BrandAbbr($order->brand_type_code)}}</p>

                                        <a href="javascript:open_win('{{$order->channel_url()}}', 'vendor_domain', 1000, 900, 50, 50)" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="right" data-bs-content="{{$order->channel_mall()}}">
                                            <p class="brand_type {{$order->mall_code}}" style="margin-top: 3px">
                                                {{$order->channel_name()}}
                                            </p>
                                        </a>

                                    </td>
                                    <!-- 수집일/배송일 -->
                                    <td class="center">
                                        <div style="position: relative" class="date_container simptip-position-bottom simptip-fade cursor_p" tooltip="{{$order->admin_memo}}" flow="down">

                                            <span class="span_date" onclick="order_detail('{{$order->order_idx}}');">{{$order->create_ts}}
                                                @switch($order->inflow)
                                                    @case("mall")
                                                        <i class="bx bx-cart inflow_check icon_font_size"
                                                           data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="top" data-bs-content="몰"
                                                        ></i>
                                                    @break
                                                    @case("call")
                                                        <i class="bx bx-phone inflow_check icon_font_size"
                                                           data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="top" data-bs-content="전화"
                                                        ></i>
                                                    @break
                                                    @case("sms")
                                                        <i class="bx bx-envelope inflow_check icon_font_size"
                                                           data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="top" data-bs-content="문자"
                                                        ></i>
                                                    @break
                                                    @case("talk")
                                                        <i class="far fa-comment inflow_check"
                                                           data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="top" data-bs-content="카톡"
                                                        ></i>
                                                        @break
                                                    @case("channel")
                                                        <i class="far fa-comments inflow_check"
                                                           data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="click focus hover" data-bs-placement="top" data-bs-content="플친"
                                                        ></i>
                                                    @break
                                                @endswitch
                                            <br>
                                            <span class="deli_date span_date
                                                 @if ($today === $delivery_date) today_delivery
                                                @elseif ($tomorrow === $delivery_date) tomorrow_delivery
                                                 @endif
                                                ">{{$order->delivery_date}} {{$order->delivery_time}}
                                            </span>
                                            @if(!empty($order->admin_memo))
                                                <i class="mdi mdi-note-text-outline memo_check"></i>
                                            @endif
                                            </span>
                                        </div>
                                    </td>
                                    <!-- 주문자/연락처 -->
                                    <td class="center">
                                        <p class="gs_name"
                                           data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="top" data-bs-content="{{$order->orderer_name}}"
                                           onclick="clipBoardCopy(event)">
                                            {{$order->orderer_name}}</p>
                                        <span class="cursor_p" onclick="send_SMS('{{$order->order_idx}}', '{{$order->brand_type_code}}')">
                                        @if(empty($order->orderer_phone))
                                            {{$order->orderer_tel}}
                                        @else
                                            {{$order->orderer_phone}}
                                        @endif
                                        </span>
                                    </td>
                                    <!-- 받는분/연락처 -->
                                    <td class="center"><p class="gs_name"
                                                          data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="top" data-bs-content="{{$order->receiver_name}}"
                                                          onclick="clipBoardCopy(event)">
                                            {{$order->receiver_name}}</p>
                                        @if(empty($order->receiver_phone))
                                            {{$order->receiver_tel}}
                                        @else
                                            {{$order->receiver_phone}}
                                        @endif
                                    </td>
                                    <!-- 주문상품/결제금액 -->
                                    <td class="center"><a href='javascript:void(0);' onclick="market_open('{{ App\Utils\Common::get_item_url($order->mall_code, $order->brand_type_code) ?? ''}}{{$order->goods_url}}');"><p class="gs_name">{{$order->goods_name}}</p></a><p class="amount">{{number_format((int)$order->total_amount - (int)$order->discount_amount)}}원</p></td>
                                    <!-- 배송지/보내는분 -->
                                    <td class="center">
                                        <p class="addr cursor_p"
                                           data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="top" data-bs-content="{{$order->delivery_address}}"
                                           onclick="clipBoardCopy(event)"
                                        >
                                            {{$order->delivery_address}}
                                        </p>
                                        <p class="ribbon_left cursor_p" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="top" data-bs-content="{{$order->delivery_ribbon_left}}" onclick="clipBoardCopy(event)">{{$order->delivery_ribbon_left}}</p>
                                    </td>
                                    <!-- 결제수단 -->
                                    <?php ?>
                                    <td class="center">
                                        <p class="state_p {{$order->payment_type_code}} cursor_p" style="margin: 0 auto;">{{ CommonCodeName($order->payment_type_code) }}</p>
                                        <span class="deposit_name_text cursor_p {{$order->payment_state_code==="PSUD"? "text-danger" : ""}}" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="top" data-bs-content="{{$order->deposit_names}}" onclick="clipBoardCopy(event)">{{$order->deposit_names}}</span><br>
                                    </td>
                                    <!-- 결제상태/배송상태 -->
                                    <td class="center">
                                        <!-- 결제 상태 취소 요청 -->
                                        @if($order->payment_state_code === 'PSCR' || $order->payment_state_code === 'PSER' || $order->payment_state_code === 'PSRR')
                                            <div class="btn-group">
                                                <p class="state_p {{$order->payment_state_code}} dropdown-toggle dropdown-toggle-split cursor_p" data-bs-toggle="dropdown" aria-expanded="false">{{CommonCodeName($order->payment_state_code)}}</p>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="javascript:cancel_progress('{{$order->order_idx}}', '{{Str::ucfirst(Auth::user()->name)}}')">취소 처리 중</a></li>
                                                    <li><a class="dropdown-item" href="#" id="cancel_refuse_btn" data-bs-toggle="modal" data-bs-target="#cancel_refuse" data-number="{{ $order-> order_number}}">주문진행</a></li>
                                                    <li><a class="dropdown-item" href="#" id="cancel_complete_btn" data-bs-toggle="modal" data-bs-target="#cancel_complete" data-state="{{ $order -> payment_state_code }}" data-number="{{ $order-> order_number}}">취소완료</a></li>
                                                </ul>
                                            </div>
                                        @else
                                            <p class="state_p {{$order->payment_state_code}}">{{CommonCodeName($order->payment_state_code)}}</p>
                                        @endif

                                        <div class="btn-group">
                                            <p class="state_p mt-1 {{$order->delivery_state_code}} dropdown-toggle dropdown-toggle-split cursor_p" data-bs-toggle="dropdown" aria-expanded="false">{{CommonCodeName($order->delivery_state_code)}}</p>
                                            @if($order->payment_state_code === "PSCC" || $order->payment_state_code === "PSUD")
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" data-index="{{$order->order_idx}}" href="" onclick="change_deli_state(event,'DLCC', '{{Auth::user()->name}}')">배송취소 상태변경</a></li>
                                                    @if($order->delivery_state_code === "DLSP")
                                                        <li><a class="dropdown-item" data-index="{{$order->order_idx}}" href="" onclick="change_deli_state(event,'DLDN', '{{Auth::user()->name}}')">배송완료 상태변경</a></li>
                                                    @endif
                                                </ul>
                                            @elseif($order->delivery_state_code === "DLSP")
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" data-index="{{$order->order_idx}}" href="" onclick="change_deli_state(event,'DLDN', '{{Auth::user()->name}}')">배송완료 상태변경</a></li>
                                                </ul>
                                            @endif
                                        </div>
                                    </td>
                                    <!-- 담당자 -->
                                    <td class="center" id="send_name{{$order->order_idx}}">{{$order->handler}}</td>
                                    <!-- 전송 -->
                                    <td class="center" id="send_area{{$order->order_idx}}">
                                        @if($order -> brand_type_code === 'BTCS' || $order -> brand_type_code === 'BTFC')
                                            @if($order->is_balju === 1)
                                                <span>완료</span>
                                            @elseif(optional($order->is_credit()) && $order->delivery_state_code!=="DLDN" )
                                                <button class="btn btn-primary btn-soft-primary btn-sm" onclick="send_intranet('{{ $order->order_idx }}');">발주</button>
                                            @elseif($order->payment_state_code === 'PSDN' && $order->delivery_state_code!=="DLDN")
                                                <button class="btn btn-primary btn-soft-primary btn-sm" onclick="send_intranet('{{ $order->order_idx }}');">발주</button>
                                            @else
                                                <div></div>
                                            @endif
                                        @else
                                            @if($order->is_balju === 1)
                                                <span>완료</span>
                                            @elseif(!($order->payment_state_code === 'PSDN' || $order->payment_type_code === 'PTDP'))
                                                <div></div>
                                            @else
                                                <button id="send_btn{{$order->order_idx}}" class="btn btn-primary btn-soft-primary btn-sm" onclick="nr_send(event,'{{ $order->order_idx }}');">전송</button>
                                            @endif
                                        @endif
                                    </td>
                                    <!-- 배송사진 -->
                                    <td class="center" style="max-width: 110px;">
                                        @if(!empty($order->delivery_insuName))
                                        <i class="uil-user etc_icon icon_insu" data-bs-toggle="popover" data-bs-html="true" data-bs-trigger="hover"
                                           data-bs-placement="left" data-bs-content="{{$order->delivery_insuName}}"></i>
                                        @endif
                                        @if(!empty($order->delivery_photo))
                                            <i class="uil-truck etc_icon icon_photo" data-bs-toggle="popover" data-bs-html="true" data-bs-trigger="hover"
                                               data-bs-placement="left" data-bs-content="<img src='{{ $order->delivery_photo }}' alt='배송 사진' width='150px' height='200px'>" onclick="photo_popup('{{ $order->delivery_photo }}');"></i>
                                        @endif
                                        @if(!empty($order->delivery_photo2))
                                            <i class="uil-truck etc_icon icon_photo" data-bs-toggle="popover" data-bs-html="true" data-bs-trigger="hover"
                                               data-bs-placement="left" data-bs-content="<img src='{{ $order->delivery_photo2 }}' alt='배송 사진' width='150px' height='200px'>" onclick="photo_popup('{{ $order->delivery_photo2 }}');"></i>
                                        @endif
                                        @if(!empty($order->delivery_photo3))
                                            <i class="uil-truck etc_icon icon_photo" data-bs-toggle="popover" data-bs-html="true" data-bs-trigger="hover"
                                               data-bs-placement="left" data-bs-content="<img src='{{ $order->delivery_photo3 }}' alt='배송 사진' width='150px' height='200px'>" onclick="photo_popup('{{ $order->delivery_photo3 }}');"></i>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="10" class="text-center"><h4 class="my-4">데이터가 없습니다.</h4></td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                    {{ $orders->links('vendor.pagination.10-page-custom', ['orders_count' => $orders_count, ]) }}
                </div>
            </div>
        </div> <!-- end col -->
            <button class="fixed_btn" data-bs-toggle="modal" data-bs-target="#order_form_modal"><i class="uil-file-plus-alt fixed_btn_icon"></i><span class="fixed_btn_text">주문서 작성</span></button>
            <!-- Center Modal example -->
            <div class="modal fade" id="order_form_modal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-sm modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">주문서 작성</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="dropdown">
                                <div><button class="brand_btn BTCP" id="BTCP" onclick="modal_popup('BTCP');">꽃파는총각</button></div>
                                <div><button class="brand_btn BTCC" id="BTCC" onclick="modal_popup('BTCC');">칙칙폭폭플라워</button></div>
                                <div><button class="brand_btn BTSP" id="BTSP" onclick="modal_popup('BTSP');">사팔플라워</button></div>
                                <div><button class="brand_btn BTBR" id="BTBR" onclick="modal_popup('BTBR');">바로플라워</button></div>
                                <div><button class="brand_btn BTOM" id="BTOM" onclick="modal_popup('BTOM');">오만플라워</button></div>
                                <div><button class="brand_btn BTCS" id="BTCS" onclick="modal_popup('BTCS');">꽃파는사람들</button></div>
                                <div><button class="brand_btn BTFC" id="BTFC" onclick="modal_popup('BTFC');">플라체인</button></div>
                            </div>
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
    </div> <!-- end row -->
@endsection
@section('script')
    <script src="{{asset('assets/js/order/order-index.js')}}"></script>
    <script>
        function order_detail(order_idx){
            var url = './order-detail/'+order_idx;

            @if(Auth::user()->auth < 8)
                $('#new_order'+order_idx).hide();
            @endif
            open_win(url,"주문서"+fix,1440,900,0,0);
            fix++;
        }
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
            }else if(type=='지지난달'){
                start_date ='{{$commonDate['2month_ago_s']}}';
                end_date ='{{$commonDate['2month_ago_e']}}';
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





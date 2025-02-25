@extends('layouts.master-without-nav')
@section('content')
<link href="{{ URL::asset('/assets/css/order/form-order.css') }}" rel="stylesheet">
<script src="https://pg-web.nicepay.co.kr/v3/common/js/nicepay-pgweb.js" type="text/javascript"></script>
@include('order.modal.order-form-modal')
@if(session('alert'))
    <script>
        alert("{{session('alert')}}")
    </script>
@endif

<!-- 폼 양식 시작 -->
<form id="order_form" action="{{ route('form-order-insert') }}" method="post">
@csrf
<!-- 상단 브랜드 이름 fixed -->
<div class="text-center position-fixed top-0 start-0 px-2 py-1 opacity-75 rounded m-2 brand_type {{ $brand }}" style="z-index: 10; overflow: visible">
    <div class="dropdown">
        <p class="m-0 cursor_p" style="font-size: 18px" data-bs-toggle="dropdown" aria-expanded="false">{{ BrandAbbr($brand) }}</p>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="{{url('order/BTCP/form/')}}">꽃파는총각</a></li>
            <li><a class="dropdown-item" href="{{url('order/BTCC/form/')}}">칙칙폭폭플라워</a></li>
            <li><a class="dropdown-item" href="{{url('order/BTSP/form/')}}">사팔플라워</a></li>
            <li><a class="dropdown-item" href="{{url('order/BTBR/form/')}}">바로플라워</a></li>
            <li><a class="dropdown-item" href="{{url('order/BTOM/form/')}}">오만플라워</a></li>
            <li><a class="dropdown-item" href="{{url('order/BTCS/form/')}}">꽃파는사람들</a></li>
            <li><a class="dropdown-item" href="{{url('order/BTFC/form/')}}">플라체인</a></li>
        </ul>
    </div>
    <input type="hidden" id="brand" name="brand_type_code" value="{{ $brand }}">
</div>
<!-- 상단 카톡 전송 버튼 -->
@if($brand === 'BTCP')
<div class="text-center position-fixed top-0 end-0 px-2 py-1 opacity-75 m-2 btn-app" style="z-index: 10;">
    <p class="m-0 " data-bs-toggle="offcanvas" id="" data-bs-target="#offcanvasRight" onclick="orderer_phone()" aria-controls="offcanvasRight" style="font-weight: bold">간편주문</p>
</div>
@endif

<!-- 페이지 내용 시작 -->
    <div class="mt-3 p-2" style="display: flex; gap: 10px">
        <div class="row justify-content-center" style="min-width: 850px">
            <div class="col-12">
            @if($brand === 'BTFC' || $brand === 'BTCS')
                @include('order.include.order-form.channel-select')
            @endif
                <!-- 주문 정보 시작-->
                <div class="card">
                    <div class="card-body py-2">
                        <div class="mb-1 row">
                            <h5 class="card-title m-2">주문 정보</h5>
                        </div>
                        <!-- 주문 정보 [ 이름 / 일반 전화 ] -->
                        <div class="mb-2 row">
                            <div class="col-6">
                                <div class="input-group">
                                    <label class="input-group-text" for="orderer_name">이름</label>
                                    <input type="text" class="form-control" name="orderer_name" id="orderer_name" aria-label="Name" aria-describedby="orderer_name" value="{{optional($order)->orderer_name}}" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="input-group">
                                    <label class="input-group-text" for="orderer_tel">일반전화</label>
                                    <input type="text" class="form-control" name="orderer_tel" id="orderer_tel" aria-label="Tel" aria-describedby="orderer_tel" value="{{optional($order)->orderer_tel}}" onkeyup="auto_hyphen(event);">
                                </div>
                            </div>
                        </div>
                        <!-- 주문 정보 [ 휴대전화 / Email ] -->
                        <div class="row mb-2">
                            <div class="col-6">
                                <div class="input-group">
                                    <label class="input-group-text" for="orderer_phone">휴대전화</label>
                                    <input type="text" class="form-control" name="orderer_phone" id="orderer_phone"  aria-label="Phone" value="{{optional($order)->orderer_phone}}" onkeyup="auto_hyphen(event);" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="input-group">
                                    <label class="input-group-text" for="orderer_email">Email</label>
                                    <input type="text" class="form-control" name="orderer_email" id="orderer_email" aria-label="Email" value="{{optional($order)->orderer_email}}" aria-describedby="orderer_email">
                                </div>
                            </div>
                        </div>
                        <!-- 주문 정보 전송 [ SMS / 알림톡 ] -->
                        <div class="row">
                            <div class="col-12">
                                <div class="input-group mb-1 rounded radio_text">
                                    <div class="input-group-text me-3 border-top-0 border-bottom-0 border-start-0 ps-2">주문 정보 전송</div>
                                    <div class="form-check form-check-inline mt-1">
                                        <input class="form-check-input" type="radio" name="is_alim" id="checkBox_sms" value="sms">
                                        <label class="form-check-label" for="checkBox_sms">SMS 전송</label>
                                    </div>
                                    <div class="form-check form-check-inline mt-1">
                                        <input class="form-check-input" type="radio" name="is_alim" id="checkBox_talk" value="talk" checked>
                                        <label class="form-check-label" for="checkBox_talk">알림톡 전송</label>
                                    </div>
                                    <div class="form-check form-check-inline mt-1">
                                        <input class="form-check-input" type="radio" name="is_alim" id="checkBox_none" value="none">
                                        <label class="form-check-label" for="checkBox_none">전송 안함</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- 주문 정보 끝 -->
                <!-- 상품 정보 시작 -->
                <div class="card">
                    <div class="card-body py-2">
                        <div class="mb-2 row">
                            <div class="col-2">
                                <h5 class="card-title m-2">상품 정보</h5>
                            </div>
                            <!-- 상품 추가 모달 버튼 -->
                            <div class="col-2 ps-0">
                                <input type="hidden" id="price_type" value="1">
                                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#product-list-modal" style="margin-top: 4px; position: relative; left: -40px;">상품 추가</button>
                            </div>
                        </div>
                        <div class="row mb-1" id="goods_list_table">
                            <div class="col-12">
                                <div class="row">
                                    <div class="col-12">
                                        <!-- 추가 된 상품 리스트 table -->
                                        <table class="table table-bordered table-striped-columns">
                                            <thead>
                                                <tr class="table-secondary">
                                                    <th>상품명</th>
                                                    <th style="width: 55px">수량</th>
                                                    <th style="width: 100px">상품금액</th>
                                                    <th style="width: 100px">옵션금액</th>
                                                    <th style="width: 100px">결제금액</th>
                                                </tr>
                                            </thead>
                                            <!-- 상품 리스트 tbody -->
                                            <tbody id="selected-product">
                                                @include('order.include.order-form.product-selected')
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- 상품 정보 끝 -->
                <!-- 배송 정보 시작 -->
                <div class="card">
                    <div class="card-body py-2">
                        <div class="mb-1 row">
                            <h5 class="card-title m-2">배송 정보</h5>
                        </div>
                        <!-- 배송 정보 [ 이름 / 일반전화 ] -->
                        <div class="mb-1 row">
                            <div class="col-6">
                                <div class="input-group mb-2">
                                    <label class="input-group-text" for="receiver_name">이름</label>
                                    <input type="text" class="form-control" name="receiver_name" id="receiver_name" aria-label="Name" value="{{optional($order) -> delivery->receiver_name ?? ''}}" aria-describedby="receiver_name" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="input-group mb-2">
                                    <label class="input-group-text" for="receiver_tel">일반전화</label>
                                    <input type="text" class="form-control" name="receiver_tel" id="receiver_tel" aria-label="Tel" aria-describedby="receiver_tel" value="{{optional($order) -> delivery->receiver_tel ?? ''}}" onkeyup="auto_hyphen(event);">
                                </div>
                            </div>
                        </div>
                        <!-- 배송 정보 [ 휴대전화 / 배송시간 라디오 박스 ] -->
                        <div class="mb-1 row">
                            <div class="col-6">
                                <div class="input-group mb-2">
                                    <label class="input-group-text" for="receiver_phone">휴대전화</label>
                                    <input type="text" class="form-control" name="receiver_phone" id="receiver_phone" aria-label="Phone" aria-describedby="receiver_phone" value="{{optional($order) -> delivery->receiver_phone ?? ''}}" onkeyup="auto_hyphen(event);" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="input-group mb-1 rounded position-absolute bottom-0 ">
                                    <div class="form-check form-check-inline mt-1 me-2">
                                        <input class="form-check-input" type="radio" name="delivery_time_sel" id="deli_now" value="now" checked>
                                        <label class="form-check-label" for="deli_now" id="deli_now_label">즉시</label>
                                    </div>
                                    <div class="form-check form-check-inline mt-1 me-2">
                                        <input class="form-check-input" type="radio" name="delivery_time_sel" id="deli_event" value="event">
                                        <label class="form-check-label" for="deli_event">행사시간</label>
                                    </div>
                                    <div class="form-check form-check-inline mt-1 me-2">
                                        <input class="form-check-input" type="radio" name="delivery_time_sel" id="deli_time" value="time">
                                        <label class="form-check-label" for="deli_time">시간선택</label>
                                    </div>
                                    <div class="form-check form-check-inline mt-1">
                                        <input class="form-check-input" type="radio" name="delivery_time_sel" id="deli_input" value="input">
                                        <label class="form-check-label" for="deli_input">직접입력</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- 배송 정보 [ 배송일 / 배송 시간 ] -->
                        <div class="mb-1 row">
                            <div class="col-6">
                                <div class="input-group mb-2">
                                    <label class="input-group-text" for="delivery_date">배송일</label>
                                    <input type="date" class="form-control datepicker" name="delivery_date" id="delivery_date" placeholder="YYYY-MM-DD" value="{{ optional($order) -> delivery->delivery_date ?? date('Y-m-d') }}" style="z-index: 0 !important;" required>
                                </div>
                            </div>
                            <div class="col-6" id="event_time_input">
                                <div class="input-group mb-2">
                                    <label class="input-group-text" for="delivery_time">배송시간</label>
                                    <input type="text" class="form-control" name="delivery_time" id="delivery_time" value="{{optional($order) -> delivery->delivery_time ?? "즉시"}}">
                                </div>
                            </div>
                            <div class="col-6 d-none" id="event_time_select">
                                <div class="input-group mb-2">
                                    <span class="input-group-text">행사시간</span>
                                    <select class="form-select" name="event_hour" aria-label="event_hour">
                                        @for($i=8; $i<24; $i++)
                                            <option value="{{$i}}">{{$i}} 시</option>
                                        @endfor
                                    </select>
                                    <select class="form-select" name="event_min" aria-label="event_min">
                                        @for($j=0; $j<=5; $j++)
                                            <option value="{{$j*10}}">{{$j*10}} 분</option>
                                        @endfor
                                    </select>
                                    <input type="radio" class="btn-check" name="event_text" value="예식" id="event1" autocomplete="off" checked>
                                    <label class="btn btn-outline-secondary" for="event1">예식</label>
                                    <input type="radio" class="btn-check" name="event_text" value="행사" id="event2" autocomplete="off">
                                    <label class="btn btn-outline-secondary" for="event2">행사</label>
                                </div>
                            </div>
                            <div class="col-6 d-none" id="event_gap_select">
                                <div class="input-group mb-2">
                                    <span class="input-group-text">시간선택</span>
                                    <select class="form-select" name="event_time_start" aria-label="event_time_start">
                                        @for($i=8; $i<24; $i++)
                                            <option value="{{$i}}">{{$i}} 시</option>
                                        @endfor
                                    </select>
                                    <select class="form-select" name="event_time_end" aria-label="event_time_end">
                                        @for($i=8; $i<24; $i++)
                                            <option value="{{$i}}">{{$i}} 시</option>
                                        @endfor
                                    </select>
                                    <span class="input-group-text" style="width: 60px;">사이</span>
                                </div>
                            </div>
                        </div>
                        <!-- 주소 검색 버튼 -->
                        <div class="mb-1 row">
                            <!-- 주소 입력 칸 -->
                            <div class="col-12">
                                <div style="display: flex">
                                    <div class="input-group mb-1">
                                        <label class="input-group-text" for="delivery_address">주소</label>
                                        <input type="text" class="form-control" name="delivery_address" id="delivery_address" value="{{optional($order) -> delivery->delivery_address ?? ''}}" required>
                                        <button type="button" class="btn btn-primary btn-sm" id="searchMap" style="width:100px" onclick="search_address()">주소 검색</button>
                                    </div>
                                    <button type="button" class="btn btn-outline-secondary btn-sm location_btn" data-bs-toggle="modal" data-bs-target="#location_price" style="width:100px">지역추가금</button>
                                </div>
                                <div class="kakao_search_address" id="kakao_area" style="display:none;border:1px solid;width:680px;height:300px;margin:5px; left:110px; position:relative">
                                    <img src="//t1.daumcdn.net/postcode/resource/images/close.png" id="btnFoldWrap" style="cursor:pointer;position:absolute;right:0px;top:-1px;z-index:1" onclick="foldDaumPostcode()" alt="접기 버튼">
                                </div>
                            </div>
                        </div>
                        <!-- 메시지 라디오 버튼 -->
                        <div class="mb-1 row">
                            <div class="col-12">
                                <div class="input-group mb-1 rounded radio_text">
                                    <div class="input-group-text me-3 border-top-0 border-bottom-0 border-start-0">메시지</div>
                                    <div class="form-check form-check-inline mt-1">
                                        <input class="form-check-input" type="radio" required name="msg_type" id="radio_ribbon" value="ribbon" checked>
                                        <label class="form-check-label" for="radio_ribbon">리본</label>
                                    </div>
                                    <div class="form-check form-check-inline mt-1">
                                        <input class="form-check-input" type="radio" required name="msg_type" id="radio_card" value="card">
                                        <label class="form-check-label" for="radio_card">카드</label>
                                    </div>
                                    <div class="form-check form-check-inline mt-1">
                                        <input class="form-check-input" type="radio" required name="msg_type" id="radio_none" value="none">
                                        <label class="form-check-label" for="radio_none">없음</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- 리본 메시지 -->
                        <div class="msg_input" id="msg_ribbon">
                            <div class="mb-1 row px-2">
                                <!-- 자주 쓰는 경조사어 버튼 -->
                                <div class="col-12 bg-light py-2 rounded-4">
                                    <div class="row ms-2 mb-1">
                                        <span class="ps-0">자주 쓰는 경조사어</span>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <button type="button" class="btn btn-outline-secondary btn-sm mb-1 freq_btn w60">祝 結婚</button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm mb-1 freq_btn w60">祝 華婚</button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm mb-1 freq_btn w44">謹弔</button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm mb-1 freq_btn w44">弔儀</button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm mb-1 freq_btn w44">賻儀</button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm mb-1 freq_btn w60">祝 發展</button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm mb-1 freq_btn w60">祝 開業</button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm mb-1 freq_btn w60">祝 榮轉</button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm mb-1 freq_btn w60">祝 昇進</button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm mb-1 freq_btn w60">祝 生日</button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm mb-1 freq_btn w60">祝 古稀</button>
                                            <br>  <!-- 줄바꿈 위치 -->
                                            <button type="button" class="btn btn-outline-secondary btn-sm mb-1 freq_btn w60">축 결혼</button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm mb-1 freq_btn w60">축 화혼</button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm mb-1 freq_btn w44">근조</button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm mb-1 freq_btn w44">조의</button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm mb-1 freq_btn w44">부의</button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm mb-1 freq_btn w60">축 발전</button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm mb-1 freq_btn w60">축 개업</button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm mb-1 freq_btn w60">축 영전</button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm mb-1 freq_btn w60">축 승진</button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm mb-1 freq_btn w60">축 생일</button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm mb-1 freq_btn w60">축 고희</button><br>
                                            <button type="button" class="btn btn-outline-secondary btn-sm mb-1 freq_btn">삼가 故人의 冥福을 빕니다</button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm mb-1 freq_btn">삼가 고인의 명복을 빕니다</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- 경조사어 input -->
                            <div class="mb-1 row">
                                <div class="col-12">
                                    <div class="input-group mb-1">
                                        <span class="input-group-text input_msg">경조사어(우측)</span>
                                        <input type="text" class="form-control" name="delivery_ribbon_right" id="delivery_ribbon_right" value="{{optional($order) -> delivery->delivery_ribbon_right ?? ''}}">
                                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#find_ribbon" style="width:100px">경조사어 찾기</button>
                                    </div>
                                </div>
                            </div>
                            <!-- 보내는분 input -->
                            <div class="mb-1 row">
                                <div class="col-12">
                                    <div class="input-group mb-1">
                                        <span class="input-group-text input_msg">보내는분(좌측)</span>
                                        <input type="text" class="form-control" name="delivery_ribbon_left" id="delivery_ribbon_left" value="{{optional($order) -> delivery->delivery_ribbon_left ?? ''}}">
                                        <button type="button" class="btn btn-outline-secondary btn-sm" id="orderer_name_btn" style="width:100px">주문자 동일</button>
                                        <button type="button" class="btn btn-outline-secondary btn-sm" style="width:100px" onclick="previous_ribbon();">최근 문구</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- 카드 메시지 히든 -->
                        <div class="mb-1 row d-none msg_input" id="msg_card">
                            <div class="col-12">
                                <div class="input-group mb-1">
                                    <span class="input-group-text input_textarea_text">카드메시지</span>
                                    <textarea class="form-control input_textarea" name="delivery_card" aria-label="With textarea" id="card_msg">{{optional($order) -> delivery->delivery_card ?? ''}}</textarea>
                                </div>
                            </div>
                        </div>
                        <!-- 배송 요구사항 -->
                        <div class="mb-1 row">
                            <div class="col-12">
                                <div class="input-group mb-1">
                                    <span class="input-group-text input_msg" id="delivery_message">배송 요구사항</span>
                                    <input type="text" class="form-control" name="delivery_message" value="{{optional($order) -> delivery->delivery_message ?? ''}}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 결제 정보 시작 -->
                <div class="card">
                    <div class="card-body py-2">
                        <div class="mb-1 row">
                            <h5 class="card-title m-2">결제 정보</h5>
                        </div>
                        <!-- 결제 정보 [ 상품 금액 / 관리자 할인 ] -->
                        <div class="mb-2 row">
                            <div class="col-6">
                                <div class="input-group mb-1">
                                    <span class="input-group-text">상품 총액</span>
                                    <input type="text" class="form-control text-end" name="item_total_amount" value="0" readonly>
                                    <span class="input-group-text unit_text">원</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="input-group mb-1">
                                    <span class="input-group-text">관리자 할인</span>
                                    <input type="text" class="form-control text-end" id="admin_discount_input" value="0">
                                    <input type="radio" class="btn-check" name="discount_ratio" id="percentage" value="percentage" autocomplete="off">
                                    <label class="btn btn-outline-secondary discount_btn" for="percentage">% 할인</label>
                                    <input type="radio" class="btn-check" name="discount_ratio" id="amount" value="amount" autocomplete="off" checked>
                                    <label class="btn btn-outline-secondary discount_btn" for="amount">원 할인</label>
                                </div>
                            </div>
                        </div>
                        <!-- 결제 정보 결과 -->
                        <div class="row mb-2 px-3">
                            <div class="col-12 bg-light rounded-4">
                                <div class="row mt-2">
                                    <div class="col-3 text-center">
                                        <p>상품 총액</p>
                                    </div>
                                    <div class="offset-1 col-3 text-center">
                                        <p>관리자 할인 금액</p>
                                        <input type="hidden" name="admin_discount" value="0">
                                    </div>
                                    <div class="offset-1 col-3 text-center">
                                        <p>결제 금액</p>
                                        <input type="hidden" name="total_amount" value="0">
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-3 text-center">
                                        <span class="fw-bold text-primary" id="item-total-amount-text">0</span><span> 원</span>
                                    </div>
                                    <div class="col-1 text-center">
                                        <span class="uil-minus-circle fs-4"></span>
                                    </div>
                                    <div class="col-3 text-center">
                                        <span class="fw-bold text-danger" id="admin-discount-text">0</span><span> 원</span>
                                    </div>
                                    <div class="col-1 text-center">
                                        <span class="uil-equal-circle fs-4"></span>
                                    </div>
                                    <div class="col-3 text-center">
                                        <span class="fw-bold text-success" id="total-amount-text">0</span><span> 원</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- 결제 방법 라디오 박스 -->
                        <div class="row mb-1">
                            <div class="col-12">
                                <div class="input-group mb-1 rounded radio_text">
                                    <span class="input-group-text me-3 border-top-0 border-bottom-0 border-start-0">결제 방법</span>
                                    <div class="form-check form-check-inline mt-1">
                                        <input class="form-check-input" type="radio" name="payment_type_code" required id="pay_card" value="PTMN" checked>
                                        <label class="form-check-label" for="pay_card">수기카드</label>
                                    </div>
                                    <div class="form-check form-check-inline mt-1">
                                        <input class="form-check-input" type="radio" name="payment_type_code" required id="pay_account" value="PTVA">
                                        <label class="form-check-label" for="pay_account">가상계좌</label>
                                    </div>
                                    <div class="form-check form-check-inline mt-1" id="pay_misu_area">
                                        <input class="form-check-input" type="radio" name="payment_type_code" required id="pay_misu" value="PTDP">
                                        <label class="form-check-label" for="pay_misu">법인미수</label>
                                    </div>
                                    <div class="form-check form-check-inline mt-1">
                                        <input class="form-check-input" type="radio" name="payment_type_code" required id="pay_deposit" value="PTDB">
                                        <label class="form-check-label" for="pay_deposit">무통장</label>
                                    </div>
                                    <div class="form-check form-check-inline mt-1">
                                        <input class="form-check-input" type="radio" name="payment_type_code" required id="pay_except" value="PTOP">
                                        <label class="form-check-label" for="pay_except">외부결제</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- 선결제 라디오 박스 -->
                        <div class="row mb-1">
                            <div class="col-12">
                                <div class="input-group mb-1 rounded radio_text">
                                    <span class="input-group-text me-3 border-top-0 border-bottom-0 border-start-0">사업자 선결제</span>
                                    <div class="form-check form-check-inline mt-1">
                                        <input class="form-check-input" type="radio" name="advance_payment" required id="advance_payment_N" value="0" checked>
                                        <label class="form-check-label" for="advance_payment_N">일반</label>
                                    </div>
                                    <div class="form-check form-check-inline mt-1">
                                        <input class="form-check-input" type="radio" name="advance_payment" required id="advance_payment_Y" value="1">
                                        <label class="form-check-label" for="advance_payment_Y">사업자 선결제</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- 결제 정보 끝 -->

                <!-- 증빙 서류 신청 -->
                <div class="card">
                    <div class="card-body py-2">
                        <div class="row mb-1">
                            <h5 class="card-title m-2">증빙</h5>
                        </div>
                        <div class="row mb-1">
                            <div class="col-6">
                                <div class="input-group mb-1">
                                    <span class="input-group-text">입금자명</span>
                                    <input type="text" class="form-control" name="deposit_name" id="deposit_name" value="" aria-label="deposit_name">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="input-group mb-1 rounded radio_text">
                                    <span class="input-group-text border-top-0 border-bottom-0 border-start-0">증빙 서류</span>
                                    <select class="form-select document_select" name="document_type" aria-label="Default select example">
                                        <option value="">- 증빙선택 -</option>
                                        <option value="PMCR">현금영수</option>
                                        <option value="PMPE">지출증빙</option>
                                        <option value="PMVI">자진발급</option>
                                        <option value="PMIB">계산서</option>
                                    </select>
                                    <select class="form-select document_select" name="is_publish" aria-label="Default select example">
                                        <option value="0">미발행</option>
                                        <option value="1">발행</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-12">
                                <div class="input-group mb-1">
                                    <span class="input-group-text input_textarea_text">결제 메모</span>
                                    <textarea class="form-control input_textarea" name="payment_memo" aria-label="With textarea" style="height: 55px"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- 증빙 서류 끝 -->
            <div class="card" style="margin-bottom: 200px;">
                <div class="card-body py-2">
                    <div class="mb-1 row">
                        <div class="col-12">
                            <div class="input-group mb-1 rounded radio_text">
                                <span class="input-group-text me-3 border-top-0 border-bottom-0 border-start-0">유입 정보</span>
                                <div class="form-check form-check-inline mt-1">
                                    <input class="form-check-input" type="radio" name="inflow" required id="inflow_type_mall" value="mall">
                                    <label class="form-check-label" for="inflow_type_mall">몰</label>
                                </div>
                                <div class="form-check form-check-inline mt-1">
                                    <input class="form-check-input" type="radio" name="inflow" required id="inflow_type_call" value="call">
                                    <label class="form-check-label" for="inflow_type_call">전화</label>
                                </div>
                                <div class="form-check form-check-inline mt-1">
                                    <input class="form-check-input" type="radio" name="inflow" required id="inflow_type_sms" value="sms">
                                    <label class="form-check-label" for="inflow_type_sms">문자</label>
                                </div>
                                <div class="form-check form-check-inline mt-1">
                                    <input class="form-check-input" type="radio" name="inflow" required id="inflow_type_talk" value="talk">
                                    <label class="form-check-label" for="inflow_type_talk">카톡</label>
                                </div>
                                <div class="form-check form-check-inline mt-1">
                                    <input class="form-check-input" type="radio" name="inflow" required id="inflow_type_channel" value="channel">
                                    <label class="form-check-label" for="inflow_type_channel">카톡채널</label>
                                </div>
                                <div class="form-check form-check-inline mt-1">
                                    <input class="form-check-input" type="radio" name="inflow" required id="inflow_type_etc" value="etc">
                                    <label class="form-check-label" for="inflow_type_etc">기타</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-1 row">
                        <div class="col-6">
                            <div class="input-group mb-1">
                                <span class="input-group-text">주문입력자</span>
                                <input type="text" class="form-control" name="handler" id="handler" value="{{ Auth::user()->name }}" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="admin_memo_area">
                <!-- 주문 메모 시작-->
                <div class="card" style="background-color: #f1f1f1;">
                    <div class="card-body py-2">
                        <div class="mb-1 row">
                            <div class="col-6">
                                <h5 class="card-title m-2">주문 메모</h5>
                            </div>
                            <!-- 메모 편집 버튼 -->
                            <div class="col-6">
                                <button type="button" class="btn btn-outline-secondary btn-sm position-absolute bottom-0 end-0 me-3" onclick="edit_memo('{{ $brand }}');">자주 쓰는 메모 편집</button>
                            </div>
                        </div>

                        <!-- 관리자 메모 -->
                        <div class="mb-1 row">
                            <div class="col-12">
                                <div class="input-group mb-1">
                                    <span class="input-group-text input_textarea_text">관리자메모</span>
                                    <textarea class="form-control input_textarea" id="admin_memo" name="admin_memo" aria-label="With textarea"></textarea>
                                </div>
                            </div>
                        </div>
                        <!-- 자주 쓰는 메모 select -->
                        <div class="row">
                            <div class="col-6">
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary dropdown-toggle waves-effect waves-light" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        자주 쓰는 메모 <i class="mdi mdi-chevron-down"></i>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        @foreach($memo_list as $memo)
                                            <a class="dropdown-item" href="javascript:void(0);" onclick="set_admin_memo(event);">{{ $memo -> note }}</a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 text-end">
                                <button type="button" class="btn btn-primary waves-effect waves-light w-75" onclick="form_submit(event)">주문 등록</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- 주문 메모 끝 -->
            </div>

            </div> <!-- 내용 끝 ( col ) -->

            <!-- 간편주문 앱 링크 보내기 -->
            @include('order.include.order-form.mobile-order-link')

        </div><!-- 페이지 끝 ( row ) -->
{{--        <div style="min-width: 400px" id="channel-info-container">--}}
{{--            @include('order.include.order-form.channel-info')--}}
{{--        </div>--}}
    </div>
</form>
<div id="payment_area"></div>

<script> $('.datepicker').datepicker({ autoclose: true }); </script>
<script src="/assets/js/order/form-order.js?v={{ time() }}"></script>
<script src="//t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>
@endsection


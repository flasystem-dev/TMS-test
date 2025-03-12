<div class="modal fade" id="add_payment_modal" tabindex="-1" aria-labelledby="add_payment_modal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">추가결제</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-5">
                <form action="{{ url('order/detail/payment') }}" id="add_payment_form">
                    <input type="hidden" name="order_idx" value="{{$order->order_idx}}">
                    <div class="input-group mb-3">
                        <span class="input-group-text modal_label_text">결제수단</span>
                        <select class="form-select" name="payment_type_code">
                            <option value="PTMN">수기결제</option>
                            <option value="PTVA">가상계좌</option>
                            <option value="PTCD">신용카드</option>
                            <option value="PTDP">법인미수</option>
                            <option value="PTDB">무통장</option>
                            <option value=""></option>
                        </select>
                    </div>
                    <div>
                        <div class="input-group mb-3">
                            <span class="input-group-text modal_label_text">구매자 명</span>
                            <input type="text" class="form-control" name="paymentName">
                            <button type="button" class="btn btn-secondary" style="width: 150px;" onclick="get_ordererName()">주문자명 가져오기</button>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <span class="input-group-text modal_label_text">결제금액</span>
                        <input type="number" class="form-control text-end" name="payment_amount" value="0">
                        <span class="input-group-text text-center" style="width: 10%">원</span>
                    </div>
                    <div class="input-group mb-3">
                        <span class="input-group-text modal_label_text">추가결제-상품명</span>
                        <input type="text" class="form-control" name="payment_item" value="{{$order->delivery->goods_name}}">
                    </div>
                    <div class="input-group mb-3">
                        <span class="input-group-text modal_label_text">입금자명</span>
                        <input type="text" class="form-control" name="deposit_name">
                    </div>
                    <div class="input-group mb-3">
                        <span class="input-group-text modal_label_text">결제 메모</span>
                        <textarea class="form-control" name="payment_memo" aria-label="add_price_memo"></textarea>
                    </div>
{{--                    <div class="input-group mb-3 rounded form_radio_group">--}}
{{--                        <span class="input-group-text modal_label_text me-3">알림톡</span>--}}
{{--                        <div class="form-check form_radio mt-2">--}}
{{--                            <input class="form-check-input" type="radio" value="Y" name="add_alim_talk" id="add_alim_talk_Y">--}}
{{--                            <label class="form-check-label" for="add_alim_talk_Y">전송</label>--}}
{{--                        </div>--}}
{{--                        <div class="form-check form_radio mt-2">--}}
{{--                            <input class="form-check-input" type="radio" value="N" name="add_alim_talk" id="add_alim_talk_N" checked>--}}
{{--                            <label class="form-check-label" for="add_alim_talk_N">미전송</label>--}}
{{--                        </div>--}}
{{--                    </div>--}}
                    <div class="input-group mb-3 rounded form_radio_group">
                        <span class="input-group-text modal_label_text me-3">사업자 선결제</span>
                        <div class="form-check form_radio mt-2">
                            <input class="form-check-input" type="radio" value="0" name="advance_payment" id="add_advance_payment_N" checked>
                            <label class="form-check-label" for="add_advance_payment_N">일반</label>
                        </div>
                        <div class="form-check form_radio mt-2">
                            <input class="form-check-input" type="radio" value="1" name="advance_payment" id="add_advance_payment_Y">
                            <label class="form-check-label" for="add_advance_payment_Y">선결제</label>
                        </div>
                    </div>
                </form>
                <div id="payment_area"></div>
            </div>
            @if(Auth::user()->auth > 2)
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="addPay_close">닫기</button>
                <button type="button" class="btn btn-warning" onclick="add_payment()">결제추가</button>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- 아이템 리스트 모달 창 -->
<div class="modal fade" id="product_modal" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg goods_modal">
        <div class="modal-content">
            <!-- 모달 헤더 -->
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="myExtraLargeModalLabel">상품 변경 <span class="price-type-text">{{priceTypeName($order->price_type())}}</span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="product_modal_close">
                </button>
            </div>
            <div class="modal-body">
                <!-- 상품 검색 라인 -->
                <form id="product-search-form">
                    <div class="row">
                        <!-- 브랜드 코드 히든 -->
                        <input type="hidden" id="price_type" value="{{$order->price_type()}}">
                        <!-- 카테고리 select -->
                        <div class="col-4">
                            @php
                                $ctgys = DB::table('tms_ctgy') -> select('ct2','ct_name') -> where('ct1', 'A') -> where('ct2', '>', 0) -> get();
                            @endphp

                            <select class="form-select" name="product_ctgy" id="product_ctgy">
                                <option value="">카테고리</option>
                                @foreach($ctgys as $ctgy)
                                    <option value="{{ $ctgy -> ct2 }}">{{ $ctgy -> ct_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- 직접 입력 input -->
                        <div class="col-6">
                            <input type="text" class="form-control" name="search_word" placeholder="상품명 or 검색어">
                        </div>
                        <!-- 검색 버튼 -->
                        <div class="col-2">
                            <a type="button" class="btn btn-outline-secondary fs-5 py-1 h-100" id="search_btn">검색</a>
                        </div>
                    </div>
                </form>
                <!-- 상품 리스트 결과 창 -->
                <hr>
                <div class="row">
                    <div class="col-12" id="product-list">
                        <!-- 아이템 리스트 검색 결과 목록 -->
                        @include('order.include.order-form.products-list')
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->

</div><!-- /.modal -->
<!-- 아이템 리스트 모달 창 끝 -->

<div class="modal fade" id="change_vendor_modal" tabindex="-1" aria-labelledby="add_payment_modal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">사업자 변경</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="change_vendor_form">
                    <input type="hidden" name="order_idx" value="{{$order->order_idx}}">
                    <input type="hidden" name="handler" value="{{Auth::user()->name}}">
                    <div class="input-group mb-3">
                        <span class="input-group-text">사업자선택</span>
                        <select class="form-select" name="change_vendor" id="change_vendor" aria-label="change_vendor">

                        </select>
                    </div>

                    <div class="input-group mb-3">
                        <span class="input-group-text" >고객선택</span>
                        <select class="form-select" name="orderer_mall_id" id="orderer_mall_id" data-customer="{{$order->orderer_mall_id}}" aria-label="orderer_mall_id">

                        </select>
                    </div>

                    <div class="input-group mb-4">
                        <span class="input-group-text">관리자 메모</span>
                        <textarea class="form-control" name="add_admin_memo" aria-label="add_admin_memo">{{$order->admin_memo}}</textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="change_vendor();">사업자 변경</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="change_payment_type" tabindex="-1" aria-labelledby="change_payment_type" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">결제수단 변경</h1>
                <span style="margin-left: 10px;">(</span>
                <span id="modal_payment_number_text">?</span>번)
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="payment_type_form">
                    <input type="hidden" name="order_idx" value="{{$order->order_idx}}">
                    <input type="hidden" name="payment_number" value="">
                    <div class="input-group mb-3">
                        <span class="input-group-text">결제수단</span>
                        <select class="form-select" name="payment_type_code" aria-label="payment_type_code">
                            <option value="PTCD">신용카드</option>
                            <option value="PTBT">계좌이체</option>
                            <option value="PTVA">가상계좌</option>
                            <option value="PTDP">미수거래</option>
                            <option value="PTDB">무통장입금</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="change_payment_type();">결제수단 변경</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="change_payment_state" tabindex="-1" aria-labelledby="change_payment_type" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5">결제상태 변경</h1>
                <span style="margin-left: 10px;">(</span>
                <span id="modal_payment_state_text">?</span>번)
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="payment_state_form">
                    <input type="hidden" name="order_idx" value="{{$order->order_idx}}">
                    <input type="hidden" name="payment_number" value="">
                    <input type="hidden" name="handler" value="{{Auth::user()->name}}">
                    <div class="input-group mb-3">
                        <span class="input-group-text">결제상태</span>
                        <select class="form-select" name="payment_state_code" id="select_payment_state" aria-label="payment_state_code">
                            <option value="PSDN">결제완료</option>
                            <option value="PSCC">취소완료</option>
                            <option value="PSUD">결제대기</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="change_payment_state();">결제상태 변경</button>
            </div>
        </div>
    </div>
</div>

@if($order->brand_type_code=="BTFC" || $order->brand_type_code=="BTCS")
<div class="modal fade" id="balju_amount_area" tabindex="-1" aria-labelledby="change_payment_type" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 700px">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5">발주 정보</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="balju_amount_form">
                <table class="table m-0 bg-light option_table">
                    <thead>
                    <tr class="thead_border_bottom">
                        <th>상품명</th>
                        <th style="padding-left: 15px">상품금액</th>
                        <th style="padding-left: 45px">사업자발주</th>
                        <th style="padding-left: 45px">화원사발주</th>
                    </tr>
                    </thead>
                    <tbody id="table_tbody">
                    <tr>
                        <td class="price_title">{{$order->delivery->goods_name}}</td>
                        <td class="price_text" >{{number_format($order->item->product_price)}} 원</td>
                        <td class="balju_amount"><input type="number" id="vendor_amount" name="vendor_amount" class="form-control option_price_input" value="{{$order->vendor_amount}}"></td>
                        <td class="balju_amount"><input type="number" name="balju_amount" class="form-control option_price_input" value="{{$order->balju_amount}}" disabled></td>
                    </tr>
                    @if($order->item->options->isNotEmpty())
                        @foreach($order->item->options as $option)
                            <tr>
                                <input type="hidden" name="option_id[]" value="{{$option->id}}">
                                <td class="option_title">{{$option->option_name}}</td>
                                <td class="price_text">{{number_format($option->option_price)}} 원</td>
                                <td class="balju_amount"><input type="text" class="form-control option_price_input vendor_option_price" name="vendor_option_price[]" value="{{$option->vendor_option_price}}"></td>
                                <td class="balju_amount"><input type="text" class="form-control option_price_input" name="balju_option_price[]" value="{{$option->balju_option_price}}" disabled></td>
                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>
                </form>
                <div style="display: flex; gap: 10px; margin-top: 10px;">
                    <div class="input-group">
                        <span class="input-group-text" style="width: 150px; padding-left: 10px;">사업자발주금액</span>
                        <input type="text" class="form-control text-end" id="vendor_amount_display" aria-label="vendor_amount_display" value="{{number_format($order->vendor_amount)}}" disabled>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text" style="width: 150px; padding-left: 10px;">사업자옵션합계</span>
                        <input type="text" class="form-control text-end" id="vendor_optionAmount_display" aria-label="vendor_optionAmount_display" value="{{$order->item->vendor_options_amount}}" disabled>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="update_balju();">발주정보 수정</button>
            </div>
        </div>
    </div>
</div>
@endif

<div class="modal fade" id="talk_modal_area" tabindex="-1" aria-labelledby="change_payment_type" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 1200px">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5">알림톡 전송</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="sendTalk_form">
                <div class="d-flex" style="width: 100%; gap: 10px; justify-content: space-between; align-items: center;">
                    <div class="input-group" style="width:  25%">
                        <span class="input-group-text">템플릿</span>
                        <select name="talk_template_type" id="talk_template_type" class="form-select" aria-label="template_type">
                            <option value="order_check">주문확인</option>
                            <option value="pay_complete">결제완료</option>
                            <option value="VA_guide">가상계좌 안내</option>
                            <option value="deli_photo">배송사진</option>
                            <option value="without_bank_account">무통장 안내</option>
                        </select>
                    </div>
                    <div class="input-group" style="width: 25%">
                        <span class="input-group-text">결제번호</span>
                        <select name="talk_payment_number" id="talk_payment_number" class="form-select" aria-label="payment_number">
                            <option value="total">통합</option>
                            @if($order->payments->isNotEmpty())
                            @foreach($order->payments as $payment)
                            <option value="{{$payment->payment_number}}">{{$payment->payment_number}}</option>
                            @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="input-group" style="width: 27%; margin-left: auto; margin-right: 40px;">
                        <span class="input-group-text" id="basic-addon1">수신번호</span>
                        <input type="text" class="form-control" name="receive_number" value="{{$order->orderer_phone}}" aria-label="receive_phone" aria-describedby="basic-addon1" oninput="">
                    </div>
                </div>
                <div id="template_area" style="font-weight: bolder"></div>
                </form>
            </div>
            <div class="modal-footer">
                <div style="display: flex">
                    <button type="button" class="btn btn-primary" onclick="send_customTalk()">알림톡 전송</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 환불 요청 모달 -->
<div class="modal fade" id="refund_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 620px;">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">환불 처리</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="refund_modal_body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">취소</button>
                <button type="button" class="btn btn-primary" onclick="refund_submit();">환불 처리</button>
            </div>
        </div>
    </div>
</div>
<!-- 환불 요청 모달 끝 -->

<!-- 카드 키인 추가 결제 -->
<div class="modal fade" id="cardKeyIn" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">수기 결제</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="keyIn_form">


                    <div>
                        <span class="col-form-text mb-2">카드 번호</span>
                        <div class="input-group mb-3">
                            <input type="number" class="form-control" name="cardNum1" id="cardNum1" maxlength="4" aria-label="card_num" value="" oninput="moveToNext(this, 4)">
                            <input type="number" class="form-control" name="cardNum2" id="cardNum2" maxlength="4" aria-label="card_num" value="" onkeydown="checkBackspace(event)" oninput="moveToNext(this, 4)">
                            <input type="number" class="form-control" name="cardNum3" id="cardNum3" maxlength="4" aria-label="card_num" value="" onkeydown="checkBackspace(event)" oninput="moveToNext(this, 4)">
                            <input type="number" class="form-control" name="cardNum4" id="cardNum4" maxlength="6" aria-label="card_num" value="" onkeydown="checkBackspace(event)">
                        </div>
                    </div>
                    <div class="mb-1">
                        <span class="col-form-text mb-2">만료 기간 (MM/YY)</span>
                        <div class="input-group w-50">
                            <input type="text" class="form-control" name="exMonth" id="exMonth" aria-label="card_num" value="" maxlength="2" placeholder="MM" onkeydown="checkBackspace(event)">
                            <input type="text" class="form-control" name="exYear" id="exYear" aria-label="card_num" value="" maxlength="2" placeholder="YY" oninput="moveToNext(this, 2)">
                        </div>
                    </div>
                    <div class="mb-1">
                        <span class="col-form-text mb-2">할부</span>
                        <div class="input-group w-50">
                            <select class="form-select" name="cardQuota" aria-label="example">
                                <option value="00" selected>일시불</option>
                                <option value="02">2개월</option>
                                <option value="03">3개월</option>
                                <option value="04">4개월</option>
                                <option value="05">5개월</option>
                                <option value="06">6개월</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="pay_keyIn(event);">키인 결제</button>
            </div>
        </div>
    </div>
</div>
<!-- 카드 키인 추가 결제 끝 -->
@extends('layouts.master-without-nav')
@section('content')
<link href="{{ asset('/assets/css/order/order-excel.css') }}" rel="stylesheet">
<div class="row m-3">
    <div class="card m-0">
        <div class="card-body">
            <div>
                <h4 class="text-center">주문 엑셀 다운로드</h4>
                <hr>
            </div>
            <div>
                <form id="excel_bulk_download_form">
                    <div class="mb-3">
                        <input type="radio" class="btn-check brand_checkbox" name="excel_brand" id="btn-brand_BTCP" value="BTCP" autocomplete="off">
                        <label class="btn excel_modal_brand_checkbox brand_BTCP" for="btn-brand_BTCP">꽃총</label>
                        <input type="radio" class="btn-check brand_checkbox" name="excel_brand" id="btn-brand_BTCC" value="BTCC" autocomplete="off">
                        <label class="btn excel_modal_brand_checkbox brand_BTCC" for="btn-brand_BTCC">칙폭</label>
                        <input type="radio" class="btn-check brand_checkbox" name="excel_brand" id="btn-brand_BTSP" value="BTSP" autocomplete="off">
                        <label class="btn excel_modal_brand_checkbox brand_BTSP" for="btn-brand_BTSP">사팔</label>
                        <input type="radio" class="btn-check brand_checkbox" name="excel_brand" id="btn-brand_BTBR" value="BTBR" autocomplete="off">
                        <label class="btn excel_modal_brand_checkbox brand_BTBR" for="btn-brand_BTBR">바로</label>
                        <input type="radio" class="btn-check brand_checkbox" name="excel_brand" id="btn-brand_BTOM" value="BTOM" autocomplete="off">
                        <label class="btn excel_modal_brand_checkbox brand_BTOM" for="btn-brand_BTOM">오만</label>
                        <input type="radio" class="btn-check brand_checkbox" name="excel_brand" id="btn-brand_BTCS" value="BTCS" autocomplete="off">
                        <label class="btn excel_modal_brand_checkbox brand_BTCS" for="btn-brand_BTCS">꽃사</label>
                        <input type="radio" class="btn-check brand_checkbox" name="excel_brand" id="btn-brand_BTFC" value="BTFC" autocomplete="off">
                        <label class="btn excel_modal_brand_checkbox brand_BTFC" for="btn-brand_BTFC">플체</label>
                    </div>
                    <div class="mb-1">
                        <select class="form-select excel_modal_select" name="payment_state_code">
                            <option value="all">- 결제상태 -</option>
                            <option value="PSUD">결제대기</option>
                            <option value="PSDN">결제완료</option>
                            <option value="PSCC">취소완료</option>
                        </select>

                        <select class="form-select excel_modal_select" name="payment_type_code">
                            <option value="all">- 결제방법 -</option>
                            <option value="PTCD">신용카드</option>
                            <option value="PTBT">계좌이체</option>
                            <option value="PTVA">가상계좌</option>
                            <option value="PTDP">법인미수</option>
                            <option value="PTDB">무통장</option>
                            <option value="PTMN">수기결제</option>
                        </select>

                        <select class="form-select excel_modal_select" name="delivery_state_code">
                            <option value="all">- 배송상태 -</option>
                            <option value="DLUD">미배송</option>
                            <option value="DLSP">배송중</option>
                            <option value="DLDN">배송완료</option>
                            <option value="DLCC">취소주문</option>
                        </select>
                    </div>
                    <div class="mb-2 text-end">

                        <div class="form-check excel_modal_checkbox me-2">
                            <input class="form-check-input" type="checkbox" name="excel_except[]" value="PSCC" id="excel_except_PSCC">
                            <label class="form-check-label" for="excel_except_PSCC">
                                취소완료 제외
                            </label>
                        </div>
                        <div class="form-check excel_modal_checkbox">
                            <input class="form-check-input" type="checkbox" name="excel_except[]" value="DLCC" id="excel_except_DLCC">
                            <label class="form-check-label" for="excel_except_DLCC">
                                취소주문 제외
                            </label>
                        </div>
                    </div>
                    <div class="mb-1">
                        <select class="form-select excel_modal_select" name="date_type">
                            <option value="order_time">주문접수일 기준</option>
                            <option value="delivery_date">배송요청일 기준</option>
                        </select>
                        <input type="date" name="excel_start_date" id="excel_start_date" class="form-control excel_modal_date_input" value="{{date('Y-m-01')}}">
                        <span>~</span>
                        <input type="date" name="excel_end_date" id="excel_end_date" class="form-control excel_modal_date_input" value="{{date('Y-m-d')}}">
                    </div>
                    <div class="text-end">
                        <button type="button" class="btn btn-outline-secondary excel_modal_date_btn" onclick="excel_date_select('2monthAgo')">전전월</button>
                        <button type="button" class="btn btn-outline-secondary excel_modal_date_btn" onclick="excel_date_select('monthAgo')">전월</button>
                        <button type="button" class="btn btn-outline-secondary excel_modal_date_btn" onclick="excel_date_select('thisMonth')">당월</button>
{{--                        <button type="button" class="btn btn-outline-secondary excel_modal_date_btn" onclick="excel_date_select('recent2Month')">최근 2개월</button>--}}
                    </div>
                </form>
            </div>
            <div class="excel_footer">
                <span class="info_text">&#8251; 데이터 양에 따라 시간이 오래 걸릴 수 있습니다.</span>
                <button type="button" class="btn btn-success" onclick="excel_batch_download()">다운로드</button>
            </div>
        </div>
    </div>
</div>

@endsection
@section('script')
<script src="{{asset('assets/js/order/order-excel.js')}}"></script>
@endsection
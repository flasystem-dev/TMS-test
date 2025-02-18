@extends('layouts.master-without-nav')
@section('content')
<link href="{{ URL::asset('/assets/css/order/form-balju.css') }}" rel="stylesheet">
@php
    $mytime = time();
    $mysec = md5($mytime);
    $mytime = base64_encode($mytime);
    $myid = base64_encode('flachain');
    $rose_session = $mytime."DiV".$mysec."DiV".$myid;

@endphp

<div style="min-width: 920px;">
<form id="order_form" action="{{route('order-intranet')}}" method="POST">
    @csrf
    <div class="row">
        <div class="col-12 pt-2 px-5">
            <div class="card m-0">
                <div class="card-body py-2">
                    <h4>발주하기</h4>
                    <hr>
                    <table class="table m-0">
                        <tr>
                            <th>발주화원</th>
                            <td><input type="text" name="order_addr" class="form-control shop_addr"></td>
                            <td><input type="text" name="order_shop" class="form-control" value="{{CommonCodeName($order->brand_type_code)}}"></td>
                        </tr>
                        <tr>
                            <th>수주화원</th>
                            <td><input type="text" name="receive_addr" class="form-control shop_addr" value="부산 부산진구"></td>
                            <td><input type="text" name="receive_shop" class="form-control" value="전국플라워센터"></td>
                            <td><input type="text" name="receive_name" class="form-control" value="김태진"></td>
                            <td><input type="text" name="receive_tel" class="form-control" value="1544-6487"></td>
                            <td>
                                <button type="button" class="btn btn-outline-secondary input_btn" onclick="find_suju('{{$rose_session}}');">수주화원 검색</button>
                                <input type="hidden" name="receive_shop_id" value="all1592">
                                <input type="hidden" name="order_idx" value="{{$order -> order_idx}}">
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 pt-2 px-5">
            <div class="card m-0">
                <div class="card-body py-2">
                    <table class="table m-0">
                        <tr>
                            <th>상품명</th>
                            <td style="width: 60%;"><input type="text" class="form-control" name="intranet_product_name" value="{{$order->item->product_name}}"></td>
                            <th>수량</th>
                            <td><input type="text" class="form-control text-end px-3" name="rw_qty" value="1" disabled></td>
                        </tr>
                    </table>

                    <div class="product-container bg-light mt-2">
                        <div class="product-table-row product-table-border">
                            <div class="product-name text-center fw-bold">품명</div>
                            <div class="product-price text-center fw-bold">소비자금액</div>
                            <div class="product-vendor-price text-center fw-bold">사업자발주</div>
                            <div class="product-balju-price text-center fw-bold">화원사발주</div>
                            <div class="product-etc text-center fw-bold"></div>
                        </div>
                        <div class="product-table-row">
                            <input type="hidden" name="product_id" value="{{$order->item->product_id}}">
                            <input type="hidden" name="price_type_id" value="{{$order->item->price_type_id}}">
                            <input type="hidden" name="product_name" value="{{$order->item->product_name}}">
                            <input type="hidden" name="item_total_amount" value="{{$order->item->product_price}}">
                            <input type="hidden" name="product_price" value="{{$order->item->product_price}}">
                            <div class="product-name text-center fw-bold">{{$order->item->product_name}}</div>
                            <div class="product-price product-price-padding text-end">{{number_format($order->item->product_price)}} 원</div>
                            <div class="product-vendor-price"><input type="number" class="form-control price-input" name="vendor_price" value="{{$order->vendor_amount}}"></div>
                            <div class="product-balju-price"> <input type="number" class="form-control price-input" name="balju_price" value="{{$order->balju_amount}}"></div>
                            <div class="product-etc text-center fw-bold"><button type="button" class="btn btn-primary option-btn" id="add-option-btn">+</button></div>
                        </div>

                        <div id="options-container">
                            <template id="option-template">
                                <div class="product-table-row">
                                    <input type="hidden" name="option_type_id[]" value="10">
                                    <input type="hidden" name="option_type_name[]" value="기타">
                                    <input type="hidden" name="option_price_id[]" value="0">
                                    <input type="hidden" name="option_price[]" value="0">
                                    <div class="product-name text-center ps-3"><input type="text" class="form-control text-input" name="option_name[]"></div>
                                    <div class="product-price product-price-padding text-end">0 원</div>
                                    <div class="product-vendor-price option-price-input"><input type="number" name="vendor_option_price[]" class="form-control price-input" value="0"></div>
                                    <div class="product-balju-price option-price-input"><input type="number" name="balju_option_price[]" class="form-control price-input" value="0"></div>
                                    <div class="product-etc text-center fw-bold"><button type="button" class="btn btn-danger option-btn remove-option-btn">-</button></div>
                                </div>
                            </template>
                        @if($order->item->options->isNotEmpty())
                            @foreach($order->item->options as $option)
                                <div class="product-table-row">
                                    <input type="hidden" name="option_type_id[]" value="{{$option->option_type_id}}">
                                    <input type="hidden" name="option_type_name[]" value="{{$option->option_type_name}}">
                                    <input type="hidden" name="option_price_id[]" value="{{$option->option_price_id}}">
                                    <input type="hidden" name="option_price[]" value="{{$option->option_price}}">
                                    <div class="product-name text-center ps-3"><input type="text" class="form-control text-input" name="option_name[]" value="{{$option->option_name}}" readonly></div>
                                    <div class="product-price product-price-padding text-end">{{number_format($option->option_price)}} 원</div>
                                    <div class="product-vendor-price option-price-input"><input type="number" name="vendor_option_price[]" class="form-control price-input" value="{{$option->vendor_option_price}}"></div>
                                    <div class="product-balju-price option-price-input"><input type="number" name="balju_option_price[]" class="form-control price-input" value="{{$option->balju_option_price}}"></div>
                                    <div class="product-etc text-center fw-bold"></div>
                                </div>
                            @endforeach
                        @endif
                        </div>
                        <div class="product-table-row row-result">
                            <div class="product-name text-center fw-bold option-result">옵션 합계 금액</div>
                            <div class="product-price product-price-padding text-end"></div>
                            <div class="product-vendor-price"><input type="text" class="form-control price-input" id="vendor_options_amount" value="{{$order->item->vendor_options_amount}}" disabled></div>
                            <div class="product-balju-price"><input type="text" class="form-control price-input" id="balju_options_amount" value="{{$order->item->balju_options_amount}}" disabled></div>
                            <div class="product-etc text-center fw-bold"></div>
                        </div>

                    </div>
                    <div class="product-container bg-light mt-2">
                        <div class="product-table-row product-table-border">
                            <div class="total-balju-price total-text">원청금액</div>
                            <div class="total-balju-symbol"></div>
                            <div class="total-calculate-price total-text">상품총액</div>
                            <div class="total-balju-symbol"></div>
                            <div class="total-calculate-price total-text">관리자할인</div>
                            <div class="total-balju-symbol"></div>
                            <div class="total-calculate-price total-text">할인</div>
                            <div class="total-balju-symbol"></div>
                            <div class="total-calculate-price total-text">포인트</div>
                        </div>
                        <div class="product-container">
                            <div class="product-table-row">
                                <div class="total-balju-price fw-bold">
                                    <input type="text" class="form-control " value="{{number_format($order->total_amount)}}" disabled>
                                    <input type="checkbox" class="form-check-input sale_amount" name="price_view_none" value="true" id="sale_amount" checked>
                                    <label for="sale_amount" class="form-check-label sale_amount_label">원청금액 표시 X</label>
                                </div>
                                <div class="total-balju-symbol fw-bold">=</div>
                                <div class="total-calculate-price">{{number_format($order->item->item_total_amount)}}</div>
                                <div class="total-balju-symbol fw-bold">-</div>
                                <div class="total-calculate-price">{{number_format($order->admin_discount)}}</div>
                                <div class="total-balju-symbol fw-bold">-</div>
                                <div class="total-calculate-price">{{number_format($order->discount_amount)}}</div>
                                <div class="total-balju-symbol fw-bold">-</div>
                                <div class="total-calculate-price">{{number_format($order->point_amount)}}</div>
                            </div>
                        </div>
                    </div>

                    <table class="table m-0 mt-1">
                        <tr>
                            <th>상품사진</th>
                            <td style="width: 600px"><input type="text" class="form-control" name="goods_url" value="{{$order->item->product->thumbnail}}"></td>
                            <td>
                                <button type="button" class="btn btn-outline-secondary ms-3 input_btn" data-bs-toggle="popover" data-bs-html="true" data-bs-trigger="hover focus"
                                        data-bs-placement="right" data-bs-content="<img src='{{ $order->item->product->thumbnail }}' alt='상품 사진' width='200px' height='250px'>" onclick="photo_popup('{{ $order->item->product->thumbnail }}');">이미지 확인</button>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 pt-2 px-5">
            <div class="card m-0">
                <div class="card-body py-2">
                    <table class="table m-0">
                        <tr>
                            <th>주문자</th>
                            <td><input type="text" class="form-control" name="orderer_name" value="{{$order->orderer_name?? ''}}" aria-label="orderer_name"></td>
                            <th>휴대전화</th>
                            <td><input type="text" class="form-control" name="orderer_phone" value="{{$order->orderer_phone?? ''}}" aria-label="orderer_phone"></td>
                            <th>일반전화</th>
                            <td><input type="text" class="form-control" name="orderer_tel" value="{{$order->orderer_tel?? ''}}" aria-label="orderer_tel"></td>
                        </tr>
                        <tr>
                            <th>받는분</th>
                            <td><input type="text" class="form-control" name="receiver_name" value="{{$order->delivery->receiver_name?? ''}}"></td>
                            <th>휴대전화</th>
                            <td><input type="text" class="form-control" name="receiver_phone" value="{{$order->delivery->receiver_phone?? ''}}"></td>
                            <th>일반전화</th>
                            <td><input type="text" class="form-control" name="receiver_tel" value="{{$order->delivery->receiver_tel?? ''}}"></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 pt-2 px-5">
            <div class="card m-0">
                <div class="card-body py-2">
                    <table class="table m-0">
                        <tr>
                            <th>배송일</th>
                            <td class="delivery_long_text">
                                <input type="date" class="form-control delivery_long_text datepicker" name="delivery_date" placeholder="YYYY-MM-DD" value="{{$order->delivery->delivery_date?? ''}}" >
                            </td>
                            <td class="delivery_short_text">
                                <input type="text" class="form-control ms-2 delivery_short_text" name="delivery_time" value="{{$order->delivery->delivery_time?? ''}}">
                            </td>
                        </tr>
                        <tr>
                            <th>배송주소</th>
                            <td class="delivery_long_text">
                                <input type="text" class="form-control delivery_addr_text" name="delivery_address" value="{{$order->delivery->delivery_address?? ''}}">
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 pt-2 px-5">
            <div class="card m-0">
                <div class="card-body">
                    <table class="table m-0">
                        <tr>
                            <th>경조사어(우)</th>
                            <td class="event_text"><input type="text" class="form-control event_text" name="delivery_ribbon_right" value="{{$order->delivery->delivery_ribbon_right?? ''}}"></td>
                            <th rowspan="2">카드</th>
                            <td rowspan="2"><textarea class="form-control event_textarea" name="delivery_card">{{$order->delivery->delivery_card?? ''}}</textarea></td>
                        </tr>
                        <tr>
                            <th>보내는분(좌)</th>
                            <td class="event_text"><input type="text" class="form-control event_text" name="delivery_ribbon_left" value="{{$order->delivery->delivery_ribbon_left?? ''}}"></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 pt-2 px-5">
            <div class="card m-0">
                <div class="card-body">
                    <table class="table m-0">
                        <tr>
                            <th rowspan="2">요구사항</th>
                            <td rowspan="2" class="memo"><textarea class="form-control shop_request" name="rw_custreq" aria-label="rw_custreq">{{$order->delivery->delivery_message?? ''}}</textarea></td>
                            <td class="td_checkbox">
                                <input type="checkbox" class="form-check-input form_checkbox" name="rw_dica" value="true" id="camera_check" checked>
                                <label for="camera_check" class="form-check-label" style="vertical-align: bottom">디카촬영</label>
                            </td>
                            <th style="width: 150px" class="text-start">담당자</th>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="td_checkbox" style="vertical-align: top">
                                <input type="checkbox" class="form-check-input form_checkbox" name="rw_happycall" value="true" id="happy_call" checked>
                                <label for="happy_call" class="form-check-label" style="vertical-align: top">해피콜</label>
                            </td>
                            <td>
                                <input type="text" class="form-check-input handler_text" name="handler" aria-label="handler" value="{{Auth::user()->name}}">
                            </td>
                            <td></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="submit_btn">
        <button type="button" class="btn btn-primary btn-lg m-3" onclick="balju_check(event);">발주하기</button>
    </div>
</form>
</div>
@endsection
@section('script')
    <script src="{{asset('assets/js/order/form-balju.js')}}"></script>
@endsection
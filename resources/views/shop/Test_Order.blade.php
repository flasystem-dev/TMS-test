@extends('layouts.master')
@section('title')
    테스트 주문
@endsection
@section('content')
    <div class="row justify-content-center mt-4" style="min-width: 800px;">
        <div class="col-10">
            <div class="card">
                <div class="card-body py-2">
                    <div class="row">
                        <div class="col-12 p-2">
                            <input type="radio" class="btn-check" name="menu_radio" id="send_talk" value="talk" autocomplete="off" checked>
                            <label class="btn btn-outline-primary" for="send_talk">TMS</label>
                            <input type="radio" class="btn-check" name="menu_radio" id="manege_channel" value="channel" autocomplete="off" >
                            <label class="btn btn-outline-warning ms-1" for="manege_channel">BMS</label>
                        </div>
                        <hr>
                    </div>
                    <div class="row" id="sub_menu">
                        <div class="col-12">
                            @include('Shop.include.Select_TMS_type')
                        </div>
                    </div>
                </div><!-- end card-body -->
            </div><!-- end card -->
        </div><!-- end col -->
    </div><!-- end row -->

    <div class="row justify-content-center">
        <div class="col-10">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-4">
                            <div class="input-group mb-3">
                                <label class="input-group-text" for="key">Key</label>
                                <input type="password" class="form-control" name="key" id="key" aria-label="key">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-4">
                            <div class="input-group mb-3">
                                <label class="input-group-text" for="od_name">주문자명</label>
                                <input type="text" class="form-control" name="od_name" id="od_name" aria-label="od_name" value="테스트">
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="input-group mb-3">
                                <label class="input-group-text" for="od_email">주문자 이메일</label>
                                <input type="text" class="form-control" name="od_email" id="od_email" aria-label="od_email" value="dev@flasystem.com">
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="input-group mb-3">
                                <label class="input-group-text" for="od_hp">주문자 핸드폰</label>
                                <input type="text" class="form-control" name="od_hp" id="od_hp" aria-label="od_hp" value="010-1111-1111">
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-4">
                            <div class="input-group mb-3">
                                <label class="input-group-text" for="od_b_name">수령인</label>
                                <input type="text" class="form-control" name="od_b_name" id="od_b_name" aria-label="od_b_name" value="테스트">
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="input-group mb-3">
                                <label class="input-group-text" for="od_b_hp">수령인 핸드폰</label>
                                <input type="text" class="form-control" name="od_b_hp" id="od_b_hp" aria-label="od_b_hp" value="010-2222-2222">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <div class="input-group mb-3">
                                <label class="input-group-text" for="od_b_addr1">배송 주소</label>
                                <input type="text" class="form-control" name="od_b_addr1" id="od_b_addr1" aria-label="od_b_addr1" value="부산광역시 중앙대로 623">
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="input-group mb-3">
                                <label class="input-group-text" for="od_b_addr2">상세 주소</label>
                                <input type="text" class="form-control" name="od_b_addr2" id="od_b_addr2" aria-label="od_b_addr2" value="601호 플라시스템">
                            </div>
                        </div>
                        <div class="col-4">
                            <input type="radio" class="btn-check" name="od_b_addr_jibeon" id="road" value="R" checked>
                            <label class="btn btn-outline-primary" for="road">도로명주소</label>
                            <input type="radio" class="btn-check" name="od_b_addr_jibeon" id="jibeon" value="O">
                            <label class="btn btn-outline-primary" for="jibeon">지번주소</label>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-4">
                            <div class="input-group mb-3">
                                <label class="input-group-text" for="od_cart_price">상품금액</label>
                                <input type="text" class="form-control" name="od_cart_price" id="od_cart_price" aria-label="od_cart_price" value="">
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="input-group mb-3">
                                <label class="input-group-text" for="od_receipt_price">소비자가</label>
                                <input type="text" class="form-control" name="od_receipt_price" id="od_receipt_price" aria-label="od_receipt_price" value="">
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="input-group mb-3">
                                <label class="input-group-text" for="od_misu">미수금액</label>
                                <input type="text" class="form-control" name="od_misu" id="od_misu" aria-label="od_misu" value="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
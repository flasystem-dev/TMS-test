@extends('layouts.master-without-nav')

@section('content')
<link href="{{ URL::asset('/assets/css/order/form-balju.css') }}" rel="stylesheet">
<div class="row">
    <div class="col-12 pt-3 px-5">
        <div class="card m-0">
            <div class="card-body">
                <h3>발주하기</h3>
                <hr>
                <table class="table m-0">
                    <tr>
                        <th>발주화원</th>
                        <td><input type="text" class="form-control"></td>
                        <td><input type="text" class="form-control"></td>
                    </tr>
                    <tr>
                        <th>수주화원</th>
                        <td><input type="text" class="form-control"></td>
                        <td><input type="text" class="form-control"></td>
                        <td><input type="text" class="form-control"></td>
                        <td><input type="text" class="form-control"></td>
                        <td><button type="button" class="btn btn-outline-secondary input_btn">수주화원 검색</button></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 pt-3 px-5">
        <div class="card m-0">
            <div class="card-body">
                <table class="table m-0">
                    <tr>
                        <th>상품명</th>
                        <td>
                            <select class="form-select">
                                <option value="">--- 선택 ---</option>
                            </select>
                        </td>
                        <td colspan="2"><input type="text" class="form-control"></td>
                        <th>수량</th>
                        <td><input type="text" class="form-control"></td>
                    </tr>
                    <tr>
                        <th>원청금액</th>
                        <td><input type="text" class="form-control"></td>
                        <td style="width: 100px;"><input type="text" class="form-control"></td>
                        <td><input type="checkbox" class="form-check-input sale_amount" id="sale_amount"><label for="sale_amount" class="form-check-label sale_amount_label">원청금액 표시 X</label></td>
                        <th>발주금액</th>
                        <td><input type="text" class="form-control"></td>
                    </tr>
                    <tr>
                        <th>상품사진</th>
                        <td colspan="3"><input type="text" class="form-control"></td>
                        <td><button class="btn btn-outline-secondary ms-3 input_btn">이미지 확인</button></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 pt-3 px-5">
        <div class="card m-0">
            <div class="card-body">
                <table class="table m-0">
                    <tr>
                        <th>주문자</th>
                        <td><input type="text" class="form-control"></td>
                        <th>휴대전화</th>
                        <td><input type="text" class="form-control"></td>
                        <th>일반전화</th>
                        <td><input type="text" class="form-control"></td>
                    </tr>
                    <tr>
                        <th>받는분</th>
                        <td><input type="text" class="form-control"></td>
                        <th>휴대전화</th>
                        <td><input type="text" class="form-control"></td>
                        <th>일반전화</th>
                        <td><input type="text" class="form-control"></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 pt-3 px-5">
        <div class="card m-0">
            <div class="card-body">
                <table class="table m-0">
                    <tr>
                        <th>배송일</th>
                        <td class="delivery_long_text"><input type="text" class="form-control delivery_long_text"></td>
                        <td class="delivery_short_text"><input type="text" class="form-control delivery_short_text"></td>
                    </tr>
                    <tr>
                        <th>배송주소</th>
                        <td class="delivery_long_text"><input type="text" class="form-control delivery_long_text"></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
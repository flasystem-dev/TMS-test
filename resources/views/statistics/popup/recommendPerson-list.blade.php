@extends('layouts.master-without-nav')
@section('title')
    추천인
@endsection
@section('content')
<link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('/assets/css/statistics/vendor-sales.css') }}" rel="stylesheet" type="text/css" />
    <div class="row">
        <div class="col-12">
            <!-- 상단 정보 -->
            <div class="card card-body">
                <div class="page_top">
                    <div class="sales_info">
                        <input type="hidden" name="recommend" value="{{$recommend}}">
                        <input type="hidden" name="brand" value="{{$brand}}">
                        <div>
                            <input type="radio" class="btn-check" name="dateType" id="tbl_date_type_order" value="order" autocomplete="off"  @if(request()->dateType==="order") checked @endif {{ isset(request()->dateType) ? "" : "checked"}}>
                            <label class="btn btn-outline-secondary btn-sm date_type" for="tbl_date_type_order">주문일 기준</label>
                            <input type="radio" class="btn-check" name="dateType" id="tbl_date_type_delivery" value="delivery" autocomplete="off" @if(request()->dateType==="delivery") checked @endif>
                            <label class="btn btn-outline-secondary btn-sm date_type" for="tbl_date_type_delivery">배송일 기준</label>
                        </div>
                        <div class="select_month_container">
                            <select class="form-control rounded select_year" name="select_year" id="select_year">
                                @for($i=date('Y'); $i>=date('Y')-2; $i--)
                                    <option value="{{$i}}" {{$year==$i ? "selected" : "" }}>{{$i}}</option>
                                @endfor
                            </select>
                            <select class="form-control rounded select_month" name="select_month" id="select_month">
                                @for($i=1; $i<=12; $i++)
                                    <option value="{{$i}}" {{$month==$i ? "selected" : ""}}>{{$i}}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <!-- 테이블 시작 -->
            <div class="card card-body">
                <div>
                    <button class="btn btn-primary btn-sm search_reset" onclick="reset_search()">전체 검색</button>
                    <table id="vendor_tbl" class="table table-striped">
                        <thead>
                        <tr>
                            <th class="tbl_th_{{$brand}}">번호</th>
                            <th class="tbl_th_{{$brand}}">브랜드</th>
                            <th class="tbl_th_{{$brand}}">상호</th>
                            <th class="tbl_th_{{$brand}}">사업자</th>
                            <th class="tbl_th_{{$brand}}">연락처</th>
                            <th class="tbl_th_{{$brand}}">추천인 수</th>
                            <th class="tbl_th_{{$brand}}">추천인</th>
                            <th class="tbl_th_{{$brand}}">주문건수</th>
                            <th class="tbl_th_{{$brand}}">매출금액</th>
                            <th class="tbl_th_{{$brand}}">가입일</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($vendors as $vendor)
                            <tr>
                                <td class="text-center"></td>
                                <td class="text-center">{{CommonCodeName($vendor->brand_type)}}</td>
                                <td class="text-center mall_name" style="max-width: 150px">{{$vendor->mall_name}}</td>
                                <td class="text-center fw-bold"><span class="possible_click" onclick="open_specifications({{$vendor->idx}})">{{$vendor->rep_name}}</span></td>
                                <td class="text-center">{{$vendor->rep_tel}}</td>
                                <td class="text-center"><span class="fw-bold possible_click" onclick="recommendPerson('{{$vendor->idx}}')">{{$vendor->recommend_count!==0 ? $vendor->recommend_count : ""}}</span></td>
                                <td class="text-center"><span class="fw-bold">{{$vendor->recommend_name}}</span>({{number_format($vendor->recommend_person_count)}}명)</td>
                                <td class="text-end fw-bold"><span class="possible_click" onclick="vendor_calendar('{{$vendor->idx}}')">{{$vendor->order_count!=0? number_format($vendor->order_count) : ""}}</span></td>
                                <td class="text-end fw-bold"><span class="possible_click" onclick="vendor_calendar('{{$vendor->idx}}')">{{$vendor->order_amount!=0? number_format($vendor->order_amount) : ""}}</span></td>
                                <td class="text-center">{{$vendor->registered_date}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{asset('assets/js/statistics/recommendPerson-list.js')}}"></script>
@endsection
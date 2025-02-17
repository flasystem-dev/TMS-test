@extends('layouts.master-without-nav')
@section('title')
    사업자 매출 달력
@endsection
@section('content')
    <link href="{{ URL::asset('/assets/css/statistics/vendor-salesCalendar.css') }}" rel="stylesheet" type="text/css" />

<div class="row">
    <div class="col-md-12 calendar_container">
        <div class="card card-body">
            <div class="row">
                <div class="col-12">
                    <h2 class="text-center">{{$rep_name}} 매출 달력</h2>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-12">
                    <div class="calendar_top">
                        <input type="hidden" name="vendor" value="{{$vendor}}">
                        <input type="hidden" name="selected_month" value="{{$selected_month}}">
                        <div class="monthly_sales_data">
                            <span class="month_sales_title">해당 월 총 매출</span>
                            <span id="monthly_sales_count">0</span>
                            <span class="me-2 month_sales_text">건</span>
                            <span id="monthly_sales_amount">0</span>
                            <span class="month_sales_text">원</span>
                        </div>
                        <div class="brand_search_container">
                            <input type="radio" class="btn-check" name="date_type" id="date_type_order" value="order" autocomplete="off" {{$dateType==="order" ? "checked" : ""}}>
                            <label class="btn btn-outline-secondary btn-sm date_type" for="date_type_order">주문일 기준</label>
                            <input type="radio" class="btn-check" name="date_type" id="date_type_delivery" value="delivery" autocomplete="off" {{$dateType==="delivery" ? "checked" : ""}}>
                            <label class="btn btn-outline-secondary btn-sm date_type" for="date_type_delivery">배송일 기준</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-12">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
    <script src='{{ URL::asset('/assets/libs/fullcalendar/fullcalendar.global.min.js') }}'></script>
    <script src="{{asset('assets/js/statistics/vendor-salesCalendar.js')}}"></script>
@endsection
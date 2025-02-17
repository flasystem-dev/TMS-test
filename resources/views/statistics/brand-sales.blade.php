@extends('layouts.master')
@section('title')
    브랜드 통계
@endsection
@section('content')
<link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('/assets/css/statistics/brand-sales.css') }}" rel="stylesheet" type="text/css" />
<div class="row">
    <div class="col-md-12">
        <!-- 매출 데이터테이블 -->
        <div class="card card-body">
            <div class="row">
                <div class="col-12">
                    <div class="brand_search_container">
                        <input type="radio" class="btn-check" name="tbl_date_type" id="tbl_date_type_order" value="order" autocomplete="off" checked>
                        <label class="btn btn-outline-secondary btn-sm date_type" for="tbl_date_type_order">주문일 기준</label>
                        <input type="radio" class="btn-check" name="tbl_date_type" id="tbl_date_type_delivery" value="delivery" autocomplete="off" >
                        <label class="btn btn-outline-secondary btn-sm date_type" for="tbl_date_type_delivery">배송일 기준</label>
                    </div>
                    <table id="sales_table" class="table">
                        <thead>
                        <tr>
                            <th>브랜드</th>
                            <th>당일 건수</th>
                            <th>당일 매출</th>
                            <th>당월 건수</th>
                            <th>당월 매출</th>
                            <th>금년 건수</th>
                            <th>금년 매출</th>
                            <th>작년 건수</th>
                            <th>작년 매출</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
        <!-- 매출 풀캘린더 -->
        <div class="card card-body">
            <div class="row">
                <div class="col-12">
                    <div class="brand_btn_container">
                        @foreach($brands as $brand)
                            <input type="radio" class="btn-check" name="brand_btns" id="brand_btn_{{$brand}}" value="{{$brand}}" autocomplete="off" @if($brand==="BTCP") checked @endif>
                            <label class="btn {{$brand}}" for="brand_btn_{{$brand}}">{{CommonCodeName($brand)}}</label>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-12">
                    <div class="calendar_top">
                        <div class="monthly_sales_data">
                            <span class="month_sales_title">해당 월 총 매출</span>
                            <span id="monthly_sales_count">10</span>
                            <span class="me-2 month_sales_text">건</span>
                            <span id="monthly_sales_amount">10,000</span>
                            <span class="month_sales_text">원</span>
                        </div>
                        <div class="brand_search_container">
                            <input type="radio" class="btn-check" name="date_type" id="date_type_order" value="order" autocomplete="off" checked>
                            <label class="btn btn-outline-secondary btn-sm date_type" for="date_type_order">주문일 기준</label>
                            <input type="radio" class="btn-check" name="date_type" id="date_type_delivery" value="delivery" autocomplete="off">
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
        <!-- 매출 차트 -->
        <div class="card card-body">
            <div class="row">
                <div class="col-12">
                    <div class="brand_btn_container">
                        @foreach($brands as $brand)
                            <input type="radio" class="btn-check" name="chart_brand_btns" id="chart_brand_btn_{{$brand}}" value="{{$brand}}" autocomplete="off" @if($brand==="BTCP") checked @endif>
                            <label class="btn {{$brand}}" for="chart_brand_btn_{{$brand}}">{{CommonCodeName($brand)}}</label>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-1">
                    <select class="form-select" name="chart_year">
                        <option value="{{date('Y')}}">{{date('Y')}}</option>
                        <option value="{{date('Y')-1}}">{{date('Y')-1}}</option>
                    </select>
                </div>
                <div class="col-11">
                    <div class="brand_search_container">
                        <input type="radio" class="btn-check" name="chart_date_type" id="chart_date_type_order" value="order" autocomplete="off" checked>
                        <label class="btn btn-outline-secondary btn-sm date_type" for="chart_date_type_order">주문일 기준</label>
                        <input type="radio" class="btn-check" name="chart_date_type" id="chart_date_type_delivery" value="delivery" autocomplete="off">
                        <label class="btn btn-outline-secondary btn-sm date_type" for="chart_date_type_delivery">배송일 기준</label>
                    </div>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-12">
                    <div>
                        {!! $chart->container() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
    <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/chart-js/chart.min.js') }}"></script>
    <script src='{{ URL::asset('/assets/libs/fullcalendar/fullcalendar.global.min.js') }}'></script>
    <script src="{{asset('assets/js/statistics/brand-sales.js')}}"></script>
    {!! $chart->script() !!}
    <script>
        // 차트 데이터 변경 API
        $('input[name="chart_brand_btns"], select[name="chart_year"], input[name="chart_date_type"]').on('change', function(){
            var param = chart_dataParam();
            var original_api_url = {{ $chart->id }}_api_url.split('?')[0];
            const full_url = `${original_api_url}?${param}`;
            {{ $chart->id }}_refresh(full_url);
        })
    </script>
@endsection
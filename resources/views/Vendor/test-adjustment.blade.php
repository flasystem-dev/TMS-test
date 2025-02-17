@extends('layouts.master')
@section('title')
    사업자목록
@endsection
@section('css')
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
@php
    $year = request()->year ?? now()->year;
    $month = request()->month ?? now()->month;
@endphp
@include('Vendor.Modal.calc-list-modal')
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
                <form method="get" action="{{ route('cal-list') }}" id="search_from">
                    <div class="row">
                        <div class="col-4 py-1">
                            <div class="input-group">
                                <a class="btn btn-light me-3 rounded px-5" href="">브랜드</a>
                                @php
                                    $brand = request()->brand ?? "BTCS";
                                @endphp
                                <div class="btn-group">
                                    <input type="radio" class="btn-check" name="brand" value="BTCS" id="brand_BTCS"   {{$brand==="BTCS"? "checked" : ""}} autocomplete="off">
                                    <label class="btn btn-outline-secondary" for="brand_BTCS">꽃파는사람들</label>
                                    <input type="radio" class="btn-check" name="brand" value="BTFCC" id="brand_BTFCC" {{$brand==="BTFCC"? "checked" : ""}} autocomplete="off">
                                    <label class="btn btn-outline-secondary" for="brand_BTFCC">플라체인 B2C</label>
                                    <input type="radio" class="btn-check" name="brand" value="BTFCB" id="brand_BTFCB" {{$brand==="BTFCB"? "checked" : ""}} autocomplete="off">
                                    <label class="btn btn-outline-secondary" for="brand_BTFCB">플라체인 B2B</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-3 py-1">
                            <div class="input-group">
                                <a class="btn btn-light me-3 rounded px-5" href="">정산년월</a>
                                <select class="form-control rounded mx-1" name="year" type="text" id="select_year" aria-label="word1">
                                    @for($i=date('Y')-2;$i<=date('Y');$i++)
                                        <option value="{{$i}}" {{$year==$i? "selected" : ""}} >{{$i}}년</option>
                                    @endfor
                                </select>
                                <select class="form-control rounded" name="month" type="text" id="select_month" aria-label="word1">
                                    @for($i=1;$i<13;$i++)
                                        <option value="{{$i}}" {{$month==$i? "selected" : ""}}>{{$i}}월</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="col-3 py-1">
                            <div class="input-group">
                                <div class="btn-group me-3">
                                    <button type="button" class="btn btn-light waves-effect" style="width: 110px;">
                                    <span id="sw_2_title">
                                        {{request()->sw_1_view ?? "전체"}}
                                    </span>
                                    </button>
                                    <input type="hidden" id="sw_1" name="sw_1" value="{{request()->sw_1 ?? "all"}}">
                                    <input type="hidden" id="sw_1_view" name="sw_1_view" value="{{request()->sw_1_view ?? "전체"}}">
                                    <button type="button" class="btn btn-light dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="mdi mdi-chevron-down"></i>
                                    </button>
                                    <div class="dropdown-menu" style="">
                                        <a class="dropdown-item" href="javascript:select_btn('sw_1','전체','all');">전체</a>
                                        <a class="dropdown-item" href="javascript:select_btn('sw_1','상호명','mall_name');">상호명</a>
                                        <a class="dropdown-item" href="javascript:select_btn('sw_1','대표자','rep_name');">대표자</a>
                                        <a class="dropdown-item" href="javascript:select_btn('sw_1','연락처','rep_tel');">연락처</a>
                                        <a class="dropdown-item" href="javascript:select_btn('sw_1','대표번호','gen_number');">대표번호</a>
                                        <a class="dropdown-item" href="javascript:select_btn('sw_1','DID','did_number');">DID</a>
                                    </div>
                                </div>
                                <input class="form-control rounded" name="word1" type="text" id="selectedName" value="" aria-label="word1">
                            </div>
                        </div>
                        <div class="col-2 py-1">
                            <button style="border-radius:3px;" class="btn btn-secondary waves-effect me-2">검색하기</button>
                            <button type="button" style="border-radius:3px;" class="btn btn-success waves-effect waves-light me-2" onclick="download_calc_excel()">Excel</button>
                            <button type="button" style="border-radius:3px;" data-bs-toggle="modal" data-bs-target="#excel_form_modal" class="btn btn-info waves-effect waves-light me-2">업로드</button>
                        </div>
                    </div>
                </form>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div><!-- end col -->
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card card-body pb-2 pt-3">
            <div class="row">
                <div class="col-12 px-3">
                    <table class="table table-borderless text-center">
                        <tr>
                            <td class="p-0"><b>총건수 합계</b></td>
                            <td class="p-0"><b>원청금액 합계</b></td>
                            <td class="p-0"><b>화훼금액 합계</b></td>
                            <td class="p-0"><b>옵션 합계</b></td>
                            <td class="p-0"><b>실제 지급액 합계</b></td>
                            <td class="p-0" width="14%" style="text-align: right;vertical-align: middle">
                                <button type="button" class="btn btn-sm btn-info waves-effect waves-light" onclick="calc_cardAmount()">카드금액계산</button>
                                <button type="button" class="btn btn-sm btn-warning waves-effect waves-light" onclick="open_specificationList()">명세서전송</button>
                            </td>
                        </tr>
                        <tr>
                            <!-- 원청금액 -->
                            <td class="fs-5 text-danger p-0">{{number_format($total_order_cnt)}}건</td>
                            <!-- 원청금액 -->
                            <td class="fs-5 text-danger p-0">{{number_format($total_order_amount)}}원</td>
                            <!-- 화훼금액 -->
                            <td class="fs-5 text-danger p-0">{{number_format($total_vendor_amount)}}원</td>
                            <!-- 옵션 -->
                            <td class="fs-5 text-danger p-0">{{number_format($total_option_amount)}}원</td>
                            <!-- 지급액 -->
                            <td class="fs-5 text-danger p-0">{{number_format($total_settlement_amount)}}원</td>
                            <td class="p-0"  width="14%" style="text-align: right;padding-top:10px !important;">
                                <div class="btn-group" >
                                    <input type="date" name="deposit_date" id="deposit_date" class="form-control datepicker" style="width:120px;height: 28px;text-align: center" placeholder="정산예정일">
                                    <button type="button"  class="btn btn-sm btn-primary waves-effect waves-light" id="spec_btn">명세서발급</button>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div><!-- end card -->
    </div>
</div>
<!-- row e -->


<!-- row e -->


<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                {{--datatable-buttons--}}
                <form name="specification_form" id="specification_form">
                @csrf
                <table id="datatable" class="table table-striped table-bordered cal-table" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                        <tr>
                            <th>브랜드</th>
                            <th>상호명</th>
                            <th>대표자<br>(동업자)</th>
                            <th>건수</th>
                            <th>원청<br>화훼</th>
                            <th>옵션</th>
                            <th>수수료</th>
                            <th data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="top" data-bs-content="원청금액 - 발주금액 - 옵션금액">
                                차액수익</th>
                            <th data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="top" data-bs-content="발주수익 + 차액수익 + 기타2 + 기타3 - 카드수수료" style="background-color: #ffb3b3">
                                수익총액</th>
                            <th data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="top" data-bs-content="(개인)소득세 3% + 지방소득세 0.3%">
                                원천징수<br>합계</th>
                            <th>서비스<br>이용료</th>
                            <th data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="top" data-bs-content="(꽃사)카드 결제액 3.3%">카드수수료<br>(카드총액)</th>
                            <th data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="top" data-bs-content="원천징수 + 서비스이용료 - 기타1" style="background-color: #c6c6ff">공제총액</th>
                            <th>기타(1)</th>
                            <th>기타(2)</th>
                            <th>기타(3)</th>
                            <th>입금예정일</th>
                            <th data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="top" data-bs-content="수익총액 - 공제총액" style="background-color: #bcffbc">
                                실제지급액</th>
                            <th>명세서<br><input type="checkbox" id="all_check" style="margin-left:33%"></th>
                         </tr>
                    </thead>
                        <input type="hidden" name="year" value="{{request()->year ?? date('Y')}}">
                        <input type="hidden" name="month" value="{{request()->month ?? date('m')}}">
                        <input type="hidden" name="brand" value="{{request()->brand ?? "BTCS"}}">
                        <input type="hidden" name="handler" value="{{Auth::user()->name}}">
                    <tbody>
                        @foreach($vendors as $vendor)

                            <tr class="text-center align-middle px-0">
                                <!-- 브랜드 -->
                                <td class="text-center">
                                    <p class="cal-p {{$vendor -> brand_type}}">
                                        @if($vendor->brand_type==='BTFCB')
                                            <b>플체B2B</b>
                                        @elseif($vendor->brand_type==='BTFCC')
                                            <b>플체B2C</b>
                                        @elseif($vendor->brand_type==='BTCS')
                                            <b>꽃사</b>
                                        @endif
                                    </p>
                                </td>
                                <!-- 상호명 -->
                                <td class="text-center nowrap-110" >
                                    <span class="cursor_p" onclick="vendorForm_popup({{$vendor->idx}})">{{$vendor->mall_name}}</span>
                                </td>
                                <!-- 연락처 -->
                                <td class="">
                                    {{$vendor->rep_name}}<br>
                                    {{$vendor->partner_name}}
                                </td>
                                <!-- 건수 -->
                                <td>
                                    {{number_format($vendor -> order_cnt ?? 0)}}
                                </td>
                                <!-- 원청금액 / 화훼금액 -->
                                <td data-order="{{$vendor -> order_amount()}}">
                                    {{number_format($vendor -> total_amount ?? 0)}}
                                    <br>
                                    {{number_format($vendor -> vendor_amount ?? 0)}}
                                </td>
                                <!-- 옵션금액 -->
                                <td>
                                    {{number_format($vendor -> vendor_options_amount ?? 0)}}
                                </td>
                                <!-- 수수료 -->
                                <td>
                                    {{$vendor -> service_percent ?? 0}} %
                                </td>
                                @php
                                    $profit = $vendor->vendor_amount / 100 * $vendor->service_percent;
                                    $difference = $vendor -> total_amount - $vendor -> vendor_amount - $vendor -> vendor_options_amount;

                                    $tax = 0;
                                    if($vendor->service_percent!==0 && $vendor->rep_type==='개인') {
                                        $tax_amount = floor(($profit/100*3)/10)*10;
                                        $tax = $tax_amount + floor(($tax_amount/10)/10)*10;
                                    }


                                    $card_amount = $vendor->service_percent===0 ? 0 : $vendor->card_amount ?? 0;
                                    $card_fee = ($vendor->service_percent===0 || $vendor->brand_type==="BTFCC" || $vendor->brand_type==="BTFCB") ? 0 : floor(($card_amount/100*3.3)/10)*10;

                                    $revenue = $profit + $difference + $vendor -> etc2 + $vendor -> etc3 - $card_fee;
                                    $deduction = $tax + ($vendor -> service_fee2(request()->year ?? date('Y'), request()->month ?? date('m'))) + $vendor -> etc1;
                                @endphp
                                <!-- 차액수익 -->
                                <td>
                                    {{number_format($difference)}}
                                </td>
                                <!-- 수익총액(기타1 제외) -->
                                <td style="background-color: #fff0f0">
                                    {{number_format($profit)}}
                                </td>
                                <!-- 원천징수합계 -->
                                <td>
                                    {{number_format($tax)}}
                                </td>
                                <!-- 서비스 이용료 -->
                                <td>
                                    {{number_format($vendor -> service_fee2(request()->year ?? date('Y'), request()->month ?? date('m')))}}
                                </td>
                                <!-- 카드 수수료 -->
                                <td class="cursor_p" onclick="reCalculate_cardAmount({{$year}}, {{$month}}, '{{$vendor->idx}}')">
                                    {{number_format($card_fee)}}
                                    <br>
                                    ({{number_format($card_amount)}})
                                </td>
                                <!-- 공제 총액 -->
                                <td style="background-color: #ebebff">
                                    {{number_format($deduction)}}
                                </td>
                                <!-- 기타1 -->
                                <td>
                                    {{number_format($vendor -> etc1?? 0)}}
                                </td>
                                <!-- 기타2 -->
                                <td>
                                    {{number_format($vendor -> etc2??0)}}
                                </td>
                                <!-- 기타3 -->
                                <td>
                                    {{number_format($vendor -> etc3??0)}}
                                </td>
                                <!-- 입금예정일 -->
                                <td>
                                    {{$vendor->deposit_date}}
                                </td>
                                <!-- 실제지급액 -->
                                <td style="background-color: #ebffeb">
                                    {{number_format($revenue - $deduction)}}
                                </td>
                                <td>
                                    @if($vendor->sp_order_cnt)
                                        <span onclick="specification_view({{$vendor->sp_id}});" class="badge bg-success-subtle text-success font-size-12">명세서</span>
                                    @else
                                        <input type="checkbox" name="mall_code[]" class="spec_check" value="{{$vendor->idx}}">
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                </form>
            </div>
        </div>
</div>
<!-- end row -->
<style>
#datatable{
    font-size:11px !important;
}
td {
    vertical-align: middle;
    text-align: center;
}
</style>
@endsection
@section('script')
    <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/vendor/calculate-vendorAmount.js') }}"></script>
@endsection






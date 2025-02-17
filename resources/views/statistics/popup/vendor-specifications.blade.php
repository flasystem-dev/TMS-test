@extends('layouts.master-without-nav')
@section('content')
    <link href="{{ URL::asset('/assets/css/statistics/vendor-specifications.css') }}" rel="stylesheet" type="text/css" />
    @php
        $year = request() -> year ?? date('Y');
    @endphp
    <div class="row">
        <div class="col-12">
            <div class="card card-body">
                <div class="row">
                    <div class="col-1  mb-3">
                        <select class="form-control rounded select_year" name="select_year" id="select_year">
                            @for($i=date('Y'); $i>=date('Y')-2; $i--)
                                <option value="{{$i}}" {{$year==$i ? "selected" : "" }}>{{$i}}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="offset-4 col-4">
                        <h2>{{$rep_name}} 정산내역</h2>
                    </div>
                </div>
                <div>
                    <div class="col-12">
                        <table class="table table-striped table-bordered table_border">
                            <thead>
                                <tr>
                                    <th colspan="2" class="text-center basic_text">정산일정</th>
                                    <th colspan="3" class="text-center sales_title">월 거래금액</th>
                                    <th colspan="5" class="text-center revenue_title">수익 내역</th>
                                    <th colspan="4" class="text-center deduction_title">공재 내역</th>
                                    <th colspan="2" class="text-center profit_title">지급액</th>
                                </tr>
                                <tr>
                                    <th>정산기준월</th>
                                    <th class="border_right">정산일</th>
                                    <th>주문총액</th>
                                    <th>발주총액</th>
                                    <th class="border_right">옵션총액</th>
                                    <th>발주수익</th>
                                    <th>차액수익</th>
                                    <th>카드수수료</th>
                                    <th>기타금액(1)</th>
                                    <th class="border_right">수익총액</th>
                                    <th>원천징수</th>
                                    <th>서비스이용료</th>
                                    <th>기타금액(2)</th>
                                    <th class="border_right">공제 총액</th>
                                    <th>실 지급액</th>
                                    <th>명세서</th>
                                </tr>

                            </thead>
                            <tbody>
                            @foreach($specifications as $row)
                                <tr>
                                    <td class="text-center">{{$row->year}}-{{str_pad($row ->month, 2, '0', STR_PAD_LEFT)}}</td>
                                    <td class="text-center border_right">{{$row->deposit_date}}</td>
                                    <td class="text-end">{{number_format($row->sp_pay_amount)}}</td>
                                    <td class="text-end">{{number_format($row->sp_vendor_amount)}}</td>
                                    <td class="text-end border_right">{{number_format($row->sp_total_option_price)}}</td>
                                    <td class="text-end">{{number_format($row->profit_amount)}}</td>
                                    <td class="text-end">{{number_format($row->difference_amount)}}</td>
                                    <td class="text-end">{{number_format($row->sp_card_charge_fee)}}</td>
                                    <td class="text-end">{{number_format($row->sp_etc2+$row->sp_etc3)}}</td>
                                    <td class="text-end border_right">{{number_format($row->revenue_amount)}}</td>
                                    <td class="text-end">{{number_format($row->totalTax)}}</td>
                                    <td class="text-end">{{number_format($row->sp_service_price_dc)}}</td>
                                    <td class="text-end">{{number_format(-$row->sp_etc1)}}</td>
                                    <td class="text-end border_right">{{number_format($row->deduction_amount)}}</td>
                                    <td class="text-end">{{number_format($row->sp_settlement_amount)}}</td>
                                    <td class="text-center"><button class="btn btn-primary btn-sm" onclick="view_specification('{{$row->id}}')">확인</button></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{asset('assets/js/statistics/vendor-specifications.js')}}"></script>
@endsection
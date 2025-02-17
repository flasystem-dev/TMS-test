@extends('layouts.master-without-nav')
@section('content')
<!-- 폼 양식 시작 -->
<form name="vendor_form" method="post" action="{{ url('vendor/fla-business') }}" enctype="multipart/form-data">
@csrf
@php
    use Carbon\Carbon;
@endphp
<!-- 페이지 내용 시작 -->
<div class="row justify-content-center ms-1">
    <div class="row mt-3">
        <div class="col-12">
            <div class="card pt-4">
                <h4 class="text-center p-2">[{{$sp->year}}년 {{$sp->month}}월 사업자 지급액 명세서]</h4>
                <table class="container">
                    <tr>
                        <td width="50%" colspan="2" rowspan="6" class="no-border center">
                            <p class="underline fs-4" >{{$sp->rep_name}} 귀하</p>
                            <p class="ft-11">입금 예정 계좌: {{$sp->getBankName($sp->bank_code)}} {{$sp->bank_number}} {{$sp->name_of_deposit}}</p>
                        </td>
                        <th> 발행처 </th>
                        <td class="center"> (주)플라시스템 사업지원팀 </td>
                    </tr>
                    <tr>
                        <th>E-MAIL</th>
                        <td class="center" >{{$sp->brand_type==="BTCS"? "fp@flasystem.com" : "flachain@flasystem.com"}}</td>
                    </tr>
                    <tr>
                        <th>TEL</th>
                        <td class="center" >{{$sp->brand_type==="BTCS"? "1877-8228" : "1811-2666"}}</td>
                    </tr>
                    <tr>
                        <th>수신처</th>
                        <td class="center" >{{$sp->rep_name}}</td>
                    </tr>
                    <tr>
                        <th>E-MAIL</th>
                        <td class="center" >{{$sp->rep_email}}</td>
                    </tr>
                    <tr>
                        <th>TEL</th>
                        <td class="center" >{{$sp->rep_tel}}</td>
                    </tr>
                    <tr>
                        <td colspan="4" class="no-border center">
                            <p>발행일자: {{Carbon::parse($sp->created_at)->format('Y-m-d')}}</p>
                            <p>서류 기준일: {{$sp->year}}-{{str_pad($sp->month, 2, '0', STR_PAD_LEFT)}}월 매출실적 기준</p>
                        </td>
                    </tr>
                    <tr>
                        <th colspan="2">월 화폐 거래 내역</th>
                        <th>금액(원)</th>
                        <th>비고</th>
                    </tr>
                    <tr>
                        <td class="center" colspan="2">ㄱ. 주문총액</td>
                        <td class="right">{{number_format($sp->sp_order_amount)}}</td>
                        <td class="center ft-11" >소비자가 당월 합계(옵션총액 포함)</td>
                    </tr>
                    <tr>
                        <td class="center" colspan="2">ㄴ. 발주총액</td>
                        <td class="right">{{number_format($sp->sp_vendor_amount)}}</td>
                        <td class="center ft-11" >발주가 당월 합계(옵션총액 미포함)</td>
                    </tr>
                    <tr>
                        <td  class="center"colspan="2">ㄷ. 옵션총액</td>
                        <td class="right">{{number_format($sp->sp_vendor_options_amount)}}</td>
                        <td class="center ft-11">옵션가(추가배송비,케잌,받침대 등) 당월 합계</td>
                    </tr>
                    <tr>
                        <th colspan="2">수익 내역</th>
                        <th>금액(원)</th>
                        <th>비고</th>
                    </tr>
                    <tr>
                        <td class="center" colspan="2">1. 발주수익</td>
                        <td class="right" >{{number_format($sp->profit_amount)}}</td>
                        <td class="center ft-11">발주총액의 {{$sp->sp_service_percent}}%</td>
                    </tr>
                    <tr>
                        <td class="center" colspan="2">2. 차액수익</td>
                        <td class="right">{{number_format($sp->difference_amount)}}</td>
                        <td class="center ft-11">주문총액 - 발주총액 - 옵션총액</td>
                    </tr>
                    <tr>
                        <td class="center" colspan="2">3. 카드결제수수료 3.3%</td>
                        <td class="right">-{{number_format($sp->sp_card_fee)}}</td>
                        <td class="center ft-11">당월 카드결제 총액 {{number_format($sp->sp_card_amount)}}원의 3.3%</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="center">4. 기타금액(1)</td>
                        <!-- 기타1 + 기타3 -->
                        <td class="right">{{number_format($sp->sp_etc2+$sp->sp_etc3)}}</td>
                        <td class="center ft-11">기타 / 할인금액 (당월 해제 조건 수익 등)</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="highlight center">A. 수익 총액</td>
                        <td class="highlight right">{{number_format($sp->revenue_amount)}}</td>
                        <td class="center ft-11"><b>합계(1+2+3+4)</b></td>
                    </tr>
                    <tr>
                        <th colspan="2">공제액</th>
                        <th>금액(원)</th>
                        <th>비고</th>
                    </tr>
                    <tr>
                        <td class="center" colspan="2">a. 수익 항목의 원천징수 3.3%</td>
                        <td class="right">{{number_format($sp->sp_tax_amount)}}</td>
                        <td class="center ft-11">소득세 3%, 주민세 0.3%</td>
                    </tr>

                    <tr>
                        <td class="center" colspan="2">b. 서비스 이용료</td>
                        <td class="right">{{number_format($sp->sp_service_fee)}}</td>
                        <td class="center ft-11">당월 서비스 이용료</td>
                    </tr>
                    <tr>
                        <td class="center" colspan="2">c. 기타 금액(2)</td>
                        <!-- 기타 2 -->
                        <td class="right">{{number_format(-$sp->sp_etc1)}}</td>
                        <td class="center ft-11">기타 지출금</td>
                    </tr>
                    <tr>
                        <td  colspan="2" class="highlight center">B. 공제 총액</td>
                        <!-- 원천징수 + 서비스 금액 + 기타2 -->
                        <td class="highlight right">{{number_format($sp->deduction_amount)}}</td>
                        <td class="center ft-11"><b>합계(a+b+c)</b></td>
                    </tr>

                </table>
                <div class=" p-2 center ft-11">
                    [실 지급액 = 수익총액 - 공제총액]<br/>
                    전 월 상황에 따라 변동될 수 있으며, 특이사항이 있을 경우 반드시 연락 부탁드립니다.
                </div>
                <table class="container">
                    <tr>
                        <th>수익총액(A)</th>
                        <td class="right">{{number_format($sp->revenue_amount)}}</td>
                        <th>공제총액(B)</th>
                        <td class="right">{{number_format($sp->deduction_amount)}}</td>
                    </tr>
                    <tr>
                        <td colspan="3" class="highlight center">실 지급액 (A-B)</td>
                        <td class="highlight right">{{number_format($sp->sp_settlement_amount)}}</td>
                    </tr>
                </table>
                <div class=" p-2 center ft-11">
                    <p class="highlight">※ {{$sp->deposit_date}} 입금 예정입니다.</p>
                </div>
            </div>
        </div>
        @if(Auth::user()->auth === 6 || Auth::user()->auth === 10)
        <div class="text-end">
            <button type="button" class="btn btn-danger" onclick="location.href='{{url('/vendor/specification-form/edit'."/".$sp->id)}}'">수정 페이지로</button>
        </div>
        @endif
    </div>
</div> <!-- 내용 끝 ( col ) -->
</form>
@endsection
<style>
    #page-topbar{
        left:0 !important;
    }
</style>
@section('script')

@endsection

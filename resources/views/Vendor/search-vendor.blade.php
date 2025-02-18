@extends('layouts.master-without-nav')

@section('content')
    <link href="{{ URL::asset('/assets/css/vendor/search-vendor.css') }}" rel="stylesheet" type="text/css" />
<div class="row my-2">
    <div class="col-12 pt-2 px-5">
        <div class="card m-0">
            <div class="card-body py-2">
                <form action="?" method="GET">
                <div class="search_select">
                    <select class="form-select" name="search_ctgy" aria-label="search_ctgy">
                        <option value="all">- 통합검색 -</option>
                        <option value="mall_name"    {{request()->search_ctgy == 'mall_name'? 'selected':''}}>상점명</option>
                        <option value="rep_name"     {{request()->search_ctgy == 'rep_name'? 'selected':''}}>대표자</option>
                        <option value="rep_tel"      {{request()->search_ctgy == 'rep_tel'? 'selected':''}}>대표자연락처</option>
                        <option value="partner_name" {{request()->search_ctgy == 'partner_name'? 'selected':''}}>동업자</option>
                        <option value="partner_tel"  {{request()->search_ctgy == 'partner_tel'? 'selected':''}}>동업자연락처</option>
                        <option value="gen_number"   {{request()->search_ctgy == 'gen_number'? 'selected':''}}>대표번호</option>
                        <option value="did_number"   {{request()->search_ctgy == 'did_number'? 'selected':''}}>DID</option>
                    </select>
                </div>
                <div class="search_text">
                    <input type="text" class="form-control" name="search_word" aria-label="search_word" value="{{request()->search_word ?? ""}}">
                </div>
                <div class="search_btn">
                    <button class="btn btn-primary">검색</button>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_valid" id="is_valid" value="N" {{request()->is_valid=="N"? "checked" : ""}}>
                    <label class="form-check-label" for="is_valid">비활성 사업자 포함</label>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12 pt-2 px-5">
        <div class="card m-0">
            <div class="card-body py-2">
                <div id="vendor_table">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>번호</th>
                                <th style="width: 90px;">브랜드<br>상점명</th>
                                <th>대표자<br>연락처</th>
                                <th>동업자<br>연락처</th>
                                <th>대표번호<br>DID</th>
                                <th>보증종류<br>잔액</th>
                                <th>가입상태<br>탈퇴일</th>
                                <th>특이사항</th>
                                <th>선택</th>
                            </tr>
                        </thead>
                        <tbody>
                        @if($vendors -> items())
                        @foreach($vendors as $vendor)
                            <tr>
                                <!-- 번호 -->
                                <td>{{$vendor->idx}}</td>
                                <!-- 브랜드 / 상점명 -->
                                <td>
                                    <p class="brand_type {{$vendor->brand_code()}}">{{$vendor -> brand_ini()}}</p>
                                    <p class="brand_type" style="margin-top: 3px" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="right" data-bs-content="{{$vendor->mall_name?? ""}}">{{empty($vendor->mall_name)? "없음": $vendor->mall_name}}</p>
                                </td>
                                <!-- 대표자 / 연락처 -->
                                <td style="width: 120px">
                                    <p class="gs_name" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="right" data-bs-content="{{$vendor->rep_name?? ""}}">{{$vendor->rep_name}}</p>
                                    {{$vendor->rep_tel}}
                                </td>
                                <!-- 동업자 / 연락처 -->
                                <td style="width: 120px">
                                    <p class="gs_name">{{$vendor->partner_name}}</p>
                                    {{$vendor->partner_tel}}
                                </td>
                                <!-- 대표번호 / DID -->
                                <td style="width: 100px">
                                    <p class="gs_tel">{{$vendor->gen_number}}</p>
                                    {{$vendor->did_number}}
                                </td>
                                <!-- 보증종류 / 잔액 -->
                                <td style="width: 100px">
                                    <p class="gs_tel">{{CommonCodeName($vendor->assurance)}}</p>
                                    {{number_format($vendor->possible_misu())}}
                                </td>
                                <!-- 가입상태 / 탈퇴일 -->
                                <td style="width: 100px">
                                    <p class="gs_tel {{$vendor->is_valid=="N"? 'text-danger':''}}">{{$vendor->is_valid=="Y"?"활성":"비활성"}}</p>
                                    {{$vendor->service_ex_date}}
                                </td>
                                <!-- 특이사항 -->
                                <td>
                                    <pre class="vendor_memo" data-bs-container="body" data-bs-html="true" data-bs-toggle="popover" data-bs-trigger="focus hover" data-bs-placement="bottom" data-bs-content="<pre>{{$vendor->vendor_memo}}</pre>">{{$vendor->vendor_memo}}</pre>
                                </td>
                                <!-- 선택 -->
                                <td style="width: 80px;">
                                    <input type="radio" class="btn-check" name="select_check" data-id="{{$vendor->idx}}" id="select_check{{$loop->index}}" autocomplete="off">
                                    <label class="btn btn-outline-success btn-sm mb-2" for="select_check{{$loop->index}}">선택</label>
{{--                                    <button type="button" class="btn btn-outline-secondary btn-sm">주문</button>--}}
                                </td>
                            </tr>
                        @endforeach
                        @else
                            <tr>
                                <td colspan="8">
                                    <p style="padding: 50px; font-size: 20px; font-weight: bold">사업자가 없습니다.</p>
                                </td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>
                {{ $vendors -> links() }}
            </div>
        </div>
    </div>
</div>
<div id="order_table"></div>

@endsection
@section('script')
    <script src="{{asset('assets/js/vendor/search-vendor.js')}}"></script>
@endsection
@extends('layouts.master')
@section('title')
    오픈마켓 계정 정보
@endsection
@section('content')
@include('shop.openMarket.modal.openMarket-account-modal')
<link href="{{ URL::asset('/assets/css/account.css') }}" rel="stylesheet">

<div class="row justify-content-center mt-3" style="min-width: 800px;">
    <div class="col-12">
        <div class="card">
            <div class="card-body py-2">
                <div class="row">
                    <div class="col-12 my-3 text-end">
                        <button type="button" class="btn btn-lg btn-outline-secondary me-4" onclick="check_account_info();">상태 업데이트</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th style="width: 170px">브랜드</th>
                                    <th style="width: 160px">채널</th>
                                    <th>아이디</th>
                                    <th>비밀번호</th>
                                    <th style="width: 200px">별칭 (플레이오토)</th>
                                    <th style="width: 5%">변경</th>
                                    <th style="width: 5%">상태</th>
                                    <th style="width: 5%">보기</th>
                                    <th>수정일</th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                @foreach($mall_list as $mall)
                                    <tr>
                                        @php $brand = DB::table('common_code') -> where('code', '=', $mall -> brand_type_code) -> first(); @endphp
                                        <!-- 브랜드 -->
                                        <td class="text-start">
                                            <span class="brand_type {{ $mall -> brand_type_code }} p-2 text-center" data-bs-toggle="modal" data-bs-target="#admin_memo_modal" data-bs-mall="{{ $mall -> mall_code }}" data-bs-brand="{{ $mall -> brand_type_code }}" data-bs-memo="{{ $mall -> admin_memo }}" style="cursor: pointer">{{ $brand -> code_name }}</span>
                                            @if(!empty($mall -> admin_memo))
                                                <i class="uil-clipboard-notes text-info ms-1" data-bs-trigger="hover focus" data-bs-container="body" data-bs-html="true" data-bs-toggle="popover" data-bs-placement="right" data-bs-content="{{ $mall -> admin_memo }}"></i>
                                            @endif
                                        </td>
                                        <!-- 채널 -->
                                        <td><span class="brand_type {{ $mall -> mall_code }} p-2" onclick="open_admin_url('{{ $mall -> admin_url }}')" style="cursor: pointer">{{ $mall -> mall}}</span></td>
                                        <!-- 아이디 -->
                                        <td><input type="text" class="form-control"  aria-label="site_id" value="{{ $mall -> site_id }}"></td>
                                        <!-- 비밀번호 -->
                                        @if(Auth::user() -> auth < 9)
                                            <td><input type="password" class="form-control" id="index{{ $loop->index }}" aria-label="site_pw" value="{{ $mall -> site_pw }}"></td>
                                        @else
                                            <td><input type="text" class="form-control" id="index{{ $loop->index }}" aria-label="site_pw" value="{{ $mall -> site_pw }}"></td>
                                        @endif
                                        <!-- 별칭 -->
                                        <td><input type="text" class="form-control"  aria-label="site_pw" value="{{ $mall -> site_nick }}"></td>
                                        <!-- 변경 -->
                                        <td><button type="button" class="btn btn-outline-primary" data-brand="{{ $mall -> brand_type_code }}" data-mall="{{ $mall -> mall_code }}" onclick="update_account_info(event, '{{ Auth::user() -> name }}')">수정</button></td>

                                        <!-- 상태 -->
                                        @if($mall -> account_status === '성공')
                                            <td><button type="button" class="btn btn-success">{{ $mall -> account_status }}</button></td>
                                        @else
                                            <td><button type="button" class="btn btn-danger" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="right" data-bs-content="{{ $mall -> account_memo }}">{{ $mall -> account_status }}</button></td>
                                        @endif
                                        <!-- 보기 -->
                                        <td>
                                            <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#show_pw_modal" data-list="index{{ $loop->index }}">보기</button>
                                        </td>
                                        <!-- 수정일 -->
                                        <td>
                                            <p class="m-0" style="vertical-align: middle">{{ $mall -> update_at }}</p>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div><!-- row end -->
            </div><!-- card body end -->
        </div><!-- card end -->
    </div>
</div>
@endsection
@section('script')
    <script src="{{ URL::asset('/assets/js/shop/openMarket_Account.js') }}"
@endsection

@extends('layouts.master')
@section('title')
    공지사항
@endsection
@section('content')
    <div class="row mt-2" style="min-width: 800px;">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="get" action="{{ route('vendor-list') }}">
                        <div class="row">
                            <div class="col-6 py-1">
                                <div class="input-group">
                                    <a class="btn btn-light me-3 rounded px-5" href="">브랜드</a>
                                    <div class="btn-group">
                                        <input type="radio" class="btn-check" name="type" value="BTCS" id="BTCS" autocomplete="off">
                                        <label class="btn btn-outline-secondary" for="BTCS">꽃파는사람들</label>
                                        <input type="radio" class="btn-check" name="type" value="BTFCC" id="BTFCC" autocomplete="off">
                                        <label class="btn btn-outline-secondary" for="BTFCC">플라체인 B2C</label>
                                        <input type="radio" class="btn-check" name="type" value="BTFCB" id="BTFCB" autocomplete="off">
                                        <label class="btn btn-outline-secondary" for="BTFCB">플라체인 B2B</label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-4 py-1">
                                <div class="input-group">
                                    <div class="btn-group me-3">
                                        <button type="button" class="btn btn-light waves-effect" style="width: 110px;">
                                                <span id="sw_1_title">
{{--                                                    @if (isset($search_arr['sw_1']))--}}
{{--                                                        {{$search_arr['sw_1_view']}}--}}
{{--                                                    @else--}}
                                                        조회 항목
{{--                                                    @endif--}}
                                                </span>
                                        </button>
                                        <input type="hidden" id="sw_1" name="sw_1" value="">
                                        <input type="hidden" id="sw_1_view" name="sw_1_view" value="">
                                        <button type="button" class="btn btn-light dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="mdi mdi-chevron-down"></i>
                                        </button>
                                        <div class="dropdown-menu" style="">
                                            <a class="dropdown-item" href="javascript:select_btn('sw_1','조회 항목','');">조회 항목</a>
                                            <a class="dropdown-item" href="javascript:select_btn('sw_1','브랜드','brand_type');">제목</a>
                                            <a class="dropdown-item" href="javascript:select_btn('sw_1','대표자','rep_name');">내용</a>
                                            <a class="dropdown-item" href="javascript:select_btn('sw_1','연락처','rep_tel');">작성자</a>
                                        </div>
                                    </div>
                                    <input class="form-control rounded" name="word1" type="text" id="selectedName" aria-label="word1">
                                </div>
                            </div>
                            <div class="col-2 pt-1">
                                <button style="border-radius:3px;" class="btn btn-secondary waves-effect waves-light me-2">검색하기</button>
                                <button style="border-radius:3px;" type="button" class="btn btn-secondary waves-effect waves-light" onclick="add_vendor();">+ 공자사항 작성</button>
                            </div>
                        </div>
                    </form>
                </div><!-- end card-body -->
            </div><!-- end card -->
        </div><!-- end col -->
    </div>

    <div class="row mt-2" style="min-width: 800px;">
        <div class="col-12">
            <div class="card">
                <div class="card-body py-2">
                    <div class="row">

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
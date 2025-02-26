@extends('layouts.master-without-nav')
@section('css')
    <link href="{{ URL::asset('/assets/libs/dropzone/dropzone.min.css') }}" rel="stylesheet" type="text/css" />
    <script src="{{ URL::asset('/assets/js/dropzone/dropzone.js') }}"></script>
@endsection
@section('title')
   배너 정보
@endsection
@section('content')
    <link href="{{ URL::asset('/assets/css/shop/banner-popup.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('/assets/libs/summernote/summernote-lite.min.css') }}" rel="stylesheet" type="text/css" />
    <script src="{{ URL::asset('/assets/libs/summernote/summernote-lite.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/summernote/lang/summernote-ko-KR2.js') }}"></script>
    <form id="popup_form" name="banner_form" action="{{ url('/shop/popupSave') }}" method="post" enctype="multipart/form-data">
    <header id="page-topbar">
        <div class="navbar-header">
            <div class="d-flex text-xl-center">
                <p class="font-size-20 fw-bold mt-3 mx-4" style="text-align:center" >팝업등록
                </p>
            </div>
            <div class="col-8 gap-3" style="text-align: right;">
                <button type="submit" class="btn btn-primary waves-effect waves-light w-md">저장</button>
            </div>
        </div>
    </header>
<div class="row justify-content-center ms-1 mt-5">
    <div class="row mt-3">
        <div class="col-12 p-4">
                @csrf
                <div class="row">
                    <div class="col-7">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <h4 class="card-title">팝업 이미지</h4>
                                        <div>
                                            <div class="dropzone" id="dropzone_product_img" style="height:545px;">
                                                <div class="fallback">
                                                    <input name="file" type="file" multiple="multiple">
                                                </div>
                                                <div class="dz-message needsclick">
                                                    <div class="mb-3">
                                                        <i class="display-4 text-muted uil uil-cloud-upload"></i>
                                                    </div>
                                                    <h4>여기에 팝업이미지를 올려주세요.</h4>
                                                </div>
                                            </div>
                                            <input type="hidden" name="temp_img">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-5">
                        <div class="card">
                            <div class="card-body">
                                <div class="col-6 mb-3">
                                    <h4 class="card-title">사용여부</h4>
                                    <div class="form-switch fs-5">
                                        <input class="form-check-input" name="use_yn"  id="use_yn" type="checkbox" role="switch" value="Y" aria-label="use_yn" checked>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <h4 class="card-title">브랜드</h4>
                                        <select class="form-select" name="brand" id="brand">
                                            <option value="BTCP">꽃파는총각</option>
                                            <option value="BTCC">칙칙폭폭플라워</option>
                                            <option value="BTSP">사팔플라워</option>
                                            <option value="BTBR">바로플라워</option>
                                            <option value="BTOM">오만플라워</option>
                                            <option value="BTCS">꽃파는사람들</option>
                                            <option value="BTFC">플라체인</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row  mb-3">
                                    <div class="col-12">
                                        <h4 class="card-title">도메인</h4>
                                        <select class="form-select" name="domain" id="domain">

                                        </select>
                                    </div>
                                </div>
                                <div class="row  mb-3">
                                    <div class="col-12">
                                        <h4 class="card-title">우선 순위</h4>
                                        <select class="form-select" name="orderBy" id="orderBy">
                                            @for($i=1; $i<=5; $i++)
                                                <option value="{{$i}}" {{$i===5? 'selected':''}}>{{$i}}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                                <div class="row  mb-3">
                                    <div class="col-12">
                                        <h4 class="card-title">제목</h4>
                                        <input type="text" class="form-control" name="title">
                                    </div>
                                </div>
                                <div class="row  mb-3">
                                    <div class="col-12">
                                        <h4 class="card-title">표출기간</h4>
                                        <div class="input-group mb-3">
                                            <input type="date" class="form-control datepicker" placeholder="시작일" name="start_date" autocomplete="off">
                                            <input type="date" class="form-control datepicker" placeholder="종료일" name="end_date" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <div class="row  mb-3">
                                    <div class="col-12">
                                        <h4 class="card-title">링크</h4>
                                        <input type="text" class="form-control" name="link">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<style>
    #page-topbar{
        left:0 !important;
    }
    .authentication-bg{
        min-height: 450px !important;
    }
</style>
@endsection
@section('script')
    <script src="{{ URL::asset('/assets/js/shop/bannerPopup.js') }}"></script>

@endsection
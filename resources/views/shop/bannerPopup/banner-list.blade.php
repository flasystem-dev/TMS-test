@extends('layouts.master')
@section('title')
    배너목록
@endsection
@section('content')
<link href="{{ URL::asset('/assets/css/shop/banner-popup.css') }}" rel="stylesheet" type="text/css" />
@if(session('update'))
    <script>showToast('수정 완료')</script>
@endif
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="get" name="brand_select" action="?">
                    <div class="row">
                        <div class="col-10 ">
                            @foreach($company as $com)
                                <input type="checkbox" class="btn-check brand_type_check"  name="brand[]" id="brand_{{ $com -> brand_type_code }}" value="{{ $com -> brand_type_code }}" autocomplete="off" @if(isset($search['brand']) && in_array($com -> brand_type_code, $search['brand'])) checked @endif>
                                <label class="btn brand_type {{ $com -> brand_type_code }}" for="brand_{{ $com -> brand_type_code }}"> {{ $com -> brand_ini }} </label>
                            @endforeach
                        </div>
                        <div class="col-2 text-end">
                            <div class="row">
                                <div class="col-12">
                                    <button type="button" style="border-radius:3px;" onclick="bannerForm()" class="btn btn-secondary waves-effect waves-light me-2">배너등록</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div><!-- end card-body -->
        </div><!-- end card -->

        <div class="col-12">
            <div class="card">
                <div class="row">
                    <div class="col-12 px-4 py-3">
                        <table id="" class="table table-striped table-bordered " style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                            <tr class="px-0">
                                <th style="width: 80px"></th>
                                <th style="width: 80px">번호</th>
                                <th style="width: 80px">브랜드</th>
                                <th style="width: 150px">도메인</th>
                                <th>배너</th>
                                <th style="">링크</th>
                                <th style="width: 100px">우선순위</th>
                                <th style="width: 80px;">사용여부</th>
                                <th style="width:100px">관리</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($banners as $row)
                                <tr class="text-center align-middle px-0">
                                    <td class="">
                                        <input type="checkbox" aria-label="products">
                                    </td>
                                    <td>
                                        {{$loop->index+1}}
                                    </td>
                                    <!-- 브랜드 -->
                                    <td>
                                        <p class="brand_type {{$row->brand}}">{{BrandAbbr($row->brand)}}</p>
                                    </td>
                                    <!-- 도메인 -->
                                    <td>
                                        <p>{{$row->domain==="basic"? "전체":$row->domain}}</p>
                                    </td>
                                    <!-- 배너 -->
                                    <td>
                                        <img src="{{$row->photo}}" style="height:100px;width:auto;">
                                    </td>
                                    <!-- 링크 -->
                                    <td>
                                        {{$row->link}}
                                    </td>
                                    <!-- 우선순위 -->
                                    <td>
                                        <select class="form-select orderBy_select" data-index="{{$row->id}}" data-type="banner">
                                            @for($i=1; $i<=5; $i++ )
                                                <option value="{{$i}}" {{$i===$row->orderBy ? "selected" : ""}}>{{$i}}</option>
                                            @endfor
                                        </select>
                                    </td>
                                    <!-- 사용여부 -->
                                    <td>
                                        <div class="form-switch">
                                            <input class="form-check-input check_used" type="checkbox" value="Y" data-index="{{$row->id}}" data-type="banner" {{$row->use_yn==='Y'? "checked":""}}>
                                        </div>
                                    </td>
                                    <!-- 삭제 -->
                                    <td class="center">
                                        <div class="center flex-wrap gap-2">
                                            <button class="btn btn-danger btn-soft-danger btn-sm delete_btn" data-index="{{$row->id}}" data-type="banner">삭제</button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div><!-- end col -->

@endsection
@section('script')
    <script src="{{ URL::asset('/assets/js/shop/bannerPopupList.js') }}"></script>
@endsection
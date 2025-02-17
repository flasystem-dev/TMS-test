@extends('layouts.master-without-nav')
@section('title')
    @lang('권한 부족')
@endsection
@section('content')
    <div class="my-5 pt-sm-5">
        <div class="container">

            <div class="row">
                <div class="col-md-12">
                    <div class="text-center">
                        <div>
                            <div class="row justify-content-center">
                                <div class="col-sm-4">
                                    <div class="error-img">
                                        <img src="{{ URL::asset('/assets/images/logo-dark.png') }}" alt=""
                                             class="img-fluid mx-auto d-block">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <h4 class="text-uppercase mt-4">권한이 없습니다.</h4>
                        <p class="text-muted">(주)플라시스템 개발팀에 문의하세요.</p>
                        <div class="mt-5">
                            <a class="btn btn-primary waves-effect waves-light" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">로그 아웃</a>
                        </div>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
@extends('layouts.master-without-nav')
@section('title')
    @lang('translation.Error_404')
@endsection
@section('content')
    @if(session('message'))
        <script>
            alert('{{ session('message') }}');
        </script>
    @endif

    <div class="my-5 pt-sm-5">
        <div class="container">

            <div class="row">
                <div class="col-md-12">
                    <div class="text-center">
                        <div>
                            <div class="row justify-content-center">
                                <div class="col-sm-4">
                                    <div class="error-img">
                                        <img src="{{ URL::asset('/assets/images/404-error.png') }}" alt=""
                                            class="img-fluid mx-auto d-block">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <h4 class="text-uppercase mt-4">페이지를 찾을 수 없습니다.</h4>
                        <p class="text-muted">다시 시도하거나 다른 페이지를 찾아 보세요</p>
                        <div class="mt-5">

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

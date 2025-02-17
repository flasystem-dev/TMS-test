@extends('layouts.master')
@section('title')
    개발 부서
@endsection
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card card-body">
            <div class="row">
                <div class="col-4">
                    <div class="input-group mb-3">
                        <input type="file" class="form-control" name="orderPayment" id="orderPayment" aria-label="orderPayment">
                        <button type="button" class="btn btn-secondary" onclick="upload_excel('orderPayment');" style="width: 150px">OrderPayment</button>
                    </div>
                </div>
                <div class="offset-6 col-2">
                    <div class="input-group mb-3">
                        <button type="button" class="btn btn-danger" onclick="update_commonCode()">CommonCode</button>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-4">
                    <div class="input-group mb-3">
                        <input type="file" class="form-control" name="user" id="user" aria-label="user">
                        <button type="button" class="btn btn-secondary" onclick="upload_excel('user');" style="width: 150px">User</button>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    <div class="input-group mb-3">
                        <input type="file" class="form-control" name="vendor" id="vendor" aria-label="vendor">
                        <button type="button" class="btn btn-secondary" onclick="upload_excel('vendor');" style="width: 150px">Vendor</button>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    <div class="input-group mb-3">
                        <button type="button" class="btn btn-primary" onclick="statistics_url()">URL 통계 계산</button>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    <div class="input-group mb-3">
                        <button type="button" class="btn btn-primary" onclick="user_test()">Test</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
    <script src="{{asset('assets/js/dev/dev-part.js')}}"></script>
    <script>
        function user_test() {
            $.ajax({
                url: '/etc/get-user-info',
                method: 'GET',
                success: function(response) {
                    console.log(response.user); // 현재 로그인한 사용자 정보
                },
                error: function(error) {
                    console.log(error.responseJSON.message); // 에러 메시지
                }
            });
        }
    </script>
@endsection
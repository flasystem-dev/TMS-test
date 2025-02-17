@extends('layouts.master')
@section('title')
    테스트존
@endsection

@section('css')

@endsection


@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="input-group mb-3">
                                <form action="{{ url('ETC/Test') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="file" name="files" class="form-control" id="file_data">
                                    <button type="submit" class="input-group-text" >Upload</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        {{--function send_file() {--}}
        {{--    var file = $('#file_data')[0].files[0];--}}

        {{--    var formData = new FormData();--}}

        {{--    formData.append('files', file);--}}
        {{--    formData.append('handler', '{{ Auth::user() -> name }}');--}}
        {{--    $.ajax({--}}
        {{--        url: main_url + "/api/ETC/Test",--}}
        {{--        method: "POST",--}}
        {{--        data: formData,--}}
        {{--        processData: false,  // false =>  formData를 string으로 변환하지 않음--}}
        {{--        contentType: false,  // false =>  헤더가 multipart/form-data로 전송--}}
        {{--        cache: false,--}}
        {{--        success: function(data) {--}}
        {{--            alert('성공');--}}
        {{--        },--}}
        {{--        error: function(e) {--}}
        {{--            alert('실패');--}}
        {{--            console.log(e)--}}
        {{--        }--}}
        {{--    })--}}
        {{--}--}}
    </script>
@endsection
@extends('layouts.master-without-nav')
@section('css')
    <link href="{{ URL::asset('/assets/libs/dropzone/dropzone.min.css') }}" rel="stylesheet" type="text/css" />
    <script src="{{ URL::asset('/assets/js/dropzone/dropzone.js') }}"></script>
    <link href="{{ URL::asset('/assets/css/board/board-register.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('title')
   {{$type->title}}
@endsection
@section('content')


<form id="board_form" name="board_form" action="{{ url('/Board/board-save') }}" method="post" enctype="multipart/form-data">
    <input type="hidden" name= 'type' value="{{$type->type}}">
<header id="page-topbar">
    <div class="navbar-header">
        <div class="d-flex text-xl-center" style="flex:3;">
            <p class="font-size-20 fw-bold mt-3 mx-4" style="text-align:center"> {{$type->title}}</p>
        </div>
        <div class="text-end pe-5" style="flex:1;">
            <input type="radio" class="btn-check" name="is_used" value="1" id="is_used" {{optional($board)->is_used? "checked" : ""}}>
            <label class="btn btn-outline-secondary w-md" for="is_used">사용</label>

            <input type="radio" class="btn-check" name="is_used" value="0" id="is_notUsed" {{optional($board)->is_used? "" : "checked"}}>
            <label class="btn btn-outline-secondary w-md" for="is_notUsed">미사용</label>
        </div>
        <div class="gap-3" style="text-align: right;" style="flex:1;">
            @isset($board->id)
                <input type="hidden" value="{{$board->id}}" name="id">
                <button type="submit" class="btn btn-primary waves-effect waves-light w-md">수정</button>
            @else
                <button type="submit" class="btn btn-primary waves-effect waves-light w-md">저장</button>
            @endisset
        </div>
    </div>
</header>
<div class="row justify-content-center ms-1 mt-5">
    <div class="row mt-3">
        <div class="col-12 p-4">
                @csrf
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <h4 class="card-title">브랜드</h4>
                                        <select class="form-select" name="brand" id="brand" aria-label="brand">
                                            @foreach($company as $com)
                                                <option value="{{$com->brand_type_code}}" {{optional($board)->brand==$com->brand_type_code ? "selected" : ""}}>{{$com->shop_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @if($type->type=='event')
                                    <div class="col-6 event_area">
                                        <h4 class="card-title">이벤트 기간</h4>
                                        <input type="date" class="form-control datepicker event_period" name="start_period" aria-label="start_period" value="{{optional($board)->start_period}}">
                                        <span>~</span>
                                        <input type="date" class="form-control datepicker event_period" name="end_period" aria-label="end_period" value="{{optional($board)->end_period}}">
                                    </div>
                                    @endif
                                </div>
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <h4 class="card-title">글 제목</h4>
                                        <input type="text" class="form-control" name="title" value="@isset($board->title){{$board->title}} @endisset">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <h4 class="card-title">내용</h4>
                                        <div id="contents_area"></div>
                                        <textarea class="d-none" id="contents" name="contents" aria-label="contents">@isset($board->contents){{$board->contents}} @endisset</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 mb-3">
                                        @isset($board->id)<p> * 파일을 첨부하면 이전 파일은 삭제됩니다.</p>@endisset
                                        <h4 class="card-title">첨부파일1 @isset($board->contents)<i class="uil-download-alt"></i>{{$board->file1_name}}@endisset</h4>
                                        <input type="file" class="form-control" name="file1" value="{{$board->file1_name}}">
                                    </div>
                                    <div class="col-12">
                                        <h4 class="card-title">첨부파일2 @isset($board->contents)<i class="uil-download-alt"></i>{{$board->file2_name}}@endisset</h4>
                                        <input type="file" class="form-control" name="file2">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<style>
    #page-topbar{
        left:0 !important;
    }
    body[data-sidebar-size=sm]{
        min-height: 1000px;
    }
</style>
@endsection
@section('script')
    <link href="{{ URL::asset('/assets/libs/summernote/summernote-lite.min.css') }}" rel="stylesheet" type="text/css" />
    <script src="{{ URL::asset('/assets/libs/summernote/summernote-lite.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/summernote/lang/summernote-ko-KR2.js') }}"></script>
    <script>
        $('.datepicker').datepicker();
        $(document).ready(function() {
            $('#contents').summernote({
                height: 300,                 // 에디터 높이
                minHeight: null,             // 최소 높이
                maxHeight: null,             // 최대 높이
                focus: true,                  // 에디터 로딩후 포커스를 맞출지 여부
                lang: "ko-KR",					// 한글 설정
                callbacks: {
                    onImageUpload : function(files) {
                        sendFile(files[0], this);
                    }
                }
            });
        });
        function sendFile(file, editor) {
            data = new FormData();
            data.append("file", file);
            $.ajax({
                url: main_url+'/api/Shop/fileUpload',
                type: "POST",
                data: data,
                cache: false,
                contentType: false,
                processData: false,
                success: function(url) {
                    // editor.insertImage(welEditable, url);
                    $(editor).summernote('insertImage', url, function ($image) {
                        $image.attr('src', url);
                    });
                }
            });
        }
        $("#board_form").on("submit", function (e) {
            if(confirm("저장하시겠습니까?")) {
                e.preventDefault();
                $.ajax({
                    type: "POST",
                    url: $(this).prop("action"),
                    data: new FormData(this),
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function (data) {
                        if(data==='success'){
                            alert('저장되었습니다.');
                            window.close();
                        }
                    }, error: function (error) {
                        alert("[에러발생] 개발팀에 문의하세요.");
                        console.log(error);
                    },
                });
            }
        });
    </script>
@endsection
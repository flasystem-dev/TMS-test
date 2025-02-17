@extends('layouts.master-without-nav')
@section('css')
    <link href="{{ URL::asset('/assets/libs/dropzone/dropzone.min.css') }}" rel="stylesheet" type="text/css" />
    <script src="{{ URL::asset('/assets/js/dropzone/dropzone.js') }}"></script>
@endsection
@section('title')
   FAQ 등록
@endsection
@section('content')


<form id="faq_form" name="faq_form" action="{{ url('/Board/faq-save') }}" method="post" enctype="multipart/form-data">
<header id="page-topbar">
    <div class="navbar-header">
        <div class="d-flex text-xl-center">
            <p class="font-size-20 fw-bold mt-3 mx-4" style="text-align:center"> FAQ 등록 </p>
        </div>
        <div class="col-8 gap-3" style="text-align: right;">
            @isset($faq->id)
                <input type="hidden" value="{{$faq->id}}" name="id">
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
                                    <div class="col-12">
                                        <h4 class="card-title">브랜드</h4>
                                        <select class="form-select" name="brand" id="brand">
                                            <option value="BTCP" @isset($faq->brand) @if($faq->brand=='BTCP') selected @endif @endisset>꽃파는총각</option>
                                            <option value="BTCC" @isset($faq->brand) @if($faq->brand=='BTCC') selected @endif @endisset>칙칙폭폭플라워</option>
                                            <option value="BTSP" @isset($faq->brand) @if($faq->brand=='BTSP') selected @endif @endisset>사팔플라워</option>
                                            <option value="BTBR" @isset($faq->brand) @if($faq->brand=='BTBR') selected @endif @endisset>바로플라워</option>
                                            <option value="BTOM" @isset($faq->brand) @if($faq->brand=='BTOM') selected @endif @endisset>오만플라워</option>
                                            <option value="BTCS" @isset($faq->brand) @if($faq->brand=='BTCS') selected @endif @endisset>꽃파는사람들</option>
                                            <option value="BTFC" @isset($faq->brand) @if($faq->brand=='BTFC') selected @endif @endisset>플라체인</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row  mb-3">
                                    <div class="col-12">
                                        <h4 class="card-title">글 제목</h4>
                                        <input type="text" class="form-control" name="title" value="@isset($faq->title){{$faq->title}} @endisset">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <h4 class="card-title">Question</h4>
                                        <div id="question_area"></div>
                                        <textarea class="form-control" id="question" name="question" aria-label="question">@isset($faq->question){{$faq->question}} @endisset</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <h4 class="card-title">Answer</h4>
                                        <div id="question_area"></div>
                                        <textarea class="d-none" id="answer" name="answer" aria-label="answer">@isset($faq->answer){{$faq->answer}} @endisset</textarea>
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
    .authentication-bg{
        min-height: 400px !important;
    }
</style>
@endsection
@section('script')
    <link href="{{ URL::asset('/assets/libs/summernote/summernote-lite.min.css') }}" rel="stylesheet" type="text/css" />
    <script src="{{ URL::asset('/assets/libs/summernote/summernote-lite.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/summernote/lang/summernote-ko-KR2.js') }}"></script>
    <script>

        function remove_bbs(id) {
            if(confirm("정말 삭제하시겠습니까?")) {
                $.ajax({
                    url: main_url + "/api/Shop/Product/"+ id,
                    type: 'delete',
                    success: function(data) {
                        alert(data);
                        location.reload();
                    },
                    error: function(error) {
                        alert('[에러발생]개발팀에 문의하세요.');
                        console.log(error);
                    }
                })
            }
        }

        $(document).ready(function() {
            // $('#question').summernote({
            //     height: 200,                 // 에디터 높이
            //     minHeight: null,             // 최소 높이
            //     maxHeight: null,             // 최대 높이
            //     focus: true,                  // 에디터 로딩후 포커스를 맞출지 여부
            //     lang: "ko-KR",					// 한글 설정
            //     callbacks: {
            //         onImageUpload : function(files) {
            //             sendFile(files[0], this);
            //         }
            //     }
            // });
            $('#answer').summernote({
                height: 200,                 // 에디터 높이
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
        $("#faq_form").on("submit", function (e) {
            if(confirm("저장하시겠습니까?")) {
                var $this = $(this).parent();
                e.preventDefault();
                $.ajax({
                    type: "POST",
                    url: $(this).prop("action"),
                    data: new FormData(this),
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function (data) {
                        console.log("dd");
                        if(data==='success'){
                            alert('저장되었습니다.');
                            window.close();
                        }
                    }, error: function (error) {
                        alert("dd");
                        console.log(error);
                    },
                });
            }
        });
    </script>
@endsection
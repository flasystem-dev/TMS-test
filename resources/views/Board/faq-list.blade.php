@extends('layouts.master')
@section('title')
    FAQ 목록
@endsection
@section('content')
    <link href="{{ URL::asset('/assets/libs/summernote/summernote-lite.min.css') }}" rel="stylesheet" type="text/css" />
    <script src="{{ URL::asset('/assets/libs/summernote/summernote-lite.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/summernote/lang/summernote-ko-KR2.js') }}"></script>

    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="get" name="brand_select" action="?">
                    <div class="row">
                        <div class="col-4">
                            @foreach($company as $com)
                                <input type="checkbox" class="btn-check brand_type_check"  name="brand[]" id="brand_{{ $com -> brand_type_code }}" value="{{ $com -> brand_type_code }}" autocomplete="off" @if(isset($search['brand']) && in_array($com -> brand_type_code, $search['brand'])) checked @endif>
                                <label class="btn brand_type {{ $com -> brand_type_code }}" for="brand_{{ $com -> brand_type_code }}"> {{ $com -> brand_ini }} </label>
                            @endforeach
                        </div>
                        <div class="col-3">
                            <div class="input-group">
                                <span class="input-group-text" style="cursor: pointer" data-bs-toggle="dropdown" aria-expanded="false">제목</span>
                                <input type="text" class="form-control" name="pr_name" aria-label="pr_name" value="">
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="row">
                                <div class="col-12">
                                    <button type="submit" style="border-radius:3px;" class="btn btn-secondary waves-effect waves-light me-2">검색하기</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-2 text-end">
                            <div class="row">
                                <div class="col-12">
                                    <button type="button" style="border-radius:3px;" onclick="faqForm()" class="btn btn-secondary waves-effect waves-light me-2">FAQ 등록</button>
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
                                <th style="">번호</th>
                                <th style="">브랜드</th>
                                <th>제목</th>
                                <th style="">조회수</th>
                                <th style="width:8%">관리</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($faqs as $row)
                                <tr class="text-center align-middle px-0">
                                    <td>
                                        {{$loop->index+1}}
                                    </td>
                                    <!-- 브랜드 -->
                                    @php
                                        $com = App\Models\CodeOfCompanyInfo::where('brand_type_code', $row -> brand) -> first();
                                    @endphp
                                    <td>
                                        <p class="brand_type {{$com->brand_type_code}}">{{$com->brand_ini}}</p>
                                    </td>
                                    <!-- 상품명 -->
                                    <td>
                                        {{$row->title}}
                                    </td>
                                    <!-- 기본가 / 발주가 -->
                                    <td>
                                        {{$row->view}}
                                    </td>
                                    <!-- 삭제 -->
                                    <td class="center">
                                        <div class="center flex-wrap gap-2">
                                            <button class="btn btn-success btn-soft-success btn-sm" onclick="faq_update({{$row->id}});">수정</button>
                                            <button class="btn btn-danger btn-soft-danger btn-sm" onclick="faq_del({{$row->id}});">삭제</button>
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
    <button class="fixed_btn" data-bs-toggle="modal" data-bs-target="#add_product_modal"><i class="uil-box fixed_btn_icon"></i><span class="fixed_btn_text ms-1">+ 상품 추가</span></button>
@endsection
@section('script')
    <script>
        function faq_update(id){
            const url = main_url + "/Board/faq-form?id="+id;
            open_win(url, 'Form', 1100, 800, 100, 100);

        }
        function faq_del(id) {
            if(confirm("정말 삭제하시겠습니까?")) {
                $.ajax({
                    url: main_url + "/api/Shop/faqDel/"+ id,
                    success: function(data) {
                        if(data=='success'){
                            alert('삭제가 완료되었습니다.');
                        }else{
                            alert('삭제 실패 - 개발팀에 문의하세요.');
                        }
                    },
                    error: function(error) {
                        alert('[에러발생]개발팀에 문의하세요.');
                        console.log(error);
                    }
                })
            }
        }
        function faqForm(){
            const url = main_url + "/Board/faq-form";
            open_win(url, 'Form', 1100, 800, 100, 100);
        }
    </script>
@endsection
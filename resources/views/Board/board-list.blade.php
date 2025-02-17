@extends('layouts.master')
@section('title')
    {{$board->title}}
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
                                <input type="checkbox" class="btn-check brand_type_check"  name="brand[]" id="brand_{{ $com -> brand_type_code }}" value="{{ $com -> brand_type_code }}" autocomplete="off" @if(isset(request()->brand) && in_array($com -> brand_type_code, request()->brand)) checked @endif>
                                <label class="btn brand_type {{ $com -> brand_type_code }}" for="brand_{{ $com -> brand_type_code }}"> {{ $com -> brand_ini }} </label>
                            @endforeach
                        </div>
                        <div class="col-3">
                            <div class="input-group">
                                <span class="input-group-text" style="cursor: pointer" data-bs-toggle="dropdown" aria-expanded="false">제목</span>
                                <input type="text" class="form-control" name="title" aria-label="title" value="{{ request()->title ?? ''}}">
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
                                    <button type="button" style="border-radius:3px;" onclick="boardForm('{{$board->type}}', 0)" class="btn btn-secondary waves-effect waves-light me-2">{{$board->title}} 등록</button>
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
                                <th style="width: 5%">번호</th>
                                <th style="width: 6%">브랜드</th>
                                <th>제목</th>
                                <th style="width: 12%">첨부파일1</th>
                                <th style="width: 12%">첨부파일2</th>
                                <th style="width: 6%">조회수</th>
                                <th style="width: 6%">관리</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($list as $row)
                                <tr class="text-center align-middle px-0">
                                    <td>
                                        {{$loop->index+1}}
                                    </td>
                                    <!-- 브랜드 -->
                                    @php
                                        $com = App\Models\CodeOfCompanyInfo::select('brand_ini')->where('brand_type_code', $row -> brand) -> first();
                                    @endphp
                                    <td>
                                        <p class="brand_type {{$row -> brand}}">{{$com->brand_ini}}</p>
                                    </td>
                                    <td onclick="boardForm('{{$board->type}}', {{$row->id}})" style="cursor: pointer">
                                        {{$row->title}}
                                    </td>
                                    <td>
                                        <a href="{{ url('Board/downloadFile') }}?file_name={{$row->file1_name}}&file_path={{$row->file1_path}}">{{$row->file1_name}}</a>
                                    </td>
                                    <td>
                                        <a href="{{ url('Board/downloadFile') }}?file_name={{$row->file2_name}}&file_path={{$row->file2_path}}">{{$row->file2_name}}</a>
                                    </td>
                                    <td>
                                        {{$row->hit}}
                                    </td>
                                    <td class="center">
                                        <div class="center flex-wrap gap-2">
                                            <button class="btn btn-danger btn-soft-danger btn-sm" onclick="board_del({{$row->id}});">삭제</button>
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
    <script>
        function board_del(id) {
            if(confirm("정말 삭제하시겠습니까?")) {
                $.ajax({
                    url: main_url + "/api/Shop/boardDel/"+ id,
                    success: function(data) {
                        if(data=='success'){
                            alert('삭제가 완료되었습니다.');
                            location.reload(true);
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

        function boardForm(type, id){
            const url = main_url + "/Board/boardForm/"+type+"/"+id;
            open_win(url, 'Form', 1100, 800, 100, 100);
        }

        $(document).ready(function(){
            $('.brand_type_check:checkbox').change(function () {
                document.brand_select.submit();
            });
        });

    </script>
@endsection
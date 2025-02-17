@extends('layouts.master-without-nav')
<style>
    body {
        background-color: #f5f6f8;
    }
</style>

@section('content')
@inject('DB', 'Illuminate\Support\Facades\DB')



<div class="row m-2">
    <div class="col-12 card" id="memo_contents">
        <div class="row m-1 fs-5">
            <div class="col-12">
                <span class=""><i class="uil-clipboard-notes"></i> &nbsp;자주 쓰는 메모</span>
            </div>
        </div>
        @foreach($memo_list as $memo)

            <div class="row bg-light rounded-2 m-1" style="font-size: 16px;">
                <div class="text-center border-end" style="width: 12%;">
                    <span>{{ $memo -> orderby }}</span>
                </div>
                <div class="ps-2 border-end" style="width: 73%;">
                    <span>{{ $memo -> note }}</span>
                </div>
                <div class="pe-1 ps-2" style="width: 15%;">
                    <button class="dripicons-gear rounded py-1 btn btn-outline-secondary memo_btns"></button>
                    <button class="dripicons-trash rounded py-1 btn btn-outline-secondary memo_btns" data-idx="{{ $memo -> idx }}"></button>
                </div>
            </div>


            <div class="row bg-light m-1 rounded-2 d-none" style="font-size: 16px;">
                <div class="px-1" style="width: 12%;">
                    <select class="form-select form-select-sm" data-old="{{ $memo -> orderby }}" style="margin-top: 1px;">
                        @for($i=1; $i<21; $i++)
                            <option value="{{ $i }}" @if($memo -> orderby == $i) selected @endif>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="px-0" style="width: 73%;">
                    <input type="text" class="form-control" style="height: 31px;" data-old="{{ $memo -> note }}" value="{{ $memo -> note }}">
                </div>
                <div class="pe-1 ps-2" style="width: 15%;">
                    <button class="far fa-save rounded py-1 btn btn-outline-secondary memo_btns" data-idx="{{ $memo -> idx }}"></button>
                    <button class="fas fa-times rounded py-1 btn btn-outline-secondary memo_btns" style="margin-top: 1px;"></button>
                </div>
            </div>

        @endforeach

        <div class="row text-center fs-1 my-2" id="add_memo_btn">
            <div class="offset-4 col-4">
                <button class="btn btn-outline-secondary py-1 px-3" onclick="add_memo_btn();">추가</button>
            </div>
        </div>
        <div class="row text-center fs-1 d-none" id="add_memo_icon">
            <div class="col-12">
                <i class="dripicons-arrow-up"></i>
            </div>
        </div>
        <div class="row bg-light m-1 rounded-2 d-none" style="font-size: 18px;" id="add_memo_input">
            <div class="px-1" style="width: 12%;">
                <select class="form-select form-select-sm" name="insert_memo_select" style="margin-top: 1px;">
                    @for($i=1; $i<21; $i++)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </select>
            </div>
            <div class="px-0" style="width: 73%;">
                @csrf
                <input type="text" class="form-control" name="insert_memo_input" style="height: 31px;" required>
            </div>
            <div class="pe-1 ps-2" style="width: 15%;">
                <button class="fas fa-plus rounded py-1 btn btn-outline-secondary memo_btns" onclick="insert_memo();"></button>
                <button class="fas fa-minus rounded py-1 btn btn-outline-secondary memo_btns" onclick="add_memo_cancel(event);"></button>
            </div>
        </div>
    </div>
</div>


<script>

    // 수정 버튼(기어)
    $('.dripicons-gear').on('click', function(e){
       let read = $(this).parent().parent();
       $(read).addClass('d-none');
       $(read).next().removeClass('d-none');
    });

    // 수정 취소 버튼( X )
    $('.fa-times').on('click', function(e){
        let write = $(this).parent().parent();
        $(write).addClass('d-none');
        $(write).prev().removeClass('d-none');

        let input = $(this).parent().prev().children('input:eq(0)')
        $(input).val($(input).data('old'));
        let select = $(this).parent().prev().prev().children('select:eq(0)');
        let idx = $(select).data('old') - 1;
        $(select).children().eq(idx).prop('selected', true);
    })

    // 추가 버튼 ( insert input )
    function add_memo_btn(){
        $('#add_memo_icon').removeClass('d-none');
        $('#add_memo_input').removeClass('d-none');
        $('#add_memo_btn').addClass('d-none');
    }

    // 추가 취소 버튼 ( - )
    function add_memo_cancel(e){
        var target = e.target;
        $('#add_memo_icon').addClass('d-none');
        $('#add_memo_input').addClass('d-none');
        $('#add_memo_btn').removeClass('d-none');
        $(target).parent().prev().children('input:eq(0)').val('');
        $(target).parent().prev().prev().children().children().eq(0).prop('selected', true);
    }

    // 추가 등록 버튼 ( + )
    function insert_memo(){
        $.ajax({
            type : 'post',
            url: '{{ route('memo-insert') }}',
            data : {
                'brand_type_code' : '{{ $brand }}',
                'note' : $('input[name="insert_memo_input"]').val(),
                'orderby' : $('select[name="insert_memo_select"]').val()
            },
            success : function(data) {
                if(data == 'success'){
                    alert('등록 완료');
                    location.reload();
                }
            },
            error : function(error){
                alert('에러 발생!');
                console.log(error)
            }
        });
    }

    // 메모 삭제 버튼 ( 휴지통 )
    $('.dripicons-trash').on('click', function(e){
       let idx = $(this).data('idx');
       if(confirm("삭제 하시겠습니까 ?")) {
           $.ajax({
               type : 'delete',
               url: '{{ route('memo-delete') }}',
               headers: {
                   'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
               },
               data : {
                   'idx' : idx,
               },
               success : function(data) {
                   if(data == 'success'){
                       alert('삭제 완료');
                       location.reload();
                   }
               },
               error : function(error){
                   alert('에러 발생!');
                   console.log(error)
               }
           });
       }
    });

    $('.fa-save').on('click', function(e){
        let idx = $(this).data('idx');
        let input = $(this).parent().prev().children('input:eq(0)').val();
        let select = $(this).parent().prev().prev().children().find(':selected').val();

        if(confirm("수정 하시겠습니까?")) {
            $.ajax({
                type : 'put',
                url: '{{ route('memo-update') }}',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data : {
                    'idx' : idx,
                    'note' : input,
                    'orderby' : select
                },
                success : function(data) {
                    if(data == 'success'){
                        alert('수정 완료');
                        location.reload();
                    }
                },
                error : function(error){
                    alert('에러 발생!');
                    console.log(error)
                }
            });
        }
    });
</script>
@endsection
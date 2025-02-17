@extends('layouts.master-without-nav')
<style>
    body {
        background-color: #f5f6f8;
    }
    .col-12 .form-select { width: 50%; height: 30px; padding: 5px 15px; line-height: 16px; }
</style>
@section('content')
@include('sms.modal.sms-memo-modal')
<div class="row m-2">
    <div class="col-12 card" id="memo_contents">
        <div class="row m-1 fs-5">
            <div class="col-12">
                <span class=""><i class="uil-clipboard-notes"></i> &nbsp;자주 쓰는 SMS 메세지</span>
            </div>
        </div>
        <div class="row my-2">
            <div class="col-12">
                <select class="form-select" id="memo_brand">
                    <option value="BTCP" @if(request()->brand==="BTCP") selected @endif>꽃파는총각</option>
                    <option value="BTCC" @if(request()->brand==="BTCC") selected @endif>칙칙폭폭플라워</option>
                    <option value="BTSP" @if(request()->brand==="BTSP") selected @endif>사팔플라워</option>
                    <option value="BTBR" @if(request()->brand==="BTBR") selected @endif>바로플라워</option>
                    <option value="BTOM" @if(request()->brand==="BTOM") selected @endif>오만플라워</option>
                    <option value="BTCS" @if(request()->brand==="BTCS") selected @endif>꽃파는사람들</option>
                    <option value="BTFC" @if(request()->brand==="BTFC") selected @endif>플라체인</option>
                </select>
            </div>
        </div>
        @foreach($memo_list as $memo)

            <div class="row bg-light rounded-2 m-1" style="font-size: 16px;">
                <div class="text-center border-end" style="width: 12%;">
                    <span>{{ BrandAbbr($memo -> brand) }}</span>
                </div>
                <div class="ps-2 border-end" style="width: 73%;">
                    <span>{{ $memo -> msg }}</span>
                </div>
                <div class="pe-1 ps-2" style="width: 15%;">
                    <button class="dripicons-gear rounded py-1 btn btn-outline-secondary memo_btns" data-bs-toggle="modal" data-bs-target="#msg_form" data-id="{{ $memo -> id }}"></button>
                    <button class="dripicons-trash rounded py-1 btn btn-outline-secondary memo_btns" data-idx="{{ $memo -> id }}"></button>
                </div>
            </div>
        @endforeach
        <div class="row text-center fs-1 my-2" id="add_memo_btn">
            <div class="offset-4 col-4">
                <button class="btn btn-outline-secondary py-1 px-3" data-bs-toggle="modal" data-bs-target="#msg_form" data-id="0">추가</button>
            </div>
        </div>
    </div>
</div>


<script>
// 메시지 수정
document.getElementById('msg_form').addEventListener('show.bs.modal', event => {
    var id = event.relatedTarget.dataset.id;

    if(id==='0') {
        $('#modal_brand').val("BTCP").change();
        $('#modal_msg').val('');
        $('#modal_id').val(id);
    }else {
        $.ajax({
            url: '{{url('SMS/memo/manage/note')}}',
            method: "GET",
            data: { 'id':id },
            success: function(data) {
                $('#modal_brand').val(data.brand).change();
                $('#modal_msg').val(data.msg);
                $('#modal_id').val(id);
            },
            error: function(e) {
                alert('문제발생');
                console.log(e)
            }
        })
    }
});

// 메모 삭제 버튼 ( 휴지통 )
$('.dripicons-trash').on('click', function(e){
    if(confirm("삭제 하시겠습니까 ?")) {
        $.ajax({
            type : 'delete',
            url: '{{ url('SMS/memo/manage/note') }}',
            data : {
                'id' : $(this).data('idx'),
            },
            success : function(data) {
                if(data){
                    alert('삭제 완료');
                    location.reload();
                }else {
                    alert('삭제 실패')
                }
            },
            error : function(error){
                alert('에러 발생!');
                console.log(error)
            }
        });
    }
});

function upsert_msg() {
    const formData = new FormData(document.getElementById('upsert_form'));

    $.ajax({
        url : '{{ url('SMS/memo/manage/note') }}',
        method : "POST",
        data : formData,
        async: false,
        processData: false,
        contentType: false,
        success: function(data) {
            if(data){
                alert('저장 완료');
                location.reload();
            }else{
                alert("저장 실패");
            }
        },
        error: function(e) {
            alert('저장 실패');
            console.log(e)
        }
    })
}

$('#memo_brand').on('change', function(){
    var brand = $(this).val();

    var currentUrl = new URL(window.location.href);
    var params = currentUrl.searchParams;

    params.set('brand', brand);

    window.location.href = currentUrl.toString();
});

</script>
@endsection
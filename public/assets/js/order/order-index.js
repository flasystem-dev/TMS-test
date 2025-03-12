var fix=1;
var isClicked = false;

$(document).ready(function () {
    $('#start_date').datepicker();
    $('#end_date').datepicker();
});

// 모두 => 동기화
function allCheckFunc( obj ) {
    $("[name=order_idx]").prop("checked", $(obj).prop("checked") );

    if($(obj).is(':checked')){
        $("[name=order_idx]").closest('tr').addClass('orders_checked');
    }else {
        $("[name=order_idx]").closest('tr').removeClass('orders_checked');
    }
}
// 체크박스 체크시 전체선택 체크 여부
function oneCheckFunc( obj )
{
    var allObj = $("[name=checkAll]");
    var objName = $(obj).attr("name");
    var tr = $(obj).closest('tr');

    if( $(obj).prop("checked") )
    {
        $(tr).addClass('orders_checked');
        checkBoxLength = $("[name="+ objName +"]").length;
        checkedLength = $("[name="+ objName +"]:checked").length;

        // console.log("checkBoxLength : " + checkBoxLength + ", checkedLength : " + checkedLength);
        // 확인용

        if( checkBoxLength == checkedLength ) {
            allObj.prop("checked", true);
        } else {
            allObj.prop("checked", false);
        }
    }
    else
    {
        $(tr).removeClass('orders_checked');
        allObj.prop("checked", false);
    }
}
$(function(){
    $("[name=checkAll]").change(function(){
        allCheckFunc( this );
    });
    $("[name=order_idx]").change(function(){
        oneCheckFunc($(this));
    });
});

function open_admin_url(url){
    open_win(url,"관리자"+fix,1440,712,200,200);
    fix++;
}
function select_btn(id,value,hidden){
    $('#'+id).val(hidden);
    $('#'+id+'_title').text(value);
    $('#'+id+'_view').val(value);
}
$(document).ready(function() {
// 지역추가금
    $('#client_id').select2({
        placeholder: "거래처를 선택해주세요.",
        dropdownParent: $('#location_price'),
    });
})
function nr_send(e,order_idx) {
    let $this = e.target;
    var name = document.getElementById('handler').value
    $this.disabled = true;
    $.ajax({
        url: main_url + "/order/Log",
        type : 'GET',
        data : {
            'order_idx' : order_idx,
        },
        success : function(data){
            if(data) {
                if(confirm('전송하시겠습니까?')){
                    send_data_ToNR(order_idx, name)
                }
            }else {
                if(confirm('이미 전송 된 주문입니다. 추가 전송하시겠습니까?')){
                    send_data_ToNR(order_idx, name)
                }
            }
            $this.disabled = false;
        },
        error : function(e) {
            alert('[전송 실패] 개발팀에 문의해주세요.')
            console.log(e);
        }
    })
}
function send_data_ToNR(order_idx, name) {
    $.ajax({
        url: main_url + "/api/Newrun/Order",
        type: "GET",
        data: {
            "order_idx" : order_idx,
            "register" : name
        },
        success: function(response) {
            if(response == "SUCCESS") {
                alert("전송 완료");
                location.reload();
            } else {
                alert('Fail');
                alert(response);
                $('#order_detail'+order_idx).click();
            }
        },
        error: function (error){
            console.log(error);
            alert('에러발생! 개발팀에 문의하세요.');
        }
    });
}
function photo_check($url) {
    let url =$url;
    let options = "width=500, height=500";
    window.open(url, "IMG", options);
}

function modal_popup(brand) {
    let url = main_url + "/order/" + brand + "/form";
    let options = "width=930, height=900";
    window.open(url, "ORDER", options);
}

function market_open(url){
    window.open(url,"상품확인","height=712,width=1440,left=300,top=200,resizable=no");
}

$('input[name="od_id[]"]').on('click',function(){
    let tr = $(this).parent().parent()
    if($(this).is(':checked')){
        $(tr).addClass('checkBox_active');
    }else {
        $(tr).removeClass('checkBox_active');
    }
});
function cancel_progress(order_idx, name ){
    if(confirm("취소 처리 중으로 변경하시겠습니까?")){
        $.ajax({
            url: main_url + "/order/cancel",
            type: "POST",
            data: {
                "order_idx" : order_idx,
                "send_name" : name
            },
            success: function(response) {
                if(response == "SUCCESS") {
                    alert("처리 완료");
                    // $('#send_name'+order_idx).text(name);
                    location.reload();
                } else {
                    alert(response);
                }
            },
            error: function (error){
                console.log(error);
                alert('에러발생! 개발팀에 문의하세요.');
            }
        });
    }
}
function cancel_refuse(name) {
    let order_number = $('#cancel_refuse_btn').data('number');
    let memo_content = $('textarea[name="cancel_refuse_memo"]').val();
    if(confirm("취소 요청을 거절하시겠습니까?\n(관리자 메모 등록)")){
        $.ajax({
            url: main_url + "/order/cancel-memo",
            type: "POST",
            data: {
                "order_number" : order_number,
                "memo_content" : memo_content,
                "register" : name
            },
            success: function(response) {
                if(response == "SUCCESS") {
                    alert("등록 완료");
                    location.reload();
                } else {
                    alert(response);
                }
            },
            error: function (error){
                console.log(error);
                alert('에러발생! 개발팀에 문의하세요.');
            }
        });
    }
}
function cancel_complete(name) {
    const order_number = $('#cancel_complete_btn').data('number');
    const memo_content = $('textarea[name="cancel_complete_memo"]').val();
    const state = $('#cancel_complete_btn').data('state');

    if(confirm("취소 완료로 변경하시겠습니까?\n(관리자 메모 등록)")){
        $.ajax({
            url: main_url + "/Order/Cancel/Complete",
            method: "POST",
            data: {
                "order_number" : order_number,
                "memo_content" : memo_content,
                "state_code"   : state,
                "register" : name
            },
            success: function(response) {
                alert("수정 완료");
                location.reload();
            },
            error: function (error){
                alert('에러발생! 개발팀에 문의하세요.');
                console.log(error);
            }
        });
    }
}
// 배송사진 팝업
function photo_popup(url) { open_win(url,'배송사진',600,700,1100,100); }
// 주문 로그
function order_log_popup(od_id) {
    url = main_url + "/order/Log/" + od_id;
    open_win(url, "Log", 920, 600, 50, 50);
}
// 발주 창 오픈
function send_intranet(idx) {
    let url = main_url + "/order/intranet/balju/" + idx;
    open_win(url, "발주", 1000,800, 50, 50);
}

// 주문 리스트 엑셀 내려받기
function excel_download() {
    // 체크박스 주문 선택
    var checkedCheckboxes = $('input[name="order_idx"]:checked').map(function() {
        return this.value;
    }).get();

    if(checkedCheckboxes.length === 0) {
        Swal.fire({
            title: "다운로드 실패",
            text: "다운로드 할 주문을 먼저 선택하여주세요.",
            icon: "error"
        });
        return ;
    }

    // 오늘 날짜 YYYYmmdd
    let today = new Date();
    let year = today.getFullYear();
    let month = String(today.getMonth() + 1).padStart(2, '0'); // 월은 0부터 시작하므로 1을 더함
    let day = String(today.getDate()).padStart(2, '0'); // 날짜를 두 자리로 맞춤
    var today_str = year + month + day;

    $.ajax({
        url: main_url + '/order/excel/download/individual',
        type: 'POST',
        data: { 'order_idx' : checkedCheckboxes },
        xhrFields: {
            responseType: 'blob'  // 이진 데이터를 받아야 하므로 'blob'으로 설정
        },
        success: function(data) {
            const blob = new Blob([data], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
            const link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.download = 'order_'+today_str+'.xlsx';
            link.click();
        },
        error: function(error) {
            alert('다운로드 실패')
            console.log(error);
        }
    });
}

// 하이라이트, 하이라이트 제거, 제거 등 체크 변경 함수
function operate_orders(type) {
    // 체크박스 주문 선택
    var checkedCheckboxes = $('input[name="order_idx"]:checked').map(function() {
        return this.value;
    }).get();

    if(checkedCheckboxes.length === 0) {
        Swal.fire({
            title: "실패",
            text: "주문을 먼저 선택하여주세요.",
            icon: "error"
        });
        return ;
    }
    // 새로운 form 요소 생성
    var form = new FormData();

    // 배열 데이터를 form에 추가
    $.each(checkedCheckboxes, function(index, value) {
        form.append('order_idx[]', value);
    });
    form.append('handler', document.getElementById('handler').value)

    var target_url = main_url;
    if(type=='remove') {
        if(confirm("선택 된 주문을 제거 하시겠습니까?")){
            target_url += "/order/operate/view";
        }else {
            return false;
        }
    }else if(type=='highlite'){
        target_url += "/order/operate/highlight/on";
    }else if(type=='highlite-off'){
        target_url += "/order/operate/highlight/off";
    }

    $.ajax({
        url: target_url,
        method: "POST",
        data: form,
        processData: false, // jQuery가 데이터를 처리하지 않도록 설정
        contentType: false, // 컨텐츠 타입을 설정하지 않음 (기본적으로 multipart/form-data 사용)
        success: function(data) {
            if(data){
                location.reload();
            }
        },
        error: function(e){
            alert('[에러발생] 개발팀에 문의하세요.')
            console.log(e)
        }
    })
}

// 배송 상태 변경
function change_deli_state(e, state, handler) {
    if(confirm("배송 상태를 변경하시겠습니까?")) {
        $.ajax({
            url: main_url + "/order/delivery/state",
            type: "GET",
            data: {
                'state' : state,
                'order_idx': e.target.dataset.index,
                'handler': handler
            },
            success: function(data){
                if(data){
                    alert('변경 완료');
                    location.reload();
                }else {
                    alert('변경 실패')
                }
            },
            error: function(e) {
                alert('[에러발생]')
                console.log(e)
            }
        })
    }
}

// 페이지당 주문 수
$('#perPage').on('change', function(){

    $.ajax({
        url: main_url + "/order/set-perPage",
        type: "GET",
        data: { 'perPage': $('#perPage').val() },
        success: function(data) {
            if(data){
                location.reload();
            }
        },
        error: function(e) {
            alert('문제 발생');
            console.log(e)
        }
    })
});

// 엑셀 익괄 다운로드
function excel_popup() {
    let url = main_url + "/order/excel/download"
    open_win(url, "excel_download", 560,400,600,100);
}

// 주문자, 연락처 클릭시 SMS 보내기
function send_SMS(idx, brand) {
    var url = main_url + "/SMS/form?order=" + idx + "&brand=" + brand;
    open_win(url,'send_SMS' ,700, 600, 50,50)
}

// 현재 날짜와 시간을 포맷하여 'YYYY-MM-DDTHH:MM' 형식으로 반환하는 함수
function getCurrentDateTimeLocal() {
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0'); // 월은 0부터 시작하므로 +1
    const day = String(now.getDate()).padStart(2, '0');
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');

    return `${year}-${month}-${day}T${hours}:${minutes}`;
}

// 입괄 입금 모달 오픈 시 현재 시간 입력
document.getElementById('deposit_completed').addEventListener('show.bs.modal', event => {
    document.getElementById('payment_payment_time').value = getCurrentDateTimeLocal();
})

// 일괄 입금 처리
function deposit_completed() {
    var invalidOrder = false;
    
    // 법인미수, 무통장 주문 확인
    $('input[name="order_idx"]:checked').each(function() {
        if(this.dataset.paytype!=="PTDP" && this.dataset.paytype!=="PTDB") {
            Swal.fire({
                title: "실패",
                text: "법인미수 또는 무통장 주문만 선택해주세요.",
                icon: "error"
            });
            invalidOrder = true;
            return false;
        }
    })
    if (invalidOrder) return;

    // 체크박스 주문 선택
    var checkedCheckboxes = $('input[name="order_idx"]:checked').map(function() {
        return this.value;
    }).get();

    if(checkedCheckboxes.length === 0) {
        Swal.fire({
            title: "실패",
            text: "주문을 먼저 선택하여주세요.",
            icon: "error"
        });
        return ;
    }

    if(confirm("선택 된 주문을 일괄 입금 처리하시겠습니까?")) {
        var form = new FormData();

        // 배열 데이터를 form에 추가
        $.each(checkedCheckboxes, function(index, value) {
            form.append('order_idx[]', value);
        });
        form.append('deposit_name', document.querySelector('input[name="deposit_name"]').value)
        form.append('payment_state_code', document.getElementById('deposit_payment_state').value)
        form.append('payment_time', document.getElementById('payment_payment_time').value)
        form.append('payment_memo', document.getElementById('payment_payment_memo').value)
        form.append('handler', document.getElementById('handler').value)

        $.ajax({
            url: main_url + "/order/operate/deposit-complete",
            method: "POST",
            data: form,
            processData: false, // jQuery가 데이터를 처리하지 않도록 설정
            contentType: false, // 컨텐츠 타입을 설정하지 않음 (기본적으로 multipart/form-data 사용)
            success: function(data) {
                if(data){
                    location.reload();
                }else {
                    alert("처리 실패")
                }
            },
            error: function(e){
                alert('[에러발생] 개발팀에 문의하세요.')
                console.log(e)
            }
        })
    }
}

function batch_input() {
    // 체크박스 주문 선택
    var checkedCheckboxes = $('input[name="order_idx"]:checked').map(function() {
        return this.value;
    }).get();

    if(checkedCheckboxes.length === 0) {
        Swal.fire({
            title: "실패",
            text: "주문을 먼저 선택하여주세요.",
            icon: "error"
        });
        return ;
    }

    if(confirm("선택 된 주문을 일괄 입력 하시겠습니까?")) {
        var form = new FormData();

        // 배열 데이터를 form에 추가
        $.each(checkedCheckboxes, function(index, value) {
            form.append('order_idx[]', value);
        });
        form.append('admin_memo', document.getElementById('order_admin_memo').value)
        form.append('handler', document.getElementById('handler').value)

        $.ajax({
            url: main_url + "/order/operate/batch-input",
            method: "POST",
            data: form,
            processData: false, // jQuery가 데이터를 처리하지 않도록 설정
            contentType: false, // 컨텐츠 타입을 설정하지 않음 (기본적으로 multipart/form-data 사용)
            success: function(data) {
                if(data){
                    location.reload();
                }else {
                    alert("처리 실패")
                }
            },
            error: function(e){
                alert('[에러발생] 개발팀에 문의하세요.')
                console.log(e)
            }
        })
    }
}
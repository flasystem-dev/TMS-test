var fix=1;

// $( document ).ready( function() {
// // 기본 위치(top)값
//     var floatPosition = parseInt($(".send_document_area").css('top')); // 바텀 기준
// // scroll 인식
//     $(window).scroll(function () {
// // 현재 스크롤 위치
//         var currentTop = $(window).scrollTop();
//         var bannerTop = currentTop + floatPosition + "px";
//
//         $(".send_document_area").stop().animate({ "top": bannerTop });
//
// //이동 애니메이션
//     }).scroll();
//
// });

$(document).ready(function() {
    $('#issuance_id').select2();
});

function scroll_up() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

function chk_easy(e) {
    const ele = e.target;
    const tr = ele.parentElement;
    if(ele.tagName == 'TD') {
        tr.firstElementChild.firstElementChild.click();
    }
}

function select_btn(id,value,hidden){
    $('#'+id).val(hidden);
    $('#'+id+'_title').text(value);
    $('#'+id+'_view').val(value);
}

function open_admin_url(url){
    open_win(url,"관리자"+fix,1440,712,200,200);
    fix++;
}

function order_detail(order_idx){
    var url = main_url + '/order/order-detail/'+order_idx;
    open_win(url,"주문서"+fix,1440,712,200,200);
    fix++;
}

// 체크박스 배경색 확인
function check_checkbox() {
    const boxes = document.querySelectorAll('input[name="od_id[]"]')

    boxes.forEach(function(box){
        if(box.checked) {
            box.parentElement.parentElement.classList.add('checkBox_active');
        }else {
            box.parentElement.parentElement.classList.remove('checkBox_active');
        }
    })
    check_cnt_amount();
}

// 선택 건수, 가격 확인
function check_cnt_amount() {
    const boxes = document.querySelectorAll('input[name="od_id[]"]')
    var total_cnt = 0;
    var total_amount = 0;

    boxes.forEach(function(box){
        if(box.checked) {
            box.parentElement.parentElement.classList.add('checkBox_active');

            total_cnt++;
            var tr = box.parentElement.parentElement;
            var amount = tr.querySelector('.payment_amount').value
            total_amount += parseInt(amount);
        }else {
            box.parentElement.parentElement.classList.remove('checkBox_active');
        }
    })

    $('.sel_order_data').text(total_cnt + "건 / " + total_amount.toLocaleString() + '원')
}

// 개별 체크박스 배경색 변경
$('input[name="od_id[]"]').on('click',function(){
    check_checkbox();
});

// 체크박스 전부 체크, 해체
$('#chk_all').on('click', function(){
    if($('#chk_all').prop('checked')) {
        $('input[name="od_id[]"]').prop('checked', true)
    }else {
        $('input[name="od_id[]"]').prop('checked', false)
    }
    check_checkbox();
})

function update_data(btn,idx,handler) {
    const tr = btn.parentElement.parentElement

    const orderer_name = tr.querySelector('input[name="orderer_name[]"]').value
    const receiver_name = tr.querySelector('input[name="receiver_name[]"]').value
    const ribbon_left = tr.querySelector('input[name="delivery_ribbon_left[]"]').value
    const delivery_address = tr.querySelector('input[name="delivery_address[]"]').value
    const deposit_name = tr.querySelector('input[name="deposit_name[]"]').value
    const product_name = tr.querySelector('input[name="product_name[]"]').value

    console.log(orderer_name)
    if(confirm('정보를 수정하시겠습니까?')) {
        $.ajax({
            url : main_url + "/Document/transaction/order/" + idx,
            type : 'POST',
            data : {
                'orderer_name' : orderer_name,
                'receiver_name' : receiver_name,
                'delivery_ribbon_left' : ribbon_left,
                'delivery_address' : delivery_address,
                'deposit_name' : deposit_name,
                'pr_name' : product_name,
                'handler' : handler
            },
            success : function(data) {
                alert(data);
                location.reload();
            },
            error : function(e) {
                alert('[에러발생] 개발팀에 문의하세요.');
                console.log(e)
            }
        })
    }
}

function transaction_detail(type) {
    const checked_list = document.querySelectorAll('input[name="od_id[]"]:checked');

    let data = {};
    let orders = [];

    checked_list.forEach((order) => {
        orders.push(order.nextElementSibling.value);
    });

    if(orders.length == 0) {
        alert('주문을 1개 이상 선택하여야합니다.')
        return;
    }

    // 데이터 한곳에 저장
    data['orders_idx'] = orders;
    data['issuance_id'] = document.getElementById('issuance_id').value;
    data['tran_year'] = document.getElementById('tran_year').value;
    data['tran_month'] = document.getElementById('tran_month').value;
    data['type'] = type;
    
    // JSON 변환
    const data_json = JSON.stringify(data);
    // Base64 인코딩
    const encodedData = btoa(data_json);


    const form = document.createElement('form');
    form.method = 'GET';
    form.action = main_url + '/Document/transaction/view';

    const hiddenField = document.createElement('input');
    hiddenField.type = 'hidden';
    hiddenField.name = 'data';
    hiddenField.value = encodedData;
    form.appendChild(hiddenField);

    document.body.appendChild(form);
    form.submit();
}

function document_area() {
    document.querySelector('.send_document_area').classList.remove('d-none');
    document.querySelector('.send_document_btn').classList.add('d-none');
}

function document_hide_btn() {
    document.querySelector('.send_document_btn').classList.remove('d-none');
    document.querySelector('.send_document_area').classList.add('d-none');
}

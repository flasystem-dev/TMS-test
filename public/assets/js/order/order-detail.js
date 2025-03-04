$('.datepicker').datepicker();
function ribbon_msg(msg){
    $('#delivery_ribbon_right').val(msg);
}

function market_open(url){
    window.open(url,"상품확인","height=712,width=1440,left=300,top=200,resizable=no");
}

// 주문 정보 URL 오픈
function popup_URL(url){
    if(!url.includes('http')) {
        url = "https://" + url;
    }
    open_win(url,'EVENT_URL',700,800,1100,100);
}

// 이미지 팝업
function popup_IMG(url){ open_win(url,'IMG',750,800,1100,100) }

// 주문 메모 시간 입력
function insert_time(time) {
    const now = new Date();
    const year = now.getFullYear();
    const month = (now.getMonth() + 1).toString().padStart(2, '0'); // 월은 0부터 시작하므로 +1
    const day = now.getDate().toString().padStart(2, '0');
    const hours = now.getHours().toString().padStart(2, '0');
    const minutes = now.getMinutes().toString().padStart(2, '0');
    const seconds = now.getSeconds().toString().padStart(2, '0');

    let currentTime = `${hours}:${minutes}:${seconds}`;
    if(time==='date'){
        currentTime = `${year}-${month}-${day}`;
    }else if(time==='datetime') {
        currentTime = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
    }

    document.querySelector('textarea[name="admin_memo"]').value += currentTime;
}

// 주문 삭제
function delete_order(idx) {
    if(confirm("[!!주의!!]\n정말 삭제하시겠습니까?\n삭제 시 주문을 복구 할 수 없습니다.")){
        $.ajax({
            url: main_url + "/order/detail/state/" +  idx,
            type: "delete",
            success: function(data){
                alert(data);
                opener.location.reload();
                window.close();
            },
            error: function(error) {
                alert('[에러 발생] 개발팀에 문의하세요.')
                console.log(error);
            }
        })
    }
}

// 알림톡 v1 전송
function send_ats(template_type,type_name,order_idx, payment_number){
    if(confirm(type_name+" 알림톡을 보내시겠습니까?")) {
        $.ajax({
            url: main_url + "/KakaoTalk/SendATS_one",
            type: "POST",
            data: {
                'template_type' : template_type,
                'order_idx' : order_idx,
                'payment_number' : payment_number,
            },
            success: function(data){
                if(data.status){
                    alert(data.msg);
                }else{
                    alert(data.msg);
                }
            },
            error: function(error) {
                alert('[에러 발생] 개발팀에 문의하세요.')
            }
        })
    }
}

// 상품 변경 select change 함수
$(document).on('change', '#product_ctgy', function(){

    var form = document.getElementById('product-search-form')
    const formData = new FormData(form);
    formData.append('brand_type_code',document.getElementById('brand').value);
    formData.append('price_type',document.getElementById('price_type').value);

    const queryString = new URLSearchParams(formData).toString();

    $.ajax({
        type: 'get',
        url: main_url + "/order/form/products?" + queryString,
        success : function(data) {
            $('#product-list').html(data);
        },
        error : function(error) {
            alert('에러 발생!');
            console.log(error);
        }
    });
})

// 상품 리스트 페이지네이션, 검색 버튼 함수
$(document).on('click', '#search_btn', function(e){
    e.preventDefault();

    var form = document.getElementById('product-search-form')
    const formData = new FormData(form);
    formData.append('brand_type_code',document.getElementById('brand').value);
    formData.append('price_type',document.getElementById('price_type').value);

    const queryString = new URLSearchParams(formData).toString();

    $.ajax({
        type: 'get',
        url: main_url + "/order/form/products?" + queryString,
        success : function(data) {
            $('#product-list').html(data);
        },
        error : function(error) {
            alert('에러 발생!');
            console.log(error);
        }
    });
});

// 상품 리스트 검색 input 엔터키 처리
// $('input[name="search_word"]').on('keyup',function(e){
//     e.preventDefault();
//     if(event.keyCode === 13) {
//
//         var form = document.getElementById('product-search-form')
//         const formData = new FormData(form);
//         formData.append('brand_type_code',document.getElementById('brand').value);
//         formData.append('price_type',document.getElementById('price_type').value);
//
//         const queryString = new URLSearchParams(formData).toString();
//
//         $.ajax({
//             type: 'get',
//             url: main_url + "/order/form/products?" + queryString,
//             success : function(data) {
//                 $('#product-list').html(data);
//             },
//             error : function(error) {
//                 alert('에러 발생!');
//                 console.log(error);
//             }
//         });
//     }
// })

// 상품 변경
$('#product-list').on('click', '.add-product-btn', function(e){
    if(confirm("상품을 정말 변경하시겠습니까?")){
        let form = e.target.closest('form');
        const formData = new FormData(form);
        formData.append('order_idx', document.getElementById('order-idx').value)
        formData.append('price_type', document.getElementById('price_type').value)

        $.ajax({
            url: main_url + "/order/detail/product",
            type: "POST",
            data: formData,
            async: false,
            processData: false,
            contentType: false,
            success: function(data) {
                if(data) {
                    opener.location.reload();
                    location.reload();
                }else {
                    alert("변경 실패");
                }
            },
            error: function(e) {
                alert("[에러발생]개발팀에 문의하세요.");
                console.log(e);
            }
        })
    }
});

// 사업자 옵션 리스트 가져오기
$('#change_vendor_modal').on('show.bs.modal', function(e){
    let brand = document.getElementById('brand').value;
    let vendor_idx = document.getElementById('vendor_idx').value;

    $.ajax({
        url: main_url + "/order/detail/vendor",
        type: "GET",
        data: { 'brand' : brand },
        success: function(data){
            document.getElementById('change_vendor').innerHTML = data;
            $('#change_vendor').select2({
                placeholder: "DID, 상점명, 대표번호, 대표자명",
                dropdownParent : $('#change_vendor_modal'),
                templateResult: formatState,
                templateSelection: formatResult,

            });

            $('#change_vendor').val(vendor_idx).trigger('change');
        },
        error: function(e){
            alert("[에러발생] 개발팀에 문의하세요.");
            console.log(e);
        }
    })
})

// 사업자 select 옵션 css 변경
function formatState(state){
    if (!state.id) {
        return state.text;
    }

    var $state = $(
        '<span class="did_number">' + $(state.element).data('did') + '</span>' +
        '<span class="shop_name">' + $(state.element).data('mall') + '</span>' +
        '<span class="gen_number">' + $(state.element).data('number') + '</span>' +
        '<span class="rep_name">' + $(state.element).data('name') + '</span>'
    );

    return $state;
}

// 사업자 select 선택 후 결과 css
function formatResult(state){
    if (!state.id) {
        return state.text;
    }

    var $state = $(
        '<span class="did_number">' + $(state.element).data('did') + '</span>' +
        '<span class="shop_name">' + $(state.element).data('mall') + '</span>' +
        '<span class="gen_number">' + $(state.element).data('number') + '</span>' +
        '<span class="rep_name">' + $(state.element).data('name') + '</span>'
    );

    return $state;
}

// 발주처 select 옵션 css 변경
function formatState2(state){
    if (!state.id) {
        return state.text;
    }

    var $state = $(
        '<span class="user_id">' + $(state.element).data('id') + '</span>' +
        '<span class="user_name">' + $(state.element).data('name') + '</span>' +
        '<span class="user_phone">' + $(state.element).data('phone') + '</span>'
    );

    return $state;
}

// 사업자 변경
$('#change_vendor').on("change", function(e){
    const customer = document.getElementById('orderer_mall_id');
    customer.innerHTML = "";
    let user_id = customer.dataset.customer;

    $.ajax({
        url: main_url + "/order/form/users",
        type: "GET",
        data:{
            'vendor_idx' : document.querySelector('#change_vendor').value
        },
        success: function(data){
            customer.innerHTML = data;
            $('#orderer_mall_id').select2({
                dropdownParent : $('#change_vendor_modal'),
                templateResult: formatState2,
                templateSelection: formatResult2,
            });

            $('#orderer_mall_id').val(user_id).trigger('change');
        },
        error: function(e){
            alert("[에러발생] 개발팀에 문의하세요.")
            console.log(e)
        }
    })
})

// 발주처 select 선택 후 css
function formatResult2(state){
    if (!state.id) {
        return state.text;
    }

    var $state = $(
        '<span class="user_id">' + $(state.element).data('id') + '</span>' +
        '<span class="user_name">' + $(state.element).data('name') + '</span>' +
        '<span class="user_phone">' + $(state.element).data('phone') + '</span>'
    );

    return $state;
}

function change_vendor() {
    if(confirm("해당 주문의 사업자를 변경하시겠습니까?")) {
        let form = document.getElementById('change_vendor_form');
        let formData = new FormData(form);

        $.ajax({
            url: main_url + "/order/detail/vendor",
            method: "POST",
            data: formData,
            async: false,
            processData: false,
            contentType: false,
            success: function(data) {
                if(data) {
                    opener.location.reload();
                    location.reload();
                }else {
                    alert("[수정 실패] 개발팀에 문의하세요.");
                }
            },
            error: function(e) {
                alert("[에러발생]개발팀에 문의하세요.");
                console.log(e);
            }
        })
    }
}

$('.pay_amount_input').on('input', function(e){
    let value = $(this).val();
    // 숫자만 남기고 나머지 제거
    $(this).val(value.replace(/[^0-9]/g, ''));
})

function form_submit() {
    let form = document.getElementById('order_update_form');
    let formData = new FormData(form);

    $.ajax({
        url: form.action,
        type: "POST",
        data: formData,
        async: false,
        processData: false,
        contentType: false,
        success: function(data) {
            opener.location.reload();
            location.reload();
        },
        error: function(e) {
            alert("[에러발생] 개발팀에 문의해주세요.")
            console.log(e)
        }
    })
}

// 결제 상태 변경
function change_state(state) {
    if(confirm("결제 상태를 변경하시겠습니까?")) {
        $.ajax({
            url: main_url + "/order/detail/state",
            type: "GET",
            data: {
                'state': state,
                'order_idx': document.getElementById('order-idx').value,
            },
            success: function(data) {
                if(data.state){
                    opener.location.reload();
                    location.reload();
                }
            },
            error: function(e) {
                alert('[에러발생] 개발팀에 문의하세요.');
                console.log(e)
            }
        })
    }
}

// 전송 버튼생성
function make_sendBtn(order_idx, brand) {
    var btn_html ='<button class="btn btn-primary btn-soft-primary btn-sm" onclick="nr_send(event,'+order_idx+');">전송</button>';
    if(brand==="BTCS" || brand==="BTFC"){
        btn_html = '<button class="btn btn-primary btn-soft-primary btn-sm" onclick="send_intranet('+order_idx+');">발주</button>';
    }
    opener.document.getElementById('send_area'+order_idx).innerHTML = btn_html;
}

// 결제 주문 번호 클립 복사
function copyData(e) {
    const text = e.target.dataset.data;

    // 클립보드에 텍스트 복사
    navigator.clipboard.writeText(text).then(function() {
        showToast("복사 완료!");
    }).catch(function(err) {
        // showToast(err)
    });
}

// 결제 주문 번호 클립 복사
function copyVAInfo(e) {
    const text = e.target.dataset.number;

    // 클립보드에 텍스트 복사
    navigator.clipboard.writeText(text).then(function() {
        showToast("복사 완료!");
    }).catch(function(err) {
        // showToast(err)
    });
}

// 결제 수단 변경 모달 index 전달
const payment_type_modal = document.getElementById('change_payment_type');
payment_type_modal.addEventListener('show.bs.modal', event => {
    var payment_number = event.relatedTarget.dataset.number;
    payment_type_modal.querySelector('#modal_payment_number_text').innerText = payment_number;
    payment_type_modal.querySelector('input[name="payment_number"]').value=payment_number;
});

// 결제 상태 변경 모달 index 전달
const payment_state_modal = document.getElementById('change_payment_state');
payment_state_modal.addEventListener('show.bs.modal', event => {
    var payment_number = event.relatedTarget.dataset.number;
    var state = event.relatedTarget.dataset.state;
    payment_state_modal.querySelector('#modal_payment_state_text').innerText = payment_number;
    payment_state_modal.querySelector('input[name="payment_number"]').value=payment_number;

    var select = document.getElementById('select_payment_state');

    // 현재 상태 옵션 제거
    for (var i = 0; i < select.options.length; i++) {
        if (select.options[i].value === state) {
            select.remove(i);
            break;
        }
    }
});

// 결제 수단 변경
function change_payment_type() {
    if(confirm("결제수단을 변경하시겠습니까?")) {
        let form = document.getElementById('payment_type_form');
        let formData = new FormData(form);

        $.ajax({
            url: main_url + "/order/detail/payment-type",
            method: "POST",
            data: formData,
            async: false,
            processData: false,
            contentType: false,
            success: function(data) {
                if(data) {
                    opener.location.reload();
                    location.reload();
                }else {
                    alert("[수정 실패] 개발팀에 문의하세요.");
                }
            },
            error: function(e) {
                alert("[에러발생]개발팀에 문의하세요.");
                console.log(e);
            }
        })
    }
}

// 결제 수단 변경
function change_payment_state() {
    if(confirm("결제상태를 변경하시겠습니까?")) {
        let form = document.getElementById('payment_state_form');
        let formData = new FormData(form);

        $.ajax({
            url: main_url + "/order/detail/payment-state",
            method: "POST",
            data: formData,
            async: false,
            processData: false,
            contentType: false,
            success: function(data) {
                if(data) {
                    opener.location.reload();
                    location.reload();
                }else {
                    alert("[수정 실패] 개발팀에 문의하세요.");
                }
            },
            error: function(e) {
                alert("[에러발생]개발팀에 문의하세요.");
                console.log(e);
            }
        })
    }
}

// 결제 정보 변경
function update_payment(e) {
    Swal.fire({
        title: "결제 정보 변경",
        text: "결제를 변경하시겠습니까 ?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "수정",
        cancelButtonText: "취소",
        reverseButtons: true,
    }).then((result) => {
        if (result.isConfirmed) {
            let btn = e.target
            let tr = e.target.closest('tr');
            let advancePayment = tr.querySelector('input[name="advance_payment"]');
            let is_checked = advancePayment.checked ? 1 : 0;

            $.ajax({
                url: main_url + "/order/detail/payment/data",
                type: "POST",
                data: {
                    'order_idx': document.getElementById('order-idx').value,
                    'payment_number': btn.dataset.num,
                    'payment_amount' : tr.querySelector('input[name="payment_amount"]').value,
                    'deposit_name' : tr.querySelector('input[name="deposit_name"]').value,
                    'payment_time' : tr.querySelector('input[name="payment_time"]').value,
                    'document_type' : tr.querySelector('select[name="document_type"]').value,
                    'is_publish' : tr.querySelector('select[name="is_publish"]').value,
                    'payment_memo' : tr.querySelector('textarea[name="payment_memo"]').value,
                    'advance_payment' : is_checked,
                },
                success: function(data){
                    if(data) {
                        opener.location.reload();
                        location.reload();
                    }else {
                        alert('수정 실패!');
                    }
                },
                error: function(e) {
                    alert('[에러발생] 개발팀에 문의하세요.');
                    console.log(e)
                }
            })
        }
    });
}

// 결제 삭제
function delete_payment(e) {
    Swal.fire({
        title: "결제 정보 삭제",
        text: "결제를 삭제하시겠습니까 ?",
        icon: "danger",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "삭제",
        cancelButtonText: "취소",
        reverseButtons: true,
    }).then((result) => {
        if (result.isConfirmed) {
            let btn = e.target

            $.ajax({
                url: main_url + "/order/detail/payment",
                type: "DELETE",
                data: {
                    'order_idx': document.getElementById('order-idx').value,
                    'payment_number': btn.dataset.num,
                },
                success: function(data){
                    if(data) {
                        opener.location.reload();
                        location.reload();
                    }else {
                        alert('삭제 실패!');
                    }
                },
                error: function(e) {
                    alert('[에러발생] 개발팀에 문의하세요.');
                    console.log(e)
                }
            })
        }
    });
}

// 주문서 복제
function open_copyForm() {
    var order_idx = document.getElementById('order-idx').value;
    var brand = document.getElementById('brand').value;
    let url = main_url + "/order/" + brand + "/form?order_idx=" + order_idx;

    open_win(url, 'order-form' , 950, 900, 500, 50);
}

// 알림톡 모달 보여주기 기본 값 가져오기
const alimTalk_modal = document.getElementById('talk_modal_area');
alimTalk_modal.addEventListener('show.bs.modal', event => {
    $.ajax({
        url: main_url + "/order/detail/alim-talk",
        type: "GET",
        data: {
            'order_idx' : document.getElementById("order-idx").value
        },
        success: function(data) {
            document.getElementById('template_area').innerHTML = data;
        },
        error: function (e) {
            alert('문제 발생');
            console.log(e)
        }
    })
})

// 알림톡 템플릿, 결제번호 변경 시 템플릿 가져오기
$(document).on('change', '#talk_template_type, #talk_payment_number', function(){
    $.ajax({
        url: main_url + "/order/detail/alim-talk",
        type: "GET",
        data: {
            'order_idx' : document.getElementById('order-idx').value,
            'template_type' : document.getElementById('talk_template_type').value,
            'payment_number' : document.getElementById('talk_payment_number').value
        },
        success: function(data) {
            document.getElementById('template_area').innerHTML = data;
            document.getElementById('insert_value').addEventListener('click', function(){
                insert_value();
            })
        },
        error: function(e) {
            alert("문제발생")
            console.log(e)
        }
    })
})


// 템플릿에 변수 적용하기
var originalText = null;
function insert_value() {
    var template_area = document.getElementById('talk_template');
    if (!originalText) {
        originalText = template_area.textContent;
    }
    var text = originalText;

    var variables = document.querySelectorAll('input[name="variables[]"]');
    var values = document.querySelectorAll('input[name="values[]"]');

    variables.forEach((variable, index) => {
        var variableValue = variable.value.trim();
        var valueValue = values[index].value.trim();

        variableValue = escapeRegExp(variableValue);
        var regex = new RegExp(variableValue, 'g');
        text = text.replace(regex, valueValue);
    });
    template_area.textContent = text;
}

// 특수문자 이스케이프
function escapeRegExp(string) {
    return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'); // 정규식의 특수 문자를 이스케이프
}

// 알림톡 v2 전송
function send_customTalk(){
    let form = document.getElementById('sendTalk_form');
    let formData = new FormData(form);
    formData.append("order_idx", document.getElementById('order-idx').value);

    if(confirm('알림톡을 전송하시겠습니까 ?')) {
        $.ajax({
            url: main_url + "/KakaoTalk/SendTalk_custom",
            type: "POST",
            data: formData,
            async: false,
            processData: false,
            contentType: false,
            success: function(data){
                if(data.status){
                    alert(data.msg);
                }else{
                    alert(data.msg)
                }
            },
            error: function(e){
                alert("문제발생")
                console.log(e)
            }
        })
    }
}

// 환불 모달 실행 시 함수
const refund_modal = document.getElementById('refund_modal');
refund_modal.addEventListener('show.bs.modal', event=> {
    let btn = event.relatedTarget;
    var order_idx = document.getElementById('order-idx').value;
    var payment_number = btn.dataset.number;
    var refund_modal_body = document.getElementById('refund_modal_body');

    $.ajax({
        url: main_url + "/order/cancel/table",
        data: { 'order_idx': order_idx, 'payment_number' : payment_number },
        success: function(data){
            refund_modal_body.innerHTML = data;
        },
        error: function(e){
            alert('[에러발생] 개발팀에 문의하세요')
            console.log(e)
        }
    })
});

// 환불 신청
function refund_submit(){

    const pg = document.getElementById('payment_pg').value

    var url = '';

    if(pg === 'toss') {
        url = main_url + '/Payment/Complain/toss';
    }else if(pg === 'nice') {
        url = main_url + '/Payment/Nice/refund';
    }

    if(confirm('환불하시겠습니까?')){
        const form = new FormData(document.getElementById('refund_form'))

        $.ajax({
            url: url,
            type: "POST",
            data: form,
            async: false,
            processData: false,
            contentType: false,
            success: function(data) {
                if(data.state){
                    alert(data.message)
                    opener.location.reload();
                    location.reload();
                }else {
                    alert(data.message)
                }
            },
            error: function(error){
                alert('[에러 발생] 개발팀에 문의하세요');
                console.log(error);
            }
        });
    }
}

// 발주 정보 업데이트
function update_balju() {
    if(confirm("발주 정보를 변경하시겠습니까 ?")) {
        let form = document.getElementById('balju_amount_form');

        let formData = new FormData(form);
        formData.append('order_idx', document.getElementById('order-idx').value);

        $.ajax({
            url: main_url + "/order/vendor/balju",
            type: "POST",
            data: formData,
            async: false,
            processData: false,
            contentType: false,
            success: function(data) {
                if(data) {
                    opener.location.reload();
                    location.reload();
                }else {
                    alert('변경 실패')
                }
            },
            error: function(e) {
                alert('[에러발생] 개발팀에 문의하세요.')
                console.log(e)
            }
        })
    }
}

// 발주가 계산
function calc_balju_amount() {
    let vendor_amount = parseInt(document.getElementById('vendor_amount').value);
    document.getElementById('vendor_amount_display').value = vendor_amount.toLocaleString();

    var options_price = document.querySelectorAll('.vendor_option_price')

    var sum_optionPrice = 0;
    options_price.forEach(function(option){
        sum_optionPrice += parseInt(option.value)
    });
    document.getElementById('vendor_optionAmount_display').value = sum_optionPrice.toLocaleString();
}

// 발주 input 변경
$(document).on('input', '#vendor_amount, .vendor_option_price', function(){
    calc_balju_amount();
})

// 발주 모달 오픈
const baljuAmountArea = document.getElementById('balju_amount_area');
if (baljuAmountArea) {
    baljuAmountArea.addEventListener('show.bs.modal', event => {
        calc_balju_amount();
    });
}

// 플레이오토 함수
function playauto_send(type) {
    var url = main_url;
    if(type==='delivery_send') {
        url += "/order/playauto/delivery";
    }else if(type==="connect") {
        url += "/order/playauto/connect";
    }

    $.ajax({
        url : url,
        type: "GET",
        data: {
            'order_idx' : document.getElementById('order-idx').value
        },
        success:function(data){
            if(data.status) {
                alert(data.msg);
            }else{
                alert(data.msg)
            }
            console.log(data);
        },
        error:function(e){
            alert('재전송 실패');
            console.log(e)
        }
    })
}

// 배송 상태 변경
function change_deli_state(state) {
    if(confirm("배송 상태를 변경하시겠습니까?")) {
        $.ajax({
            url: main_url + "/order/delivery/state",
            type: "GET",
            data: {
                'state' : state,
                'order_idx': $('#order-idx').val(),
            },
            success: function(data){
                if(data){
                    alert('변경 완료');
                    opener.location.reload();
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

// 환불 부분 환불 버튼
$('#refund_modal_body').on('change', '#partial_cancel' , function(){
    if($(this).prop('checked')) {
        $('#refund_amount').prop('readonly', false);
    }else {
        $('#refund_amount').prop('readonly', true);
    }
})

// 다수 주문 일괄 환불 버튼
$('#refund_modal_body').on('change', '#all_cancel' , function(){
    if($(this).prop('checked')) {
        $('#refund_amount').prop('readonly', true);
    }else {
        $('#refund_amount').prop('readonly', false);
    }
})

// 배송 사진 팝업
function photo_popup(url) {
    open_win(url,'배송사진1',600,700,1100,100);
}
// 카드 키인 - 번호 입력 후 자동 이동
function moveToNext(current, length) {
    const value = current.value.replace(/[^0-9]/g, '');
    current.value = value;
    if(current.value.length >= length) {
        current.nextElementSibling.focus();
    }
}

// 카드 키인 - 번호 삭제 시 이전 포커스
function checkBackspace(e) {
    if(e.target.value.length == 0 && e.keyCode === 8) {
        e.target.previousElementSibling.focus();
    }
}

// 구매자명에 주문자명 가져오기
function get_ordererName() {
    let ordererName = document.getElementById('orderer_name').value;
    document.querySelector('input[name="paymentName"]').value = ordererName;
}

// 결제추가
function add_payment() {
    let form = document.getElementById('add_payment_form');
    let formData = new FormData(form);
    formData.append('order_idx', document.getElementById('order-idx').value);

    const payType = formData.get('payment_type_code');
    if(payType == 'PTMN') {
        const keyIn = new bootstrap.Modal('#cardKeyIn');
        keyIn.show();
        $('#addPay_close').click();
        return;
    }

    $.ajax({
        url: form.action,
        type: "POST",
        data: formData,
        async: false,
        processData: false,
        contentType: false,
        success: function(data) {
            if(payType=='PTVA') {
                $('#payment_area').html(data);
                nicepayStart();
                $('#addPay_close').click();
            }else {
                alert(data.msg);
                opener.location.reload();
                location.reload();
            }

        },
        error: function(e) {
            alert("[에러발생] 개발팀에 문의해주세요.")
            console.log(e)
        }
    })
}

// 카드 키인 결제
function pay_keyIn(e) {
    e.target.disabled = true;
    var formData1 = new FormData(document.getElementById('add_payment_form'));
    var formData2 = new FormData(document.getElementById('keyIn_form'));
    var finalForm = new FormData();

    formData1.forEach((value, key) => {
        finalForm.append(key, value);
    })
    formData2.forEach((value, key) => {
        finalForm.append(key, value);
    })

    $.ajax({
        url : main_url + "/order/detail/payment",
        type : "POST",
        data: finalForm,
        async: false,
        processData: false,
        contentType: false,
        success: function(data) {
            if(data.status) {
                alert(data.msg);
                opener.location.reload();
                location.reload();
            }else {
                alert(data.msg);
                e.target.disabled = false;
            }
        },
        error: function(e) {
            alert('[에러발생]개발팀에 문의하세요.');
            console.log(e);
            e.disabled = false;
        }
    })
}
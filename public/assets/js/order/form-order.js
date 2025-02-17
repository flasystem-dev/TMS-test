$(document).ready(function() {
    // 채널 select2
    $('#channel-vendor').select2({
        placeholder: "DID, 상점명, 대표번호, 대표자명",
        templateResult: formatState,
        templateSelection: formatResult,
    });

    $('#orderer_mall_id').select2();

    // 지역추가금
    $('#select-location').select2({
        placeholder: "배송지역을 선택해주세요.",
        dropdownParent: $('#location_price'),
    });
});

$(document).on('input', '#admin_discount_input, input[name="discount_ratio"]', function(){
    calc_all_price();
});

/* ############################################## 카카오 우편 검색 ######################################################*/

// 카카오 주소 검색 요소 안보이기
function foldDaumPostcode() {
    var kakao_area = document.getElementById('kakao_area')
    // iframe을 넣은 element를 안보이게 한다.
    kakao_area.style.display = 'none';
}

// 카카오 주소 검색 버튼 클릭 함수
function search_address() {

    var kakao_area = document.getElementById('kakao_area')
    var currentScroll = Math.max(document.body.scrollTop, document.documentElement.scrollTop);
    new daum.Postcode({
        oncomplete: function (data) {
            var addr = ''; // 주소 변수
            var extraAddr = ''; // 참고항목 변수

            //사용자가 선택한 주소 타입에 따라 해당 주소 값을 가져온다.
            if (data.userSelectedType === 'R') { // 사용자가 도로명 주소를 선택했을 경우
                addr = data.roadAddress;
            } else { // 사용자가 지번 주소를 선택했을 경우(J)
                addr = data.jibunAddress;
            }

            // 사용자가 선택한 주소가 도로명 타입일때 참고항목을 조합한다.
            if (data.userSelectedType === 'R') {
                // 법정동명이 있을 경우 추가한다. (법정리는 제외)
                // 법정동의 경우 마지막 문자가 "동/로/가"로 끝난다.
                if (data.bname !== '' && /[동|로|가]$/g.test(data.bname)) {
                    extraAddr += data.bname;
                }
                // 건물명이 있고, 공동주택일 경우 추가한다.
                if (data.buildingName !== '' && data.apartment === 'Y') {
                    extraAddr += (extraAddr !== '' ? ', ' + data.buildingName : data.buildingName);
                }
                // 표시할 참고항목이 있을 경우, 괄호까지 추가한 최종 문자열을 만든다.
                if (extraAddr !== '') {
                    extraAddr = ' (' + extraAddr + ')';
                }
                // 조합된 참고항목을 해당 필드에 넣는다.
                // document.getElementById("sample6_extraAddress").value = extraAddr;

            }
            //  else {
            //     document.getElementById("sample6_extraAddress").value = '';
            // }

            // 우편번호와 주소 정보를 해당 필드에 넣는다.

            // const info_type = document.querySelector('input[name="del_info_type"]:checked').value;

            // // 도/시/군/구 정보
            //     $('.order_adress').val(data.zonecode);
            $('#delivery_address').val(addr);
            var location_data = data.sido + " " + data.sigungu;

            // 커서를 상세주소 필드로 이동한다.
            document.getElementById("delivery_address").focus();


            // iframe을 넣은 element를 안보이게 한다.
            // (autoClose:false 기능을 이용한다면, 아래 코드를 제거해야 화면에서 사라지지 않는다.)
            kakao_area.style.display = 'none';

            // 우편번호 찾기 화면이 보이기 이전으로 scroll 위치를 되돌린다.
            document.body.scrollTop = currentScroll;
        },
        // 우편번호 찾기 화면 크기가 조정되었을때 실행할 코드를 작성하는 부분. iframe을 넣은 element의 높이값을 조정한다.
        onresize: function (size) {
            kakao_area.style.height = size.height + 'px';
        },
        width: '100%',
        height: '100%'
    }).embed(kakao_area);

    // iframe을 넣은 element를 보이게 한다.
    kakao_area.style.display = 'block';
};

// 발주처 select 옵션 css 변경
function formatState(state){
    if (!state.id) {
        return state.text;
    }

    var valid = "O";
    var valid_css = "text-success"

    if($(state.element).data('valid')=="N") {
        valid = "X";
        valid_css = "text-danger"
    }

    var $state = $(
        '<span class="did_number">' + $(state.element).data('did') + '</span>' +
        '<span class="shop_name">' + $(state.element).data('mall') + '</span>' +
        '<span class="gen_number">' + $(state.element).data('number') + '</span>' +
        '<span class="rep_name">' + $(state.element).data('name') + '</span>' +
        '<span class="is_valid ' + valid_css + '">' + valid + '</span>'
    );

    return $state;
}

// 발주처 select 선택 후 css
function formatResult(state){
    if (!state.id) {
        return state.text;
    }

    var valid = "O";
    var valid_css = "text-success"

    if($(state.element).data('valid')=="N") {
        valid = "X";
        valid_css = "text-danger"
    }

    var $state = $(
        '<span class="did_number">' + $(state.element).data('did') + '</span>' +
        '<span class="shop_name">' + $(state.element).data('mall') + '</span>' +
        '<span class="gen_number">' + $(state.element).data('number') + '</span>' +
        '<span class="rep_name">' + $(state.element).data('name') + '</span>' +
        '<span class="is_valid ' + valid_css + '">' + valid + '</span>'
    );

    return $state;
}

// 간편주문 링크 보내기
function link_send(){
    var brand_type_code = $('#brand_type_code').val();
    var od_name=$('#orderer_name').val();
    var od_hp=$('#send_phone_num').val();
    var od_b_name=$('#receiver_name').val();
    var od_b_hp=$('#receiver_phone').val();
    var od_deli_date=$('#delivery_date').val();
    var od_deli_time=$('#delivery_time').val();
    var od_addr=$('#delivery_address').val();
    var od_msg_left=$('#delivery_ribbon_left').val();
    var od_msg_right=$('#delivery_ribbon_right').val();
    var od_register=$('#admin_regist').val();
    var token = $("input[name=_token]").val();
    var goods_id = document.querySelector('select[name="goods_select"]').value;
    $.ajax({
        url: main_url + "/api/App/Send-Link",
        type: 'post',
        data:{
            od_name:od_name,
            od_hp:od_hp,
            od_b_name:od_b_name,
            od_b_hp:od_b_hp,
            od_deli_date:od_deli_date,
            od_deli_time:od_deli_time,
            od_addr:od_addr,
            od_msg_left:od_msg_left,
            od_msg_right:od_msg_right,
            od_register:od_register,
            brand_type_code : brand_type_code,
            goods_id : goods_id,
        },
        success : function(data) {
            alert(data);
        },
        error : function(error) {
            alert('에러 발생!');
            console.log(error);
        }
    });
}

// 경조사어 모달
function getMsgList(ctgy) {
    $.ajax({
        type: 'get',
        url: main_url+'/order/form/msg-templates/' + ctgy,
        success : function(res) {
            $('#event_msg_btns').html('');
            $('#event_msg_btns').append(res);
        },
        error : function(error) {
            alert('문제가 발생 하였습니다.');
            console.log(error);
        }
    });
}

// 메시지 라디오 박스 변경
$('input[name="msg_type"]').on('change',function(e){
    $('.msg_input').addClass('d-none');
    $('#msg_'+this.value).removeClass('d-none');
});

// 보내는분 리본 주문자 자동 입력
document.getElementById('orderer_name_btn').addEventListener('click',function(){
    var orderer_name = $('input[name="orderer_name"]').val();
    $('input[name="delivery_ribbon_left"]').val(orderer_name);
})

// 자주 쓰는 경조사 버튼 자동 입력
$('.freq_btn').on('click',function(e){
    var text = $(this).text();
    $('input[name="delivery_ribbon_right"]').val(text);
});

// 경조사어 모델에서 선택 버튼
function get_event_msg(e) {
    let value = e.target.dataset.value;
    $('input[name="delivery_ribbon_right"]').val(value);
    $('#center_modal_close').click();
}

// 간편주문 상품 ajax
function item_ajax(category,brand){
    $.ajax({
        type: 'get',
        url:"../../ajax/app-item",
        data:{category:category,brand:brand},
        success : function(data) {
            $('#app_items').html(data);
        },
        error : function(error) {
            alert('에러 발생!');
            console.log(error);
        }
    });
}

// 간편주문 표시 버튼
function orderer_phone(){
    var orderer_phone= $('#orderer_phone').val();
    $('#send_phone_num').val(orderer_phone);
}

// 사업자 선택시 사업자 정보 전달
$('#channel-vendor').on('change', function(){
    const select = $('select[name="mall_code"]')
    $('#vendor_idx').val($(select).val());
    // 상품 가격 타입 전달
    const type = select.find('option:selected').data('type');
    const typeName = select.find('option:selected').data('type-name');
    $('#price_type').val(type)
    $('.price-type-text').text(typeName);
    // 미수 가능 여부 판단
    const credit = select.find('option:selected').data('credit');
    if(credit===0) {
        $('#pay_misu_area').addClass('d-none')
    }else {
        $('#pay_misu_area').removeClass('d-none')
    }
})

// 상품 리스트 페이지네이션, 검색 버튼 함수
$(document).on('click', '#search_btn', function(e){
    e.preventDefault();

    var form = document.getElementById('product-form')
    const formData = new FormData(form);
    formData.append('brand_type_code',document.getElementById('brand').value);
    formData.append('price_type',document.getElementById('price_type').value);

    $.ajax({
        type: 'get',
        url: main_url + "/order/form/products",
        data: formData,
        async: false,
        processData: false,
        contentType: false,
        success : function(data) {
            $('#product-list').html(data);
        },
        error : function(error) {
            alert('에러 발생!');
            console.log(error);
        }
    });
});

// 상품 검색 select 카테고리 변경
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

// 상품 검색 input 엔터키 처리
$('input[name="search_word"]').on('keydown',function(e){
    e.preventDefault();
    if(event.keyCode === 13) {
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
    }
})

// 상품 선택
$('#product-list').on('click', '.add-product-btn', function(e){
    let form = e.target.closest('form');
    const formData = new FormData(form);
    formData.append('price_type', document.getElementById('price_type').value)

    $.ajax({
        url: main_url + "/order/form/product",
        type: "POST",
        data: formData,
        async: false,
        processData: false,
        contentType: false,
        success: function(data) {
            $('#selected-product').html(data);
            // 상품 총액
            document.querySelector('input[name="item_total_amount"]').value = document.getElementById('item_total_amount').dataset.price;
            // 지역추가금 초기화
            document.getElementById('loc_price_text').innerText = '';
            // 금액 계산
            calc_all_price();
            bootstrap.Modal.getOrCreateInstance(document.getElementById('product-list-modal')).hide();
        },
        error: function(e) {
            alert("[에러발생]개발팀에 문의하세요.");
            console.log(e);
        }
    })
});


// 금액 계산
function calc_all_price(){
    // 상품 총액
    const item_total_amount = parseInt(document.getElementById('item_total_amount').dataset.price);

    // 관리자 할인
    const discount_ratio = document.querySelector('input[name="discount_ratio"]:checked').value;
    var admin_discount = parseInt(document.getElementById('admin_discount_input').value);
    if(discount_ratio === 'percentage') {
        admin_discount = item_total_amount * (admin_discount/100);
    }

    // 결제 총액
    const total_amount = item_total_amount - admin_discount;

    document.querySelector('input[name="admin_discount"]').value = admin_discount;
    document.querySelector('input[name="total_amount"]').value = total_amount;

    document.getElementById('item-total-amount-text').innerText = item_total_amount.toLocaleString();
    document.getElementById('admin-discount-text').innerText = admin_discount.toLocaleString();
    document.getElementById('total-amount-text').innerText = total_amount.toLocaleString();
}

// 배송 시간 선택 라디오 박스
$('input[name="delivery_time_sel"]').on('change', function() {
    const select = $('input[name="delivery_time_sel"]:checked').val();

    switch (select) {
        case 'now':
            document.getElementById('delivery_time').value = '즉시';
            $('#event_time_input').removeClass('d-none');
            $('#event_time_select').addClass('d-none');
            $('#event_gap_select').addClass('d-none');
            return;
        case 'event':
            $('#event_time_input').addClass('d-none');
            $('#event_time_select').removeClass('d-none');
            $('#event_gap_select').addClass('d-none');
            return;
        case 'time':
            $('#event_time_input').addClass('d-none');
            $('#event_time_select').addClass('d-none');
            $('#event_gap_select').removeClass('d-none');
            return;
        case 'input':
            document.getElementById('delivery_time').value = '';
            $('#event_time_input').removeClass('d-none');
            $('#event_time_select').addClass('d-none');
            $('#event_gap_select').addClass('d-none');
            return;
    }
})

// 자주 쓰는 메모 select
function set_admin_memo(e){
    var text = e.target.innerText;
    $('textarea[name="admin_memo"]').val(text);
}

// 메모 편집창 팝업
function edit_memo(brand){
   let url = main_url+"/order/form/memo/"+brand;
    open_win(url, "MEMO", 565, 600, 850, 200);
}

// 즉시 선택시 오늘 날짜 변경
$('#deli_now_label').on('click',function(){
    const today = new Date().toISOString().slice(0, 10);
    $('input[name="delivery_date"]').val(today);
});

// 전송
function form_submit(e){
    let btn = e.target;

    if(document.querySelector('input[name="mall_code"]') && document.querySelector('input[name="mall_code"]').value === "" ) {
        alert('사업자를 선택해주세요.');
        return;
    }

    if(document.querySelector('input[name="item_total_amount"]').value == 0) {
        alert('상품을 1개이상 선택해주세요.');
        return;
    }

    // 배송날짜 확인 ( 과거 X )
    const inputDate = document.getElementById('delivery_date').value;
    const today = new Date();
    const selectedDate = new Date(inputDate);

    today.setHours(0, 0, 0, 0);
    selectedDate.setHours(0, 0, 0, 0);

    if (selectedDate < today) {
        alert("배송날짜가 과거입니다.\n올바른 날짜를 선택해주세요.");
        return;
    }

    const isNotChecked = document.querySelector('input[name="inflow"]:checked') === null;

    if(isNotChecked) {
        alert("유입 정보를 입력해주세요.");
        return;
    }

    const payType = document.querySelector('input[name="payment_type_code"]:checked').value;

    if(payType == 'PTMN') {
        const keyIn = new bootstrap.Modal('#cardKeyIn');
        keyIn.show();
        return;
    }

    if(payType == "PTVA") {
        const virtualAccount = new bootstrap.Modal('#virtualAccount-modal');
        virtualAccount.show();
        return;
    }

    const form = document.getElementById('order_form');
    const formData = new FormData(form);

    btn.disabled = true;

    $.ajax({
        url : form.action,
        type : "POST",
        data: formData,
        async: false,
        processData: false,
        contentType: false,
        success: function(data) {
            if(payType=='PTVA') {
                $('#payment_area').html(data);
                nicepayStart();
            }else {
                alert(data.msg);
                window.opener.location.reload();
                window.close();
            }
            btn.disabled = false;
        },
        error: function(e) {
            alert('[에러발생]개발팀에 문의하세요.');
            console.log(e);
            btn.disabled = false;
        }
    })
};

// 최근 문구 가져오기
function previous_ribbon() {
    const orderer_name = document.getElementById('orderer_name').value;
    const orderer_phone = document.getElementById('orderer_phone').value;
    
    if(orderer_name == '') {
        alert('주문자명이 필요합니다.');
        return;
    }

    if(orderer_phone == '') {
        alert('주문자 휴대전화번호가 필요합니다.');
        return;
    }


    $.ajax({
        url: main_url + "/order/form/ribbon",
        type: "GET",
        data: {
            // 'vendor_idx' : vendor,
            'orderer_name' : orderer_name,
            'orderer_phone' : orderer_phone
        },
        success: function(data) {
            if(data.status == 1) {
                document.getElementById('delivery_ribbon_left').value = data.ribbon;
            }else{
                alert("일치하는 정보가 없습니다.\n(주문자명 & 주문자휴대전화)")
            }
        }
    });
}

// 카드 키인 - 번호 입력 후 자동 이동
function moveToNext(current, length) {
    const value = current.value.replace(/[^0-9]/g, '');
    current.value = value;
    if(current.value.length >= length) {
        current.nextElementSibling.focus();
    }

}
// 번호 삭제 시 이전 포커스
function checkBackspace(e) {
    if(e.target.value.length == 0 && e.keyCode === 8) {
        e.target.previousElementSibling.focus();
    }
}

// 카드 키인 결제
function pay_keyIn(e) {
    e.target.disabled = true;

    var formData1 = new FormData(document.getElementById('order_form'));
    var formData2 = new FormData(document.getElementById('keyIn_form'));
    var finalForm = new FormData();

    formData1.forEach((value, key) => {
        finalForm.append(key, value);
    })
    formData2.forEach((value, key) => {
        finalForm.append(key, value);
    })

    $.ajax({
        url : main_url + "/order/form",
        type : "POST",
        data: finalForm,
        async: false,
        processData: false,
        contentType: false,
        success: function(data) {
            if(data.status==1) {
                alert(data.msg);
                window.close();
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

// 가상계좌 결제
function pay_virtualAccount(e) {

    const virtualAccount = bootstrap.Modal.getOrCreateInstance(document.getElementById('virtualAccount-modal'));
    virtualAccount.hide();

    const btn = e.target;

    var formData = new FormData(document.getElementById('order_form'));
    var paymentName = document.getElementById('paymentName').value;

    formData.append('paymentName', paymentName);
    btn.disabled = true;

    $.ajax({
        url : main_url + "/order/form",
        type : "POST",
        data: formData,
        async: false,
        processData: false,
        contentType: false,
        success: function(data) {
            $('#payment_area').html(data);
            nicepayStart();
            btn.disabled = false;
        },
        error: function(e) {
            alert('[에러발생]개발팀에 문의하세요.');
            console.log(e);
            btn.disabled = false;
        }
    })
}

// 구매자명에 주문자명 가져오기
function get_ordererName() {
    let ordererName = document.getElementById('orderer_name').value;
    document.querySelector('input[name="paymentName"]').value = ordererName;
}

// 고객정보 가져오기
$('#channel-vendor').on("change", function(e){
    document.getElementById('orderer_mall_id').innerHTML = "";

    $.ajax({
        url: main_url + "/order/form/users",
        type: "GET",
        data:{
            'vendor_idx' : document.querySelector('#channel-vendor').value
        },
        success: function(data){
            document.getElementById('orderer_mall_id').innerHTML = data;
            $('#orderer_mall_id').select2({
                templateResult: formatState2,
                templateSelection: formatResult2,
            });
        },
        error: function(e){
            alert("[에러발생] 개발팀에 문의하세요.")
            console.log(e)
        }
    })
})

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

// 사업자 선택 시 자동 입력
$('#channel-vendor').on('change', function(){
    $('#orderer_name').val($('#channel-vendor option:selected').data('name'))
    $('#orderer_phone').val($('#channel-vendor option:selected').data('phone'))
})

// 유저 선택 시 자동 입력
$('#orderer_mall_id').on('change', function(){
    $('#orderer_name').val($('#orderer_mall_id option:selected').data('name'))
    $('#orderer_phone').val($('#orderer_mall_id option:selected').data('phone'))
})

// 지역추가금 선택
$('#select-location').on('change', function(){
    get_locationPrice();
})

// 지역 추가금 가져오기
function get_locationPrice() {
    
    if(!document.getElementById('product-name')){
        alert('상품을 먼저 선택해주세요.');
        $('#select-location').val('');
        $('#location_modal_close').click();
        return false;
    }

    $.ajax({
        url: main_url + '/order/form/location',
        method: 'GET',
        data: {
            'location': $('#select-location').val(),
            'type': $('#product-name').data('product-type'),
        },
        success: function (data) {
            document.getElementById('loc_price_text').innerText = parseInt(data).toLocaleString()
        },
        error: function (error) {
            alert('[에러발생] 개발팀에 문의하세요')
            console.log(error)
        }
    })
}

// 지역추가금 추가
function add_loc_price() {
    $.ajax({
        url: main_url + '/order/form/location',
        method: 'POST',
        data: {
            'location': $('#select-location').val(),
            'orderProduct_json' : $('#orderProduct_json').val()
        },
        success: function (data) {
            $('#selected-product').html(data);
            document.querySelector('input[name="item_total_amount"]').value = document.getElementById('item_total_amount').dataset.price;
            calc_all_price();
            $('#location_modal_close').click();
        },
        error: function (error) {
            alert('[에러발생] 개발팀에 문의하세요')
            console.log(error)
        }
    })
}
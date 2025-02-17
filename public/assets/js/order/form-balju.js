$('.datepicker').datepicker();

window.onload = function(){
    calc_balju_amount();
}

function find_suju(rose_session) {
    let url = "http://flowercenter.roseweb.co.kr/member_ext/member_search.htm?ms=2&callroseweb=ext_home"
    let return_url = main_url + "/api/order/form-balju/shop";

    url += "&rose_session="+rose_session;
    url += "&var_ret="+return_url;

    open_win(url, "수주화원 검색", "900", '700', '0', '0');
}

function photo_popup(url) {
    open_win(url, "상품사진",500, 500, 50, 50);
}

function balju_check(e) {
    let btn = e.target;

    btn.disabled = true;


    $.ajax({
        url: main_url + "/order/Log",
        type : 'GET',
        data : {
            'order_idx' : document.querySelector("input[name='order_idx']").value,
        },
        success : function(data){
            if(data) {
                order_submit();
            }else {
                if(confirm('이미 발주 된 주문입니다. [재발주] 하시겠습니까?')){
                    order_submit();
                }
            }
            btn.disabled = false;
        },
        error : function(e) {
            alert('[전송 실패] 개발팀에 문의해주세요.')
            console.log(e);
        }
    })

}

function order_submit() {
    let option_name = document.querySelectorAll('input[name="option_name[]"]');
    var cnt = 0;

    option_name.forEach(option => {
        if(option.value=="") cnt++;
    });

    if(cnt > 1) {
        alert("옵션명은 빈칸을 사용 할 수 없습니다.")
        return false;
    }

    let form = document.getElementById('order_form');
    let formData = new FormData(form);

    $.ajax({
        url: form.action,
        type: "POST",
        data : formData,
        async: false,
        processData: false,
        contentType: false,
        success: function(data) {
            if(data.state==1) {
                Swal.fire({
                    title: "발주 완료",
                    showDenyButton: false,
                    showCancelButton: false,
                    confirmButtonText: "확인",
                    // denyButtonText: `Don't save`
                }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                        window.opener.location.reload();
                        window.close();
                    }
                });

            }else {
                // alert(data.msg);
                Swal.fire({
                    title: "에러 발생!",
                    html: data.msg.replace(/\n/g, '<br>'),
                    icon: "error"
                });
            }
        },
        error: function(e) {
            Swal.fire({
                title: "에러 발생!",
                icon: "error"
            });
            console.log(e);
        }
    })


}

//  옵션 합 계산
function calculate_options_amount() {
    const vendor_options = document.querySelectorAll('input[name="vendor_option_price[]"]');
    const balju_options = document.querySelectorAll('input[name="balju_option_price[]"]');

    let vendor_options_amount = 0;
    let balju_options_amount = 0;

    vendor_options.forEach(option => {
        let vendorValue = parseInt(option.value);
        if (!isNaN(vendorValue)) vendor_options_amount += vendorValue;
    })
    balju_options.forEach(option => {
        let baljuValue = parseInt(option.value);
        if (!isNaN(baljuValue)) balju_options_amount += baljuValue;
    })

    document.getElementById('vendor_options_amount').value = vendor_options_amount;
    document.getElementById('balju_options_amount').value = balju_options_amount;
}

// 옵션 합 계산
$('#options-container').on('input', '.option-price-input', function (){
    calculate_options_amount();
})

// 상품 옵션 추가
$('#add-option-btn').on('click', function() {
    let template = $('#option-template').html();
    $('#options-container').append(template);
});

// 상품 옵션 삭제
$('#options-container').on('click', '.remove-option-btn', function() {
    $(this).closest('.product-table-row').remove();
    calculate_options_amount()
});
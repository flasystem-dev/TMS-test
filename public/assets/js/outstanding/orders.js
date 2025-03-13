var fix = 0;
$('.datepicker').datepicker();

document.getElementById('search-form').addEventListener('submit', function(e) {
    e.preventDefault();

    const checkboxes = document.querySelectorAll('input[name="brand"]');
    const isChecked = Array.from(checkboxes).some(checkbox => checkbox.checked);

    if (!isChecked) {
        alert("하나 이상의 브랜드를 선택해야 합니다.");
        return false;
    }
    e.target.submit();
})

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

function order_detail(order_idx){
    var url = main_url + '/order/order-detail/'+order_idx;

    open_win(url,"주문서"+fix,1440,900,0,0);
    fix++;
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
$('#search_form').on('submit', function(event){
    if($('input[name="brand"]:checked').length === 0) {
        alert("브랜드를 선택해주세요.")
        event.preventDefault();
        return false;
    }
})

// 선택 한 주문만 보기
function select_orders_view() {
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

    var form = new FormData();

    // 배열 데이터를 form에 추가
    $.each(checkedCheckboxes, function(index, value) {
        form.append('order_idx[]', value);
    });

    $.ajax({
        url: main_url + "/order/detail-list/select-orders",
        method: "POST",
        data: form,
        processData: false, // jQuery가 데이터를 처리하지 않도록 설정
        contentType: false, // 컨텐츠 타입을 설정하지 않음 (기본적으로 multipart/form-data 사용)
        success: function(data) {
            if(data){
                const currentUrl = window.location.origin + window.location.pathname;
                location.href = currentUrl;
            }else {
                alert("조회 실패")
            }
        },
        error: function(e){
            alert('[에러발생] 개발팀에 문의하세요.')
            console.log(e)
        }
    })

}
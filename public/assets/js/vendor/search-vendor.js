let cnt = 0 ;

$('input[name="select_check"]').on('change', function(e){

    if($(this).is(':checked')) {
        $('tr').css({'background-color': '#fff'})
        $(this).closest('tr').css({'background-color': '#ccf3ff'})
    }

    var vendor_idx = $(this).data('id');
    $.ajax({
        url: main_url + "/vendor/search/orders",
        method: "GET",
        data: { 'vendor_idx':vendor_idx },
        success: function(data){
            document.getElementById('order_table').innerHTML = data;
            const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]')
            const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl))
        },
        error: function(e) {
            alert("[에러발생] 개발팀에 문의하세요.");
            console.log(e)
        }
    })
})

function order_detail(order_idx) {
    cnt++;
    var url = main_url + "/order/order-detail/" + order_idx;
    open_win(url, "상세주문서"+cnt, 1440, 720, 50, 50)
}
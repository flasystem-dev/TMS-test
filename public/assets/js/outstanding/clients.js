var fix = 0;
$('.datepicker').datepicker();

$('#client-table').DataTable({
    paging: true,
    lengthMenu: [[20, 50 , 100, -1], [20, 50 , 100, "전체"]],
    pageLength: 20, // 기본 페이지 길이 설정,
    "order": [],
    "columnDefs": [
        { "targets": [0, 2, 9], "orderable": false },
        {
            "targets": 1, // 첫 번째 열을 순서 열로 설정
            "searchable": false, // 검색 영향 없음
            "orderable": false, // 정렬 영향 없음
        },
        { "targets": 3, "orderable": true, "type": "date" }, // 3번째 열 날짜 정렬
        { "targets": [4, 5, 6, 7, 8], "orderable": true, "type": "num" },
    ],
    "fnDrawCallback": function(settings) {
        if (!settings.aoData || settings.aoData.length === 0) return;

        var start = settings._iDisplayStart; // 현재 페이지 시작 번호

        // 현재 표시된 행만 순회
        $('#client-table tbody tr').each(function(index) {
            var $tds = $(this).find('td');
            if ($tds.length > 1) {
                $tds.eq(1).text(start + index + 1);
            }
        });
    }
});

document.getElementById('search-form').addEventListener('submit', function(e) {
    e.preventDefault();

    const checkboxes = document.querySelectorAll('input[name="brand[]"]');
    const isChecked = Array.from(checkboxes).some(checkbox => checkbox.checked);

    if (!isChecked) {
        alert("하나 이상의 브랜드를 선택해야 합니다.");
        return false;
    }
    e.target.submit();
})

// 거래처 정보 팝업
document.querySelectorAll('.client-info').forEach(function(element){
    element.addEventListener('click', function(e) {
        let tr = this.closest('tr');
        let client_id = tr.querySelector('input[name="client_id[]"]').value;

        console.log(vendor_idx);
        let url = main_url + "/document/client/client-form/" + client_id;
        open_win(url, '거래처 정보', 1100, 800, 500, 50)
    })
});

// 미수 주문 정보 이동
document.querySelectorAll('.misu-orders').forEach(function(element){
    element.addEventListener('click', function(e) {
        let tr = this.closest('tr');
        let client_id = tr.querySelector('input[name="client_id[]"]').value;
        let type = this.dataset.type;
        let client_name = tr.querySelector('.client-name').textContent;
        let channel_name = tr.querySelector('.channel-name').textContent;
        let brand = tr.querySelector('.client-name').dataset.brand;

        const params = new URLSearchParams(window.location.search);
        params.set('brand', brand);
        params.set('search1', 'rep_name');
        params.set('search_word1', channel_name);
        params.set('search2', 'client_name');
        params.set('search_word2', client_name);

        switch (type) {
            case 'past':
                params.set('date_type', 'delivery_date');
                params.set('end_date', getPreviousMonthLastDay());
                break;
        }

        const orderUrl = main_url + "/outstanding/orders";

        const newUrl = `${orderUrl}?${params.toString()}`;

        window.open(newUrl, '_blank');
    })
});

// 전전달 마지막 날 가져오기
function getPreviousMonthLastDay() {
    let today = new Date();
    let lastDayOfPreviousMonth = new Date(today.getFullYear(), today.getMonth() - 1, 0);

    let year = lastDayOfPreviousMonth.getFullYear();
    let month = String(lastDayOfPreviousMonth.getMonth() + 1).padStart(2, '0'); // 1~9월 앞에 0 추가
    let day = String(lastDayOfPreviousMonth.getDate()).padStart(2, '0');

    return `${year}-${month}-${day}`;
}
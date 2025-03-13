var fix = 0;
$('.datepicker').datepicker();

$('#vendor-table').DataTable({
    paging: true,
    lengthMenu: [[20, 50 , 100, -1], [20, 50 , 100, "전체"]],
    pageLength: 20, // 기본 페이지 길이 설정,
    "order": [],
    "columnDefs": [
        { "targets": [0, 2, 10], "orderable": false },
        {
            "targets": 1, // 첫 번째 열을 순서 열로 설정
            "searchable": false, // 검색 영향 없음
            "orderable": false, // 정렬 영향 없음
        },
        { "targets": 3, "orderable": true, "type": "date" }, // 3번째 열 날짜 정렬
        { "targets": [4, 5, 6, 7, 8, 9], "orderable": true, "type": "num" },
    ],
    "fnDrawCallback": function(settings) {
        if (!settings.aoData || settings.aoData.length === 0) return;

        var start = settings._iDisplayStart; // 현재 페이지 시작 번호

        // 현재 표시된 행만 순회
        $('#vendor-table tbody tr').each(function(index) {
            var $tds = $(this).find('td');
            if ($tds.length > 1) {
                $tds.eq(1).text(start + index + 1);
            }
        });
    }
});

// 검색 필터
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

// 사업자 정보 팝업
document.querySelectorAll('.vendor-info').forEach(function(element){
    element.addEventListener('click', function(e) {
        let tr = this.closest('tr');
        let vendor_idx = tr.querySelector('input[name="vendor_idx[]"]').value;

        console.log(vendor_idx);
        let url = main_url + "/vendor/fla-business/view/" + vendor_idx;
        open_win(url, '사업자 정보', 1100, 800, 500, 50)
    })
});

// 미수 주문 정보 이동
document.querySelectorAll('.misu-orders').forEach(function(element){
    element.addEventListener('click', function(e) {
        let tr = this.closest('tr');
        let vendor_idx = tr.querySelector('input[name="vendor_idx[]"]').value;
        let type = this.dataset.type;
        let name = tr.querySelector('.channel-name').textContent;

        const params = new URLSearchParams(window.location.search);

        params.set('search1', 'rep_name');
        params.set('search_word1', name);

        switch (type) {
            case 'personal':
                params.set('is_client', '0');
                break;
            case 'client':
                params.set('is_client', '1');
                break;
            case 'longTerm':
                params.set('date_type', 'delivery_date');
                params.set('end_date', getPreviousMonthLastDay());
                break;
            case 'total':
                break;
        }

        const orderUrl = main_url + "/outstanding/orders";

        const newUrl = `${orderUrl}?${params.toString()}`;

        window.open(newUrl, '_blank');
    })
});

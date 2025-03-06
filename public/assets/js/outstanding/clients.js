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
var count = 0;

// 매출 데이터테이블
const table = $('#vendor_tbl').DataTable({
    'paging' : false,
    "lengthChange": false,
    "info": false,
    // "searching": false,
    'order': [],
    "columnDefs": [{
        "targets": 0, // 첫 번째 열을 순서 열로 설정
        "searchable": false, // 검색 영향 없음
        "orderable": false, // 정렬 영향 없음
    }],
    "fnDrawCallback": function(settings) {
        // 현재 페이지의 시작 인덱스 계산
        var start = settings._iDisplayStart;

        // 각 행에 대해 순서 번호를 설정
        $('tbody tr').each(function(index) {
            $(this).find('td').eq(0).html(start + index + 1);
        });
    }
});

// 상단 검색 변경 시 페이지 이동
$('input[name="dateType"], select[name="select_year"], select[name="select_month"]').on('change', function(){
    const brand = $('input[name="brand"]').val();
    const dateType = $('input[name="dateType"]:checked').val();
    const year = $('#select_year').val();
    const month = $('#select_month').val();
    const recommend = $('input[name="recommend"]').val();

    const params = new URLSearchParams({
        brand: brand,
        dateType: dateType,
        year: year,
        month: month,
        recommend: recommend
    });

    const targetUrl = `${window.location.pathname}?${params.toString()}`;

    window.location.href = targetUrl;
})

// 데이터테이블 추천인 검색
function search_name(name) {
    var table = $('#vendor_tbl').DataTable(); // DataTable 인스턴스를 가져옵니다.
    table.search(name).draw(); // 검색어를 설정하고, 테이블을 다시 그립니다.
}

// 데이터테이블 검색어 초기화
function reset_search() {
    var table = $('#vendor_tbl').DataTable(); // DataTable 인스턴스를 가져옵니다.
    table.search('').draw();
}

// 명세서 리스트 팝업
function open_specifications(idx) {
    let url = main_url + "/statistics/vendor/specifications/" + idx
    open_win(url, "specifications"+count, 1600, 800, 50, 50);
    count++;
}

// 추천인 리스트 팝업
function recommendPerson(idx) {
    let url = main_url + "/statistics/vendor/recommend"

    const brand = $('input[name="brand"]').val();
    const dateType = $('input[name="dateType"]:checked').val();
    const year = $('#select_year').val();
    const month = $('#select_month').val();

    const params = new URLSearchParams({
        brand: brand,
        dateType: dateType,
        year: year,
        month: month,
        recommend: idx
    });

    const targetUrl = `${url}?${params.toString()}`;

    open_win(targetUrl, '추천인'+count, 1500, 900, 50, 50);
    count++;
}

// 사업자 매출 캘린더 팝업
function vendor_calendar(idx) {
    let url = main_url + "/statistics/vendor/calendar/" + idx;

    const dateType = $('input[name="dateType"]:checked').val();
    const year = $('#select_year').val();
    const month = $('#select_month').val();

    const params = new URLSearchParams({
        dateType: dateType,
        year: year,
        month: month,
    });

    const targetUrl = `${url}?${params.toString()}`;

    open_win(targetUrl, 'vendor-calendar', 1300, 820, 200, 50);
}
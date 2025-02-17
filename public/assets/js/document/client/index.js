$('#clients_tbl').DataTable({
    paging: true,
    lengthMenu: [[20, 50 , 100, -1], [20, 50 , 100, "전체"]],
    pageLength: 20, // 기본 페이지 길이 설정,
    "columnDefs": [
        {
            "targets": 0, // 첫 번째 열을 순서 열로 설정
            "searchable": false, // 검색 영향 없음
            "orderable": false, // 정렬 영향 없음
        }
    ],
    "fnDrawCallback": function(settings) {
        // 현재 페이지의 시작 인덱스 계산
        var start = settings._iDisplayStart;

        // 데이터가 있는 경우만 순서 번호를 설정
        if (settings.aoData.length > 0) {
            $('tbody tr').each(function(index) {
                // 현재 행이 데이터 행인지 확인
                var rowData = settings.aoData[$(this).index()];

                // 데이터가 있는 경우에만 순서 번호 설정
                if (rowData && rowData._aData) {
                    $(this).find('td').eq(0).html(start + index + 1);
                } else {
                    $(this).find('td').eq(0).html(''); // 데이터가 없으면 비우기
                }
            });
        }
    }
});

// 거래처 추가
$('#clientForm').on('click',function(){
    const url = main_url + '/document/client/client-form/0';
    open_win(url,'거래처추가', 900,900,50,50);
})


// 거래처 수정 폼 팝업
function clientForm(id) {
    const url = main_url + "/document/client/client-form/" + id;
    open_win(url,'거래처수정', 900,900,50,50);
}
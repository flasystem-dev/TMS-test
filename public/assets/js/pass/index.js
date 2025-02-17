$('#passes_tbl').DataTable({
    paging: true,
    lengthMenu: [[20, 50 , 100, -1], [20, 50 , 100, "전체"]],
    pageLength: 20, // 기본 페이지 길이 설정,
    "columnDefs": [
        {
            "targets": 0, // 첫 번째 열을 순서 열로 설정
            "searchable": false, // 검색 영향 없음
            "orderable": false, // 정렬 영향 없음
        },
        {
            targets: [6, 7], // 체크박스가 있는 열의 인덱스
            orderable: true, // 정렬 가능 설정
            render: function(data, type, row) {
                // 정렬용: 체크박스 상태 반환
                if (type === 'sort') {
                    return $(data).prop('checked') ? 1 : 0;
                }
                // 디스플레이용: 그대로 반환
                return data;
            }
        },
    ],
    "fnDrawCallback": function(settings) {
        // 현재 페이지의 시작 인덱스 계산
        var start = settings._iDisplayStart;

        // 각 행에 대해 순서 번호를 설정
        $('tbody tr').each(function(index) {
            $(this).find('td').eq(0).html(start + index + 1);
        });
    }
});

// Pass추가
$('#passForm').on('click',function(){
    const url = main_url + '/pass/pass-form/0';
    open_win(url,'Pass추가', 800,800,50,50);
})

// 상태, 선발주 변경
$('input[name="is_valid"], input[name="is_credit"]').on('change', function(e){
    $.ajax({
        url: main_url + "/pass/simple/status",
        method: "POST",
        data:{
            'column' : $(this).attr('name'),
            'id' : $(this).data('index'),
            'check' : $(this).is(':checked') ? "1" : "0"
        }
    }).then(data=>{
        if(data){
            location.reload();
        }else{
            alert("변경 실패")
            location.reload();
        }
    }).catch(e=>{
        alert("변경 실패")
        console.log(e)
    })
})

// 패스 수정 폼 팝업
function passForm(id) {
    const url = main_url + "/pass/pass-form/" + id;
    open_win(url,'Pass수정', 800,800,50,50);
}
$('#user_list_tbl').DataTable({
    paging: true,
    // columnDefs: [
    //     { orderable: false, targets: [0, 1, 2, 4, 11 ] } // 정렬을 비활성화할 열의 인덱스
    // ],
    lengthMenu: [[20, 50 , 100, -1], [20, 50 , 100, "전체"]],
    pageLength: 20, // 기본 페이지 길이 설정,
    "columnDefs": [
        {
            "targets": 0, // 첫 번째 열을 순서 열로 설정
            "searchable": false, // 검색 영향 없음
            "orderable": false, // 정렬 영향 없음
        },
        {
            targets: [7, 8], // 체크박스가 있는 열의 인덱스
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

// 회원추가
$('#memberForm').on('click',function(){
    const url = main_url + '/user/user-form/0';
    open_win(url,'회원추가', 800,800,50,50);
})

// 유저 정보 폼
function userUpdateForm(id){
    const url = main_url + '/user/user-form/'+id;
    open_win(url,'회원수정', 800,800,50,50);

}

// 검색어 폼
$('#search_form').on('submit', function(event){
    if($('input[name="brand"]:checked').length === 0) {
        alert("브랜드를 선택해주세요.")
        event.preventDefault();
        return false;
    }
})

// 체크박스의 현재 상태를 저장
$('input[name="status"], input[name="is_credit"]').on('focus', function() {
    $(this).data('previous', $(this).is(':checked'));
});

// 상태, 선발주 변경
$('input[name="status"], input[name="is_credit"]').on('change', function(e){
    Swal.fire({
        title: "수정하시겠습니까 ?",
        showDenyButton: true,
        confirmButtonText: "수정",
        denyButtonText: `취소`,

    }).then((result) => {

        if (result.isConfirmed) {
            $.ajax({
                url: main_url + "/user/simple/status",
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
                }
            }).catch(e=>{
                alert("변경 실패")
                console.log(e)
            })
        }else if (result.isDenied) {
            $(this).prop('checked', $(this).data('previous'));
        }else if (result.isDismissed) {
            $(this).prop('checked', $(this).data('previous'));
        }
    });

})
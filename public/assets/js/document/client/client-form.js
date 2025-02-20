$('.datepicker').datepicker();

// 거래처 업서트
function clientUpsert() {
    const form = document.getElementById('client_form');
    const formData = new FormData(form);

    if(confirm("저장하시겠습니까?")) {
        $.ajax({
            type: "POST",
            url: form.action,
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            success: function (data) {
                if(data.status){
                    opener.location.reload();
                    location.href = data.url;
                }else {
                    alert("수정 실패");
                }
            }, error: function (error) {
                alert("문제 발생");
                console.log(error);
            },
        });
    }
}

// 담당자 테이블
$('#manager_table').DataTable({
    paging : false,
    lengthChange: false,
    info: false,
    searching: false,
});

// 담당자 업서트
function managerUpsert() {
    if(confirm("저장하시겠습니까?")) {
        const form = document.getElementById('manager_form')
        const formData = new FormData(form)
        $.ajax({
            type: "POST",
            url: main_url + "/document/client/manager-form",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            success: function (data) {
                if(data){
                    opener.location.reload();
                    location.reload();
                }else {
                    alert("저장 실패");
                }
            }, error: function (error) {
                alert("문제 발생");
                console.log(error);
            },
        });
    }
}

// 담당자 폼 가져오기
document.getElementById('manager_form_modal').addEventListener('show.bs.modal', event => {
    const index = event.relatedTarget.dataset.index;

    $.ajax({
        url: main_url + "/document/client/manager-form/" + index,
        method: "GET",
        success: function(data) {
            document.getElementById('manager_form_body').innerHTML = data;
        },
        error: function(e) {
            alert('가져오기 실패');
            console.log(e)
        }
    })
});

// 담당자 삭제하기
function removeManager(id) {
    if(confirm("정말 담당자를 삭제하시겠습니까?")) {
        $.ajax({
            url: main_url + "/document/client/manager/" + id,
            method: "DELETE",
            success: function(data) {
                location.reload();
            },
            error: function(e) {
                alert('삭제실패 실패');
                console.log(e)
            }
        })
    }
}

// 회원 테이블
$('#user_table').DataTable({
    paging : false,
    lengthChange: false,
    info: false,
    searching: false,
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
            $('#user_table tbody tr').each(function(index) {
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

// 회원 추가 - 회원 정보 가져오기
$("#user_brand").on('change', function(){
    const brand = $("#user_brand").val();
    
    $.ajax({
        url: main_url + "/document/client/user-form/" + brand,
        method: "GET",
        success: function(data) {
            $('#user_select').html(data)
            $('#user_select').select2({
                placeholder: "채널명, 이름, ID, 사업자",
                dropdownParent : $('#user_form_modal'),
                templateResult: formatState,
                templateSelection: formatResult,
            });
        },
        error: function(e) {
            alert("정보 가져오기 실패");
            console.log(e);
        }
    });
})

// 회원 select 옵션 css
function formatState(state){
    if (!state.id) {
        return state.text;
    }

    var $state = $(
        '<span class="user_channel">' + $(state.element).data('channel') + '</span>' +
        '<span class="user_name">' + $(state.element).data('name') + '</span>' +
        '<span class="user_id">' + $(state.element).data('id') + '</span>' +
        '<span class="user_vendor text-success">' + $(state.element).data('vendor') + '</span>'
    );

    return $state;
}

// 회원 select 선택 후 css
function formatResult(state){
    if (!state.id) {
        return state.text;
    }

    var $state = $(
        '<span class="user_channel">' + $(state.element).data('channel') + '</span>' +
        '<span class="user_name">' + $(state.element).data('name') + '</span>' +
        '<span class="user_id">' + $(state.element).data('id') + '</span>' +
        '<span class="user_vendor text-success">' + $(state.element).data('vendor') + '</span>'
    );

    return $state;
}

// 거래처 - 회원 등록
function userRegister() {
    const form = document.getElementById('user_form')
    const formData = new FormData(form)

    if(confirm("등록하시겠습니까?")) {
        const form = document.getElementById('user_form')
        const formData = new FormData(form)
        $.ajax({
            type: "POST",
            url: main_url + "/document/client/user-form",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            success: function (data) {
                if(data){
                    location.reload();
                }else {
                    alert("등록 실패");
                }
            }, error: function (error) {
                alert("문제 발생");
                console.log(error);
            },
        });
    }
}

// 회원 수정
function userForm(id) {
    const url = main_url + '/user/user-form/'+id;
    open_win(url,'회원수정', 800,800,1000,50);
}

// 회원 - 담당자 수정
$('#manager_id').on('change', function(){
    if(confirm("담당자를 변경하시겠습니까?")) {
        $.ajax({
            url: main_url + "/document/client/user/manager",
            method: "patch",
            data: {
                user: $(this).data('index'),
                manager: $(this).val()
            },
            success: function(data){
                if(data){
                    location.reload();
                }else{
                    alert("변경 실패")
                }
            },
            error: function(e){
                alert("변경 실패")
                console.log(e)
            }
        })
    }
});
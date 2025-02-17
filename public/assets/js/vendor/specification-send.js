$(document).ready(function () {
    $('#specification_table').DataTable({
        paging: true,
        columnDefs: [
            { orderable: false, targets: [] } // 정렬을 비활성화할 열의 인덱스
        ],
        lengthMenu: [[50 , 100, -1], [50, 100, "전체"]],
        pageLength: 50, // 기본 페이지 길이 설정,
        createdRow:function(row,data,dataIndex){
            $('td:eq(1)', row).css({
                'white-space': 'nowrap',
                'max-width': '100px',
                'overflow': 'hidden',
                'text-overflow': 'ellipsis'
            });
        }
    });
    $('#select_email_all, #select_tel_all').on('click', function(e){
        e.stopPropagation();
    });
});

$('#select_email_all').on('click', function() {
    var isChecked = $(this).is(':checked');
    $('input[name="email[]"]').prop('checked', isChecked);
});

$('#select_tel_all').on('click', function() {
    var isChecked = $(this).is(':checked');
    $('input[name="tel[]"]').prop('checked', isChecked);
});

$('#select_id_all').on('click', function() {
    var isChecked = $(this).is(':checked');
    $('input[name="sp_id[]"]').prop('checked', isChecked);
    checked_sp_id();
});

$('input[name="sp_id[]"]').on('change', function(){
    checked_sp_id();
});

function specification_popup(id) {
    let url = main_url + "/vendor/specification-form/" + id
    open_win(url,"명세서" ,900, 900, 0, 40);
}

function send_email() {
    var year = document.querySelector('select[name="year"]').value;
    var month = document.querySelector('select[name="month"]').value;

    // 체크된 체크박스의 값을 배열로 가져옴
    var selectedEmails = [];
    $('input[name="email[]"]:checked').each(function() {
        selectedEmails.push($(this).val());
    });

    if(selectedEmails.length === 0) {
        alert('메일 전송 할 곳을 선택해 주세요.')
        return false;
    }

    // 배열을 폼 데이터로 변환
    var formData = new FormData();
    selectedEmails.forEach(function(email) {
        formData.append('idx[]', email);
    });
    formData.append('year', year)
    formData.append('month', month)

    // AJAX 요청
    $.ajax({
        url: main_url + '/vendor/specification/send/email', // 서버 엔드포인트 URL
        type: 'POST',
        data: formData,
        processData: false, // FormData 객체로 전송할 때는 false로 설정
        contentType: false, // contentType도 false로 설정
        success: function(data) {
            if(data){
                alert('전송 완료')
            }else{
                alert('전송 실패!')
            }

        },
        error: function(error) {
            alert('전송 중 오류가 발생했습니다.');
            console.log(error); // 에러 로그 출력
        }
    });
}

// 알림톡용 엑셀 다운로드
function talk_excelDownload() {
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    const year = urlParams.get('year');
    const month = urlParams.get('month');
    const brand = urlParams.get('brand');

    let date = year+"년"+month+"월";
    var brand_name = "[플체]";
    if(brand==="BTCS") {
        brand_name = "[꽃사]";
    }
    let file = brand_name+'알림톡_명세서_'+date+'.xlsx';

    $.ajax({
        url: main_url + '/vendor/specification/send/talk/excel' + queryString,  // 쿼리 스트링을 URL에 추가
        type: 'GET',
        xhrFields: {
            responseType: 'blob'  // 이진 데이터를 받아야 하므로 'blob'으로 설정
        },
        success: function(data) {
            const blob = new Blob([data], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
            const link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.download = file;
            link.click();
        },
        error: function(error) {
            alert('다운로드 실패')
            console.log(error);
        }
    });
}

function delete_specification() {
    var selectedSpecification = [];
    $('input[name="sp_id[]"]:checked').each(function() {
        selectedSpecification.push($(this).val());
    });

    if(selectedSpecification.length === 0) {
        alert('삭제 할 명세서를 선택해 주세요.');
        return false;
    }

    if(confirm('선택 된 명세서를 삭제하시겠습니까?')) {

        $.ajax({
            url : main_url + "/vendor/specification/id",
            type: "delete",
            data: { 'sp_id' : selectedSpecification },
            success: function(data) {
                alert('삭제 완료');
                location.reload();
            },
            error: function (e) {
                alert("삭제 실패");
                console.log(e)
            }
        })
    }
}

function checked_sp_id() {
    let id_elements = document.querySelectorAll('input[name="sp_id[]"]');

    id_elements.forEach((ele) => {
        let trElement = ele.closest('tr');
        if(ele.checked) {
            trElement.classList.add('checked');
        }else {
            trElement.classList.remove('checked');
        }
    });
}
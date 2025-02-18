$(document).ready(function () {
    $('input[name="excel_start_date"]').datepicker();
    $('input[name="excel_end_date"]').datepicker();

    $('#excel_start_date, #excel_end_date').on('change', validateDateRange);
});


function excel_batch_download() {
    var checkedCheckboxes = document.querySelectorAll('input[name="excel_brand"]:checked');
    if (checkedCheckboxes.length === 0) {
        alert('브랜드를 하나 이상 선택해주세요.');
        return false;
    }

    const form = new FormData(document.getElementById('excel_bulk_download_form'))

    // 오늘 날짜 YYYYmmdd
    let today = new Date();
    let year = today.getFullYear();
    let month = String(today.getMonth() + 1).padStart(2, '0'); // 월은 0부터 시작하므로 1을 더함
    let day = String(today.getDate()).padStart(2, '0'); // 날짜를 두 자리로 맞춤
    var today_str = year + month + day;

    $.ajax({
        url: main_url + '/order/excel/download/batch',
        type: 'POST',
        data: form,
        processData: false,  // FormData를 사용하면 반드시 false로 설정해야 함
        contentType: false,  // 파일 업로드 시에도 반드시 false로 설정
        // xhrFields: {
        //     responseType: 'blob'  // 이진 데이터를 받아야 하므로 'blob'으로 설정
        // },
        success: function(data) {
            alert(data.message);
        },
        error: function(error) {
            alert('다운로드 실패')
            console.log(error);
        }
    });
}

// 최대 1개월 설정
function validateDateRange() {
    var startDate = new Date(document.getElementById('excel_start_date').value);
    var endDate = new Date(document.getElementById('excel_end_date').value);

    // 현재 end_date에서 3개월이 지났는지 확인
    // var maxEndDate = new Date(startDate);
    // maxEndDate.setMonth(maxEndDate.getMonth() + 1); // 시작 날짜로부터 3개월 후 날짜

    // 시작날짜 종료날짜 확인
    if (endDate < startDate) {
        alert('종료 날짜는 시작 날짜보다 작을 수 없습니다.');
        document.getElementById('excel_end_date').value = document.getElementById('excel_start_date').value; // end_date를 start_date로 설정
    }

    // 최대 3개월 확인
    // if (endDate > maxEndDate) {
    //     alert('최대 1개월까지 선택할 수 있습니다.');
    //     // end_date 값을 3개월 후로 자동 설정
    //     document.getElementById('excel_end_date').value = maxEndDate.toISOString().split('T')[0]; // YYYY-MM-DD 형식으로 설정
    // }
}

function excel_date_select(date) {
    const now = new Date();

    var firstDay = now;
    var lastDay = now;

    switch (date) {
        case '2monthAgo' :
            firstDay = new Date(now.getFullYear(), now.getMonth() - 2, 1);
            lastDay = new Date(now.getFullYear(), now.getMonth() - 1, 0);
            break;
        case 'monthAgo' :
            firstDay = new Date(now.getFullYear(), now.getMonth() - 1, 1);
            lastDay = new Date(now.getFullYear(), now.getMonth(), 0);
            break;
        case 'thisMonth' :
            firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
            break;
        case 'recent2Month' :
            firstDay = new Date(now.getFullYear(), now.getMonth() - 2, 1);
            break;
    }

    document.querySelector('input[name="excel_start_date"]').value = formatDateToInput(firstDay);
    document.querySelector('input[name="excel_end_date"]').value = formatDateToInput(lastDay);
}

function formatDateToInput(date) {
    const year = date.getFullYear();
    const month = (date.getMonth() + 1).toString().padStart(2, '0'); // 월을 2자리로 맞춤
    const day = date.getDate().toString().padStart(2, '0'); // 일을 2자리로 맞춤
    return `${year}-${month}-${day}`;
}
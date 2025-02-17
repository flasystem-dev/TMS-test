var cnt = 0;

// 데이터테이블 로드
$(document).ready(function () {
    $('.datepicker').datepicker();
    $('#datatable').DataTable({
        paging: true,
        columnDefs: [
            { orderable: false, targets: [0, 1, 2, 11 ] }, // 정렬을 비활성화할 열의 인덱스
            {
                targets: 4, // 숫자와 <br>로 구분된 데이터가 있는 열의 인덱스
                orderable: true, // 정렬 가능하게 설정
                render: function(data, type, row, meta) {
                    if (type === 'sort') {
                        const html = $('<div>').html(data);
                        const firstLine = html.text().split('\n')[0].trim().replace(/,/g, '');
                        return parseFloat(firstLine) || 0;
                    }
                    // 디스플레이는 원본 데이터를 그대로 반환
                    return data;
                }
            }
        ],
        lengthMenu: [[50, 100, -1], [50, 100, "전체"]],
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
    $(".dataTables_length select").addClass('form-select form-select-sm');

    $("#all_check").click(function() {
        if($("#all_check").is(":checked")) $(".spec_check").prop("checked", true);
        else $(".spec_check").prop("checked", false);
    });

    $(".spec_check").click(function() {
        var total = $(".spec_check").length;
        var checked = $(".spec_check:checked").length;

        if(total != checked) $("#all_check").prop("checked", false);
        else $("#all_check").prop("checked", true);
    });

    // 브랜드 변경 시 리로드
    $('input[name="brand"]').change(function() {
        $('#search_from').submit();
    });
    // 정산 년 변경 시 리로드
    $('#select_year').change(function() {
        $('#search_from').submit();
    });
    // 정산 월 변경 시 리로드
    $('#select_month').change(function() {
        $('#search_from').submit();
    });
});

function select_btn(type,title,col){
    $('#'+type+"_title").text(title);
    $('#'+type).val(col);
    $('#'+type+"_view").val(title);
}

// 명세서 폼 양식
function specification_view(id) {
    const url = "./specification-form/"+id;
    open_win(url,"명세서" ,900, 900, 0, 40);
}

// 기타 금액 프레임 다운로드
function vendorExcelDownload(brand){
    location.href="./vendor-excel-example?brand="+brand;
}

// 명세서 발급
$("#spec_btn").on("click", function (e) {
    const checkboxes = document.querySelectorAll('input[name="mall_code[]"]');
    const isChecked = Array.from(checkboxes).some(checkbox => checkbox.checked);
    if (!isChecked) {
        alert('하나 이상의 사업주를 선택해 주세요.');
        return false;
    }

    if(document.getElementById('deposit_date').value=="") {
        alert('입금예정일을 선택해 주세요.');
        return false;
    }

    if(confirm("선택한 사업주의 명세서를 발급하시겠습니까?")) {
        var specification_form = document.getElementById('specification_form');
        var formData = new FormData(specification_form);
        var depositDate = $('#deposit_date').val();
        formData.append('deposit_date', depositDate);

        $.ajax({
            type: "POST",
            url: main_url + "/vendor/monthly-specification",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            success: function (data) {
                if(data){
                    alert('저장되었습니다.');
                    location.reload();
                }else{
                    alert("발급 실패")
                }
            }, error: function (error) {
                console.log(error);
                alert('오류가 발생했습니다. 개발팀에 문의해주세요.');
            },
        });
    }
});

// 기타 금액 업로드
$("#etc_excel_upload").on("click", function (e) {
    document.getElementById('etc_excel_upload').disabled=true;
    if(confirm("저장하시겠습니까?")) {
        $.ajax({
            type: "POST",
            url: main_url + '/vendor/monthly-etc-upload',
            data: new FormData(document.getElementById('excel_upload_form')),
            contentType: false,
            cache: false,
            processData: false,
            success: function (data) {
                alert(data.message);
                location.reload();
                document.getElementById('etc_excel_upload').disabled=false;
            }, error: function (error) {
                console.log(error);
                alert('오류가 발생했습니다. 개발팀에 문의해주세요.');
            },
        });
    }
});

// 사업자 알림톡 엑셀 다운로드
function vendorAlimExcelDownload(brand,year,month){
    location.href="./vendor-excel-alim?brand="+brand+"&year="+year+"&month="+month;
}

// 정산 리스트 엑셀 다운로드
function download_calc_excel() {
    // 오늘 날짜 YYYYmmdd
    let today = new Date();
    let year = today.getFullYear();
    let month = String(today.getMonth() + 1).padStart(2, '0'); // 월은 0부터 시작하므로 1을 더함
    let day = String(today.getDate()).padStart(2, '0'); // 날짜를 두 자리로 맞춤
    var today_str = year + month + day;

    $.ajax({
        url: main_url + '/vendor/vendor-excel-calcAmount' + window.location.search,  // 쿼리 스트링을 URL에 추가
        type: 'POST',
        xhrFields: {
            responseType: 'blob'  // 이진 데이터를 받아야 하므로 'blob'으로 설정
        },
        success: function(data) {
            const blob = new Blob([data], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
            const link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.download = 'vendor_'+today_str+'.xlsx';
            link.click();
        },
        error: function(error) {
            alert('다운로드 실패')
            console.log(error);
        }
    });
}

// 명세서 전송 팝업
function open_specificationList() {
    let url = main_url + "/vendor/specification/send"
    open_win(url, "명세서전송", 1200, 800, 50, 50);
}

// 사업자 정보 폼 팝업
function vendorForm_popup(idx){
    let url = main_url + "/vendor/fla-business/view/" + idx
    open_win(url, "vendor_info"+cnt ,1300, 900, 0, 10);
    cnt++;
}

// 카드 총 금액 계산
function calc_cardAmount() {
    let year = $('#select_year').val()
    let month = $('#select_month').val()
    let brand = $('input[name="brand"]:checked').val()

    if(confirm("카드 금액을 변경하시겠습니까?")) {
        $.ajax({
            url : main_url + "/vendor/calculate/card",
            method : "POST",
            data: {
                'year' : year,
                'month': month,
                'brand': brand
            },
            success: function(data) {
                if(data.status){
                    alert("변경 완료");
                    location.reload();
                }else {
                    alert("변경 실패");
                }
            },
            error: function(e) {
                alert("변경 실패");
                console.log(e)
            }
        })
    }
}

function reCalculate_cardAmount(year, month, idx) {
    if(confirm("선택한 사업자의 카드총액을 다시 계산하시겠습니까?")) {
        $.ajax({
            url : main_url + "/vendor/calculate/card-individual",
            method: "POST",
            data: {
                year : year,
                month : month,
                idx : idx
            },
        }).then(data => {
            if(data) {
                alert("계산 완료");
                location.reload();
            }else {
                alert("계산 실패")
            }
        }).catch(e => {
            alert('계산 실패');
            console.log(e)
        })
    }
}
var number = 1;

$('#product_table').DataTable({
    paging: true,
    lengthMenu: [[20, 50 , 100, -1], [20, 50 , 100, "전체"]],
    pageLength: 20, // 기본 페이지 길이 설정,
    columnDefs: [
        { orderable: false, targets: [0, 1, 2 ] }, // 정렬을 비활성화할 열의 인덱스
        {
            targets: [5, 6], // 체크박스가 있는 열의 인덱스
            orderable: true, // 정렬 가능하도록 설정
            render: function (data, type, row, meta) {
                if (type === 'sort') {
                    // 정렬 시 data-order 값을 반환
                    var cellNode = meta.settings.aoData[meta.row].anCells[meta.col];
                    return $(cellNode).attr("data-order") || 0;
                }
                return data; // 테이블에 표시될 때는 원래 데이터 유지
            }
        },
    ],
    order: [[5, 'desc']],
});

// 상품 추가 팝업
function add_product() {
    const url = main_url + "/shop/product/0"
    open_win(url, 'Form', 1500, 850, 50, 50);
}

// 검색 카테고리 만들기
$('#category1').on('change', function(){

    $.ajax({
        url : main_url + "/api/Shop/Ctgy",
        method : 'GET',
        data : {
            'category1' : document.querySelector('#category1').value
        },
        success : function(data) {
            document.querySelector('#category2').innerHTML = data;
        },
        error : function(error) {
            alert('[에러발생] 개발팀에 문의하세요.');
            console.log(error);
        }
    })
})

// 상품 사진 팝업
function photo_popup(url) {
    open_win(url,'상품사진'+number,600,700,300,100);
    number++;
}

// 상품 상세 페이지 팝업
function product_form(idx) {
    url = main_url + "/shop/product/" + idx
    open_win(url,'상품정보'+number, 1500, 850, 50, 50);
    number++;
}

// 상품 삭제 ajax
function remove_product(id) {
    if(confirm("상품을 숨김처리하시겠습니까?")) {
        $.ajax({
            url: main_url + "/shop/product/"+ id,
            type: 'delete',
            success: function(data) {
                if(data) {
                    alert("삭제 완료");
                    location.reload();
                }
            },
            error: function(error) {
                alert('[에러발생]개발팀에 문의하세요.');
                console.log(error);
            }
        })
    }
}

// 상태 변경
$(document).on('change', '.product-change-state', function(){
    let id = $(this).data('index')
    let column = $(this).data('column');
    
    $.ajax({
        url: main_url + `/shop/product/state/${column}/${id}`,
        method: "patch",
        success: function(data) {
            if(data) {
                location.reload();
            }
        },
        error: function(error) {
            alert('[에러발생] 개발팀에 문의하세요.');
        }
    });
});




// input 태그 숫자 입력, 콤마
$('.number_format').on('keyup', function(){
    var target = $(this);
    let value = $(this).val();
    value = Number(value.replaceAll(',', ''));
    if(isNaN(value)) {         //NaN인지 판별
        target.val(0);
    }else {                   //NaN이 아닌 경우
        const formatValue = value.toLocaleString('ko-KR');
        target.val(formatValue);
    }
})

// 엑셀 파일 업로드
function send_file() {
    var file = $('#excel_file')[0].files[0];

    if(file) {
        if(confirm('파일을 업로드 하시겠습니까?')) {
            const handler = document.querySelector('input[name="handler"]').value

            var formData = new FormData();

            formData.append('files', file);
            formData.append('handler', handler);
            $.ajax({
                url: main_url + "/api/Shop/Product/Excel",
                method: "POST",
                data: formData,
                processData: false,  // false =>  formData를 string으로 변환하지 않음
                contentType: false,  // false =>  헤더가 multipart/form-data로 전송
                cache: false,
                success: function(data) {
                    alert(data);
                },
                error: function(e) {
                    alert('업로드 실패');
                    console.log(e)
                }
            })
        }
    } else {
        alert('파일을 선택해주세요.')
    }

}

// 엑셀 파일 업로드
function send_optionFile() {
    var file = $('#option_file')[0].files[0];

    if(file) {
        if(confirm('파일을 업로드 하시겠습니까?')) {
            const handler = document.querySelector('input[name="handler"]').value

            var formData = new FormData();

            formData.append('files', file);
            formData.append('handler', handler);
            $.ajax({
                url: main_url + "/Shop/Product/option/Excel",
                method: "POST",
                data: formData,
                processData: false,  // false =>  formData를 string으로 변환하지 않음
                contentType: false,  // false =>  헤더가 multipart/form-data로 전송
                cache: false,
                success: function(data) {
                    alert(data);
                },
                error: function(e) {
                    alert('업로드 실패');
                    console.log(e)
                }
            })
        }
    } else {
        alert('파일을 선택해주세요.')
    }
}

// 엑셀 파일 업로드
function send_etcFile() {
    var file = $('#etc_file')[0].files[0];

    if(file) {
        if(confirm('파일을 업로드 하시겠습니까?')) {
            const handler = document.querySelector('input[name="handler"]').value

            var formData = new FormData();

            formData.append('files', file);
            formData.append('handler', handler);
            $.ajax({
                url: main_url + "/Shop/Product/etc/Excel",
                method: "POST",
                data: formData,
                processData: false,  // false =>  formData를 string으로 변환하지 않음
                contentType: false,  // false =>  헤더가 multipart/form-data로 전송
                cache: false,
                success: function(data) {
                    alert(data);
                },
                error: function(e) {
                    alert('업로드 실패');
                    console.log(e)
                }
            })
        }
    } else {
        alert('파일을 선택해주세요.')
    }
}
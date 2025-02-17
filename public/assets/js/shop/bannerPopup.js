var number = 1;

$(document).ready(function(){
    get_vendors();
});

$('#brand').on('change', function(){
    get_vendors();
})


$('.datepicker').datepicker();

// 배너 전송
$("#banner_form").on("submit", function (e) {
    e.preventDefault();

    if(confirm("저장하시겠습니까?")) {
        var $this = $(this).parent();
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: $(this).prop("action"),
            data: new FormData(this),
            contentType: false,
            cache: false,
            processData: false,
            success: function (data) {
                if(data){
                    Swal.fire({
                        title: "등록 완료되었습니다.",
                        confirmButtonText: "확인",
                    }).then((result) => {
                        window.close();
                    });
                }
            }, error: function (error) {
                console.log(error);
            },
        });
    }
});

//팝업저장
$("#popup_form").on("submit", function (e) {
    e.preventDefault();

    if(confirm("저장하시겠습니까?")) {
        var $this = $(this).parent();
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: $(this).prop("action"),
            data: new FormData(this),
            dataType: "JSON",
            contentType: false,
            cache: false,
            processData: false,
            success: function (data) {
                if(data){
                    Swal.fire({
                        title: "등록 완료되었습니다.",
                        confirmButtonText: "확인",
                    }).then((result) => {
                        window.close();
                    });
                }
            }, error: function (error) {
                console.log(error);
            },
        });
    }
});


Dropzone.autoDiscover = false;  // deprecated 된 옵션. false로 해놓는걸 공식문서에서 명시

// 드롭다운
const dropzone1 = new Dropzone("div#dropzone_product_img", {
    url: main_url+'/api/Shop/fileUpload', // 파일을 업로드할 서버 주소 url.
    method: 'post', // 기본 post로 request 감. put으로도 할수있음
    // headers: {
    //     // 요청 보낼때 헤더 설정
    //     Authorization: 'Bearer ' + token, // jwt
    // },
    autoProcessQueue: true, // 자동으로 보내기. true : 파일 업로드 되자마자 서버로 요청, false : 서버에는 올라가지 않은 상태. 따로 this.processQueue() 호출시 전송
    // clickable: true, // 클릭 가능 여부
    autoQueue: true, // 드래그 드랍 후 바로 서버로 전송
    // createImageThumbnails: true, //파일 업로드 썸네일 생성

    // thumbnailHeight: 80, // Upload icon size
    // thumbnailWidth: 80, // Upload icon size

    maxFiles: 1, // 업로드 파일수
    maxFilesize: 100, // 최대업로드용량 : 100MB
    paramName: 'file', // 서버에서 사용할 formdata 이름 설정 (default는 file)
    parallelUploads: 1, // 동시파일업로드 수(이걸 지정한 수 만큼 여러파일을 한번에 넘긴다.)
    // uploadMultiple: false, // 다중업로드 기능
    timeout: 20000, //커넥션 타임아웃 설정 -> 데이터가 클 경우 꼭 넉넉히 설정해주자

    addRemoveLinks: true, // 업로드 후 파일 삭제버튼 표시 여부
    dictRemoveFile: '삭제', // 삭제버튼 표시 텍스트
    acceptedFiles: '.jpeg,.jpg,.png,.gif,.JPEG,.JPG,.PNG,.GIF', // 이미지 파일 포맷만 허용
    success: function(file, res) {
        console.log(file);
        console.log(res);
        document.querySelector('input[name="temp_img"]').value = res;
    }
});

function get_vendors(){
    const brand = $('#brand').val();

    $.ajax({
        url: main_url + "/shop/vendors/" + brand,
        method: "GET",
        success: function(data) {
            $('#domain').html(data);
            $('#domain').select2({
                templateResult: formatState,
                templateSelection: formatResult,
            });
        },
        error: function(e) {
            alert("도메인 정보 가져오기 실패")
        }
    })
}

// select2 옵션 css
function formatState(state){
    if (!state.id) {
        return state.text;
    }

    var $state = $(
        '<span class="vendor_mall">' + $(state.element).data('mall') + '</span>' +
        '<span class="vendor_name">' + $(state.element).data('name') + '</span>' +
        '<span class="vendor_domain">' + $(state.element).data('domain') + '</span>'
    );

    return $state;
}

// select2 선택 후 css
function formatResult(state){
    if (!state.id) {
        return state.text;
    }

    var $state = $(
        '<span class="vendor_mall">' + $(state.element).data('mall') + '</span>' +
        '<span class="vendor_name">' + $(state.element).data('name') + '</span>' +
        '<span class="vendor_domain">' + $(state.element).data('domain') + '</span>'
    );

    return $state;
}
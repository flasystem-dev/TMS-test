Dropzone.autoDiscover = false;  // deprecated 된 옵션. false로 해놓는걸 공식문서에서 명시

const dropzone1 = new Dropzone("div#dropzone_banner", {
    url: main_url+'/api/flaChain/Vendor/Upload', // 파일을 업로드할 서버 주소 url.
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

    maxFiles: 3, // 업로드 파일수
    maxFilesize: 100, // 최대업로드용량 : 100MB
    paramName: 'banner', // 서버에서 사용할 formdata 이름 설정 (default는 file)
    parallelUploads: 3, // 동시파일업로드 수(이걸 지정한 수 만큼 여러파일을 한번에 넘긴다.)
    // uploadMultiple: false, // 다중업로드 기능
    timeout: 300000, //커넥션 타임아웃 설정 -> 데이터가 클 경우 꼭 넉넉히 설정해주자

    addRemoveLinks: true, // 업로드 후 파일 삭제버튼 표시 여부
    dictRemoveFile: '삭제', // 삭제버튼 표시 텍스트
    acceptedFiles: '.jpeg,.jpg,.png,.gif,.JPEG,.JPG,.PNG,.GIF', // 이미지 파일 포맷만 허용

    init: function () {
        // 최초 dropzone 설정시 init을 통해 호출
        // console.log('최초 실행');
        let myDropzone = this; // closure 변수 (화살표 함수 쓰지않게 주의)

        // 서버에 제출 submit 버튼 이벤트 등록
        document.querySelector('#upload_banner').addEventListener('click', function () {
            console.log('업로드');

            // 거부된 파일이 있다면
            if (myDropzone.getRejectedFiles().length > 0) {
                let files = myDropzone.getRejectedFiles();
                console.log('거부된 파일이 있습니다.', files);
                return;
            }
            myDropzone.processQueue(); // autoProcessQueue: false로 해주었기 때문에, 메소드 api로 파일을 서버로 제출
        });

    },

});

const dropzone2 = new Dropzone("div#dropzone_popup", {
    url: main_url+'/api/flaChain/Vendor/Upload', // 파일을 업로드할 서버 주소 url.
    method: 'post', // 기본 post로 request 감. put으로도 할수있음
    // headers: {
    //     // 요청 보낼때 헤더 설정
    //     Authorization: 'Bearer ' + token, // jwt
    // },
    autoProcessQueue: false, // 자동으로 보내기. true : 파일 업로드 되자마자 서버로 요청, false : 서버에는 올라가지 않은 상태. 따로 this.processQueue() 호출시 전송
    // clickable: true, // 클릭 가능 여부
    autoQueue: false, // 드래그 드랍 후 바로 서버로 전송
    // createImageThumbnails: true, //파일 업로드 썸네일 생성

    // thumbnailHeight: 80, // Upload icon size
    // thumbnailWidth: 80, // Upload icon size

    maxFiles: 3, // 업로드 파일수
    maxFilesize: 100, // 최대업로드용량 : 100MB
    paramName: 'popup', // 서버에서 사용할 formdata 이름 설정 (default는 file)
    parallelUploads: 3, // 동시파일업로드 수(이걸 지정한 수 만큼 여러파일을 한번에 넘긴다.)
    // uploadMultiple: false, // 다중업로드 기능
    timeout: 300000, //커넥션 타임아웃 설정 -> 데이터가 클 경우 꼭 넉넉히 설정해주자

    addRemoveLinks: true, // 업로드 후 파일 삭제버튼 표시 여부
    dictRemoveFile: '삭제', // 삭제버튼 표시 텍스트
    acceptedFiles: '.jpeg,.jpg,.png,.gif,.JPEG,.JPG,.PNG,.GIF', // 이미지 파일 포맷만 허용

    init: function () {
        // 최초 dropzone 설정시 init을 통해 호출
        // console.log('최초 실행');
        let myDropzone = this; // closure 변수 (화살표 함수 쓰지않게 주의)

        // 서버에 제출 submit 버튼 이벤트 등록
        document.querySelector('#upload_banner').addEventListener('click', function () {
            console.log('업로드');

            // 거부된 파일이 있다면
            if (myDropzone.getRejectedFiles().length > 0) {
                let files = myDropzone.getRejectedFiles();
                console.log('거부된 파일이 있습니다.', files);
                return;
            }
            myDropzone.processQueue(); // autoProcessQueue: false로 해주었기 때문에, 메소드 api로 파일을 서버로 제출
        });

    },

});
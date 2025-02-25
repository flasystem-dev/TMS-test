const main_url = window.location.protocol + "//" + window.location.host;

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

function brand_session(){
    var selectedBrands = [];
    $("input[name='session_brand']:checked").each(function() {
        selectedBrands.push($(this).val()); // 체크된 요소의 값을 배열에 추가
    });

    $.ajax({
        url: main_url+'/session/brand-session',
        type: "POST",
        data: {
            'brands':selectedBrands
        },
        success: function(data) {
            location.reload();
        }
    });
}

function auto_hyphen(e) {
    let ele = e.target;
    let val = ele.value.replace(/[^0-9]/g, ''); // 숫자만 남김

    let result = '';

    if (val.startsWith('02')) {
        // 서울 지역번호 (02)
        if (val.length <= 2) {
            result = val;
        } else if (val.length <= 6) {
            result = val.substring(0, 2) + '-' + val.substring(2);
        } else {
            result = val.substring(0, 2) + '-' + val.substring(2, 6) + '-' + val.substring(6);
        }
    } else if (val.startsWith('0505')) {
        // 전국 대표번호 (0505)
        if (val.length <= 4) {
            result = val;
        } else if (val.length <= 8) {
            result = val.substring(0, 4) + '-' + val.substring(4);
        } else {
            result = val.substring(0, 4) + '-' + val.substring(4, 8) + '-' + val.substring(8);
        }
    } else if (val.startsWith('010')) {
        // 휴대폰 번호 (010)
        if (val.length <= 3) {
            result = val;
        } else if (val.length <= 7) {
            result = val.substring(0, 3) + '-' + val.substring(3);
        } else {
            result = val.substring(0, 3) + '-' + val.substring(3, 7) + '-' + val.substring(7);
        }
    } else if (val.length >= 9) {
        // 일반 지역번호 (031, 051 등)
        if (val.length <= 3) {
            result = val;
        } else if (val.length <= 6) {
            result = val.substring(0, 3) + '-' + val.substring(3);
        } else {
            result = val.substring(0, 3) + '-' + val.substring(3, 6) + '-' + val.substring(6);
        }
    } else {
        result = val;
    }

    ele.value = result;
}




function open_win(url, name, popupWidth, popupHeight, Left, Top) {
    // name   : 팝업윈도우_이름
    // width  : 팝업창 가로 크기
    // height : 팝업창 세로 크기

    // window.screenX : 현재 스크린에서 브라우저 좌측상단 X좌표
    // window.screenY : 현재 스크린에서 브라우저 좌측상단 Y좌표
    var winPosX = window.screenX;
    var winPosY = window.screenY;

    // window.screen.width  : 윈도우의 가로 크기
    // window.screen.height : 윈도우의 세로 크기
    var screenWidth = window.screen.width;
    var screenHeight = window.screen.height;

    var popupX;
    var popupY;
    if (winPosX >= 0) {
        if (winPosX < screenWidth) // 1번(좌) : 2번(우) 순서에서 1번 스크린이면
            popupX = Left;

        else                       // 1번(좌) : 2번(우) 순서에서 2번 스크린이면
            popupX = Math.round(screenWidth + Left);

        popupY = Top;

    } else {
        // 2번(좌) : 1번(우) 순서에서 2번 스크린이면
        popupX = -Math.round(screenWidth + Left);

        if (winPosY > 0) {
            if (winPosY < screenHeight) // 1번(상) : 2번(하) 순서에서 위쪽 스크린이면
                popupY = Math.round((screenHeight / 2) - (popupHeight / 2));
            else                        // 1번(상) : 2번(하) 순서에서 아래쪽 스크린이면
                popupY = Math.round(screenHeight + (screenHeight / 2) - (popupHeight / 2));
        } else {
            // 2번(상) : 1번(하) 순서에서 위쪽 스크린이면
            popupY = -Math.round((screenHeight / 2) + (popupHeight / 2));
        }

    }
    // 윈도우 팝업창의 스타일 지정
    var featureWindow = "width=" + popupWidth + ", height=" + popupHeight
        + ", left=" + popupX + ", top=" + popupY + ",noopener=false";

    return window.open(url, name, featureWindow);
}

// Ajax 로딩중 이미지
$.ajaxSetup({
    beforeSend: function () {
        var width = 0;
        var height = 0;
        var left = 0;
        var top = 0;

        width = 50;
        height = 50;
        top = ( $(window).height() - height ) / 2 + $(window).scrollTop();
        left = ( $(window).width() - width ) / 2 + $(window).scrollLeft();

        if($("#div_ajax_load_image").length != 0) {
            $("#div_ajax_load_image").css({
                "top": top+"px",
                "left": left+"px"
            });
            $("#div_ajax_load_image").show();
        }else {
            $('body').append('<div id="div_ajax_load_image" style="position:fixed; top:50%; left:50%; width:' + width + 'px; height:' + height + 'px; z-index:9999; filter:alpha(opacity=50); opacity:alpha*0.5; margin:auto; padding:0; "><img src="https://flasystem.flabiz.kr/assets/images/loading.gif" style="width:100px; height:100px;"></div>');
        }
    },
    complete: function () {
        $("#div_ajax_load_image").hide();
    }
})

// 숫자 3자리 콤마 + 한글 단위
function getCommaKoreanNumber(number) {
    const koreanUnits = ['조', '억', '만', ''];
    const unit = 10000;
    let answer = '';

    while (number > 0) {
        const mod = number % unit;
        const modToString = mod.toString().replace(/(\d)(\d{3})/, '$1,$2');
        number = Math.floor(number / unit);
        answer = `${modToString}${koreanUnits.pop()}${answer}`;
    }
    return answer;
}

// 숫자 한글형식으로 변환
function getKoreanNumber(number) {
    const koreanNumber = ['', '일', '이', '삼', '사', '오', '육', '칠', '팔', '구'];
    const tenUnit = ['', '십', '백', '천'];
    const tenThousandUnit = ['조', '억', '만', ''];
    const unit = 10000;

    let answer = '';

    while (number > 0) {
        const mod = number % unit;
        const modToArray = mod.toString().split('');
        const length = modToArray.length - 1;

        const modToKorean = modToArray.reduce((acc, value, index) => {
            const valueToNumber = +value;
            if (!valueToNumber) return acc;
            // 단위가 십 이상인 '일'글자는 출력하지 않는다. ex) 일십 -> 십
            const numberToKorean = index < length && valueToNumber === 1 ? '' : koreanNumber[valueToNumber];
            return `${acc}${numberToKorean}${tenUnit[length - index]}`;
        }, '');

        answer = `${modToKorean}${tenThousandUnit.pop()} ${answer}`;
        number = Math.floor(number / unit);
    }

    return answer.replace();
}

$.datepicker.setDefaults({
    dateFormat: 'yy-mm-dd',
    prevText: '이전 달',
    nextText: '다음 달',
    monthNames: ['1월', '2월', '3월', '4월', '5월', '6월', '7월', '8월', '9월', '10월', '11월', '12월'],
    monthNamesShort: ['1월', '2월', '3월', '4월', '5월', '6월', '7월', '8월', '9월', '10월', '11월', '12월'],
    dayNames: ['일', '월', '화', '수', '목', '금', '토'],
    dayNamesShort: ['일', '월', '화', '수', '목', '금', '토'],
    dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
    showMonthAfterYear: true,
    // yearSuffix: '년'
});

function clipBoardCopy(e) {
    const text = e.target.innerText;

    // 클립보드에 텍스트 복사
    navigator.clipboard.writeText(text).then(function() {
        showToast("복사 완료!");
    }).catch(function(err) {
        // showToast(err)
    });
}

function showToast(message) {
    Swal.fire({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 2300,
        timerProgressBar: true,
        icon: 'success',  // success, error, warning, info, question 중 선택
        title: message,
        customClass: {
            popup: 'sweetAlert2-custom-position'
        },
        background : '#d4edda',
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });
}

function showAlert(type,text) {
    Swal.fire({
        icon: type,  // success, error, warning, info, question 중 선택
        title: text,
    });
}

function receipt_popup(url) {
    if(url=="") {
        Swal.fire({
            icon: 'error',
            // title: '',
            text: '연결 된 영수증이 없습니다.',
        });
        return;
    }
    open_win(url, "증빙", 600,600,50,50);
}
$('#contents').on('input', function(){
    const textarea = document.getElementById('contents');
    var text = textarea.value;
    var byte = calculateByteLength(text);

    document.getElementById('content_byte').innerText = byte;

    if(byte > 2000) {
        alert('최대 2000 바이트까지만 보낼 수 있습니다.')
    }
});

function calculateByteLength(text) {
    let byteLength = 0;
    for (let i = 0; i < text.length; i++) {
        const charCode = text.charCodeAt(i);
        if (charCode <= 0x7F) {
            // ASCII 문자 (1 byte)
            byteLength += 1;
        } else if (charCode <= 0x7FF) {
            // 2바이트 문자
            byteLength += 2;
        } else if (charCode <= 0xFFFF) {
            // 3바이트 문자
            byteLength += 3;
        } else {
            // 4바이트 문자 (유니코드 서러게이트 페어)
            byteLength += 4;
        }
    }
    return byteLength;
}

function send_SMS() {
    var form = document.getElementById('sms_form');
    const formData = new FormData(form);

    $.ajax({
        url : main_url + "/SMS/custom/send",
        method : "POST",
        data : formData,
        async: false,
        processData: false,
        contentType: false,
        success: function(data) {
            if(data.status){
                alert('전송 완료');
                window.close();
            }else{
                alert(data.message);
            }
        },
        error: function(e) {
            alert('전송 실패');
            console.log(e)
        }
    })
}

// 발주처 select2
$('#sender').select2({
    placeholder: "번호 이름",
    templateResult: formatState,
    templateSelection: formatResult,
});

// 발주처 select 옵션 css 변경
function formatState(state){
    if (!state.id) {
        return state.text;
    }

    var $state = $(
        '<span class="sender">' + $(state.element).data('sender') + '</span>' +
        '<span class="ini">' + $(state.element).data('ini') + '</span>'
    );

    return $state;
}

// 발주처 select 선택 후 css
function formatResult(state){
    if (!state.id) {
        return state.text;
    }

    var $state = $(
        '<span class="sender">' + $(state.element).data('sender') + '</span>' +
        '<span class="ini">' + $(state.element).data('ini') + '</span>'
    );

    return $state;
}

// 자주쓰는 메시지 선택
function select_msg(e) {
    var text = e.target.innerText;

    document.getElementById('contents').value = text;
}

// 브랜드 선택
$('#memo_brand').on('change', function(){
    var brand = $(this).val();

    $.ajax({
        url: main_url + "/SMS/memo/form/note",
        method: "GET",
        data: { 'brand': brand },
        success:function(data) {
            $('#msg_list').html(data);
        }
    })
});
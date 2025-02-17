$('#plusFriendID').on('change', function(){
    if(document.getElementById('plusFriendID').value == ''){
        document.getElementById('templateName_area').classList.add('d-none');
    } else {
        $('#templateName_area').removeClass('d-none');
        $.ajax({
            url: main_url + '/KakaoTalk/Page/GetTemplateNameList',
            type: 'GET',
            data: {
                'plusFriendID' : document.getElementById('plusFriendID').value
            },
            success: function(data) {
                $('#templateName').html(data.option);
            },
            error: function(error) {
                alert('[에러 발생] 개발팀에 문의하세요');
                console.log(error);
            }
        })
    }
})

$('#find_template').on('click', function(){
    $.ajax({
        url: main_url + '/KakaoTalk/Page/GetTemplate',
        type: 'GET',
        data: {
            'plusFriendID' : document.getElementById('plusFriendID').value,
            'templateName' : document.getElementById('templateName').value
        },
        success: function(data){
            $('#template_area').html(data);
        },
        error: function(error) {
            alert('[에러 발생] 개발팀에 문의하세요')
            console.log(error)
        }
    })
})

// 사용중 인 템플릿
function used_template() {
    $.ajax({
        url: main_url + '/KakaoTalk/Page/GetUsedTemplate',
        type: 'GET',
        data: {
            'brand_type_code' : document.getElementById('used_template_brand').value,
            'template_type' : document.getElementById('used_template_type').value
        },
        success: function(data){
            if(data.status===0) {
                alert(data.msg)
            }else {
                $('#template_area').html(data);
            }
        },
        error: function(error) {
            alert('[에러 발생] 개발팀에 문의하세요')
            console.log(error)
        }
    })
}

// 템플릿 설정
$('input[name="menu_radio"]').on('change', function(){
    const radio = $('input[name="menu_radio"]:checked').val();

    let area_list = document.querySelectorAll('.search_template_menu')

    area_list.forEach(function(area){
        if(area.classList.contains(radio)) {
            area.classList.remove('d-none');
        }else {
            area.classList.add('d-none');
        }
    })
})

// 채널 관리
$('#manage_channel').on('click', function(){
    $.ajax({
        url: main_url + "/KakaoTalk/GetPlusFriendMgtURL",
        type: 'GET',
        data: {
            'brand' : document.querySelector('select[name="brand_code1"]').value
        },
        success: function(data) {
            if(data.code == 'F'){
                alert(data.message);
            }else {
                open_win(data.url,"채널 관리",900,900,0,0);
            }
        },
        error: function(error) {
            alert('[에러 발생] 개발팀에 문의하세요')
            console.log(error)
        }
    })
})


// 템플릿 관리
$('#manage_template').on('click', function(){
    $.ajax({
        url: main_url + "/KakaoTalk/GetATSTemplateMgtURL",
        type: 'GET',
        data: {
            'brand' : document.querySelector('select[name="brand_code2"]').value
        },
        success: function(data) {
            if(data.code == 'F'){
                alert(data.message);
            }else {
                open_win(data.url,"템플릿 관리",900,900,0,0);
            }
        },
        error: function(error) {
            alert('[에러 발생] 개발팀에 문의하세요')
            console.log(error)
        }
    })
})

// 테이블 컬럼명, 코멘트 가져오기
$(document).on('change', '.table_name',function(){
    let index = $(this).data('index')
    $.ajax({
        url: main_url + "/KakaoTalk/Page/getColumnName",
        type: 'GET',
        data: {
            'table_name' : document.getElementById('table_name'+index).value,
            'templateCode' : document.querySelector('input[name="templateCode"]').value
        },
        success: function(data) {
            let select = $('#column_name'+index);
            // select.empty();
            select.html(data);

            // data.forEach(function(column) {
            //     let option = $('<option>', { value : column.name});
            //     option.append($('<span>', { class: 'column_comment', text: column.comment}));
            //     // option.append($('<span>', { class: 'column_name', text: column.name}));
            //     select.append(option);
            // })
        },
        error: function(error) {
            alert('[에러 발생] 개발팀에 문의하세요')
            console.log(error)
        }
    })
})

// 변수 수정 버튼
function edit_values() {
    const formData = new FormData(document.getElementById('variable_tbl'));


    if(confirm('수정하시겠습니까?')){
        $.ajax({
            url: main_url+'/KakaoTalk/Page/SetValues',
            type: 'POST',
            data : formData,
            async: false,
            processData: false,
            contentType: false,
            success: function(data) {
                if(data) {
                    alert("설정 완료");
                    $('#dev_variable_close_btn').click();
                }else {
                    alert("설정 실패")
                }
            },
            error: function(error){
                alert('[에러 발생] 개발팀에 문의하세요')
                console.log(error)
            }
        })
    }
};
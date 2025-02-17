var number = 1;

$(document).ready(function(){
    $('.brand_type_check:checkbox').change(function () {
       document.brand_select.submit();
    });
});

// 배너 추가 팝업
function bannerForm() {
    const url = main_url + "/shop/bannerForm"
    open_win(url, 'Form', 1300, 660, 100, 100);
}
function popupForm() {
    const url = main_url + "/shop/popupForm"
    open_win(url, 'Form', 1300, 750, 100, 100);
}


function banner_use(id) {
    $.ajax({
        url: main_url + "/api/Shop/bannerUse/"+ id,
        success: function(data) {
            if(data=='success'){
                alert('상태가 변경되었습니다.');
            }else{
                alert('실패 - 개발팀에 문의하세요.');
            }
        },
        error: function(error) {
            alert('[에러발생]개발팀에 문의하세요.');
            console.log(error);
        }
    });
}

function popup_use(id) {
    $.ajax({
        url: main_url + "/api/Shop/popupUse/"+ id,
        success: function(data) {
            if(data=='success'){
                alert('상태가 변경되었습니다.');
            }else{
                alert('실패 - 개발팀에 문의하세요.');
            }
        },
        error: function(error) {
            alert('[에러발생]개발팀에 문의하세요.');
            console.log(error);
        }
    });
}


// 팝업, 배너 우선순위 변경
$('.orderBy_select').on('change', function(){
    const index = $(this).data('index')
    const orderBy = $(this).val()
    const type = $(this).data('type')

    $.ajax({
        url: main_url + "/shop/" + type + "/orderBy",
        method: "PATCH",
        data: {
            'id' : index,
            'orderBy': orderBy
        },
        success: function(data) {
            if(data){
                location.reload();
            }
        },
        error:function(e){
            alert("문제 발생");
            console.log(e)
        }
    })
});

// 팝업, 배너 삭제
$('.delete_btn').on('click', function(){
    if(confirm("정말 삭제하시겠습니까?")) {
        const index = $(this).data('index')
        const type = $(this).data('type')

        $.ajax({
            url: main_url + "/shop/" + type + "/" + index,
            method: "DELETE",
            success: function(data) {
                if(data){
                    location.reload();
                }
            },
            error:function(e){
                alert("문제 발생");
                console.log(e)
            }
        })
    }
});

$('.check_used').on('change', function(){
    const index = $(this).data('index')
    const type = $(this).data('type')
    var checked = 'N';
    if($(this).prop('checked')){
        checked = 'Y';
    }

    $.ajax({
        url: main_url + "/shop/" + type + "/use",
        method: "PATCH",
        data: {
            'id' : index,
            'check': checked
        },
        success: function(data) {
            if(data){
                location.reload();
            }
        },
        error:function(e){
            alert("문제 발생");
            console.log(e)
        }
    })
})
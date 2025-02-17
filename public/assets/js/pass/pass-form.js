$('.datepicker').datepicker();

$("#pass_form").on("submit", function (e) {
    e.preventDefault();

    if(confirm("저장하시겠습니까?")) {
        var $this = $(this).parent();
        $.ajax({
            type: "POST",
            url: $(this).prop("action"),
            data: new FormData(this),
            contentType: false,
            cache: false,
            processData: false,
            success: function (data) {
                if(data.status){
                    location.href = data.url;
                }else {
                    alert("수정 실패");
                }
            }, error: function (error) {
                alert("문제 발생");
                console.log(error);
            },
        });
    }
});

// 도메인 중복 체크
function check_dup_domain() {
    const domain = document.getElementById('domain').value;
    $.ajax({
        url : main_url + "/pass/check/domain",
        method : "GET",
        data: { 'domain': domain },
        success: function(data) {
            if (data) {
                alert('중복 된 도메인입니다.')
                $('#domain').removeClass('check_ok');
                $('#domain').addClass('check_dup');
            } else {
                alert('사용 가능합니다.')
                $('#domain').removeClass('check_dup');
                $('#domain').addClass('check_ok');
            }
        },
        error: function(e) {
            console.log(e);
        }
    });
}
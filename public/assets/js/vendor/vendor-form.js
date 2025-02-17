$(document).ready(function(){
    $("#company_name").hide();
    $("#business_number").hide();
    $("#business_file").hide();
    $("#assurance_ex_date").hide();
    $("#assurance_value").hide();
    $("#deposit_form").hide();

    // 벤더 대표자 타입 확인
    vendor_rep_type();
    
    // 추천인 정보 가져오기
    recommandPersonList();
});

$('.datepicker').datepicker();

// 아이디 중복 확인
function check_id(){
    let vendor_id = $("#vendor_id").val();
    if(vendor_id==="") {
        alert("아이디를 입력해주세요.")
        return false;
    }
    var type = 'vendor';
    $.ajax({
        url: main_url + "/vendor/check_id_dup",
        type: "GET",
        data: {
            "id" : vendor_id,
            "type" : type
        },
        success: function(response) {
            if(response === "Y") {
                alert("사용가능한 ID 입니다.");
                $("#id_checked").val("Y");
                $("#vendor_id").attr("readonly",true);
                $("#vendor_id").css("background-color",'#eee');
            } else {
                alert("이미 등록된 ID 입니다.");
                $("#id_checked").val("N");
            }
        },
        error: function (error){
            console.log(error);
            alert('에러발생! 개발팀에 문의하세요.');
        }
    });
}

// 도메인 중족 확인
function check_domain(){
    let domain = $("#domain").val();
    var type = 'c';
    $.ajax({
        url: main_url + "/vendor/check_domain_dup",
        type: "GET",
        data: {
            "domain" : domain
        },
        success: function(response) {
            if(response === "Y") {
                alert("사용가능한 도메인 입니다.");
                $("#domain_checked").val("Y");
                $("#domain").attr("readonly",true);
                $("#domain").css("background-color",'#eee');
            } else {
                alert("이미 등록된 도메인 입니다.");
                $("#domain_checked").val("N");
            }
        },
        error: function (error){
            console.log(error);
            alert('에러발생! 개발팀에 문의하세요.');
        }
    });
}

// 보증 종류 변경
function change_assurance(type){
    if(type==='ARNR'||type==='ARPS'){
        $("#assurance_ex_date").hide();
        $("#assurance_value").hide();
        $("#deposit_form").hide();
    }else if(type==='ARIR'){
        $("#assurance_ex_date").show();
        $("#assurance_value").show();
        $("#deposit_form").hide();
    }else if(type==='ARDS'){
        $("#assurance_ex_date").hide();
        $("#assurance_value").hide();
        $("#deposit_form").show();
    }
}

// 브랜드 타입 변경
$('input[name="brand_type"]').on('change', function(){
    var type = $('input[name="brand_type"]:checked').val();
    if(type==='BTCS'||type==='BTFCC'){
        info_view('');
    }else if(type==='BTFCB'){
        info_view('company');
    }
    // 추천인 정보 가져오기
    recommandPersonList();
})

// 벤터 타입 변경
$('input[name="rep_type"]').on('change', function(){
    vendor_rep_type();
})

// 벤더 타입에 따른 view
function vendor_rep_type() {
    const rep_type = $('input[name="rep_type"]:checked').val();
    if(rep_type==="사업자") {
        info_view("company");
    }else {
        info_view("");
    }
}

// 개인, 사업자 정보 표시
function info_view(type) {
    if(type==="company") {
        $("#company_name").show();
        $("#business_number").show();
        $("#business_file").show();
        $("#rr_number1").hide();
        $("#rr_number2").hide();
        $("#iden_name").hide();
        $("#iden_file").hide();
    }else {
        $("#company_name").hide();
        $("#business_number").hide();
        $("#business_file").hide();
        $("#rr_number1").show();
        $("#rr_number2").show();
        $("#iden_name").show();
        $("#iden_file").show();
    }
}

// 주민 뒷자리 확인
function check_rrn() {
    Swal.fire({
        title: "확인 비밀번호",
        input: "password",
        inputAttributes: {
            autocapitalize: "off",
            autocomplete: "new-password"
        },
        showCancelButton: true,
        inputPlaceholder: "주민번호 확인용 비밀번호를 입력해주세요.",
        confirmButtonText: "확인",
        showLoaderOnConfirm: true,
        preConfirm: (password) => {
            return $.ajax({
                url: main_url + "/api/vendor/info/check",
                type: "POST",
                data: { password: password, idx : $('#vendor_idx').val() },
            }).then(response => {
                if (!response.success) {
                    // 응답이 성공적이지 않은 경우 오류 메시지 표시
                    Swal.showValidationMessage(`확인 실패: ${response.message}`);
                }
                $('#rr_number2').val(response.rr_number);
                return response;
            }).catch(error => {
                // AJAX 요청 실패 시 오류 메시지 표시
                Swal.showValidationMessage(`요청 실패: ${error.statusText || "Unknown Error"}`);
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: "인증 성공",
                text: "비밀번호 확인이 완료되었습니다.",
                icon: "success"
            });

        }
    });
}

// 주민등록증, 사업자등록증 , 통장 사본 확인
function check_pw_info(url) {
    Swal.fire({
        title: "확인 비밀번호",
        input: "password",
        inputAttributes: {
            autocapitalize: "off",
            autocomplete: "new-password"
        },
        showCancelButton: true,
        inputPlaceholder: "정보 확인용 비밀번호를 입력해주세요.",
        confirmButtonText: "확인",
        showLoaderOnConfirm: true,
        preConfirm: (password) => {
            return $.ajax({
                url: main_url + "/api/vendor/file/check",
                type: "POST",
                data: { password: password, idx : $('#vendor_idx').val() },
            }).then(response => {
                if (!response.success) {
                    // 응답이 성공적이지 않은 경우 오류 메시지 표시
                    Swal.showValidationMessage(`확인 실패: ${response.message}`);
                }
                return response;
            }).catch(error => {
                // AJAX 요청 실패 시 오류 메시지 표시
                Swal.showValidationMessage(`요청 실패: ${error.statusText || "Unknown Error"}`);
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: "인증 성공",
                text: "비밀번호 확인이 완료되었습니다.",
                icon: "success"
            });

            location.href = url;
        }
    });
}

// 추천인 브랜드 조회 후 가져오기
function recommandPersonList() {
    const vendor_brand_type = $('input[name="brand_type"]:checked').val();
    const recommendPerson = $('#recommend_person').data('recommend');

    $.ajax({
        url: main_url + "/vendor/form/recommend",
        method : "GET",
        data : {
            brand : vendor_brand_type
        }
    }).then(data => {
        $('#recommend_person').html(data);
        $('#recommend_person').select2({
            placeholder: "상점명, 대표자명, 파트너명",
            templateResult: formatState,
            templateSelection: formatResult,
        })

        if(recommendPerson){
            $('#recommend_person').val(recommendPerson).trigger('change');
        }

    }).catch(e => {
        alert('추천인 조회 실패')
        console.log(e)
    })
}

// select2 옵션 css 변경
function formatState(state){
    if (!state.id) {
        return state.text;
    }

    var $state = $(
        '<span class="shop_name">' + $(state.element).data('mall') + '</span>' +
        '<span class="rep_name">' + $(state.element).data('name') + '</span>' +
        '<span class="partner_name">' + $(state.element).data('partner') + '</span>'
    );

    return $state;
}

// select2 선택된 옵션 css 변경
function formatResult(state){
    if (!state.id) {
        return state.text;
    }

    var $state = $(
        '<span class="shop_name">' + $(state.element).data('mall') + '</span>' +
        '<span class="rep_name">' + $(state.element).data('name') + '</span>' +
        '<span class="partner_name">' + $(state.element).data('partner') + '</span>'
    );

    return $state;
}
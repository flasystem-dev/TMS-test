var cnt_num = 0;

const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]')
const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl))

// 모든 계정 정보 업데이트
function check_account_info() {
    $.ajax({
        url: main_url + '/api/PlayAuto/Account/Check',
        type: "GET",
        success: function(data) {
        alert("업데이트 완료");
        location.reload();
    },
    error: function(e){
        alert("[에러] 개발팀에 문의하세요.");
        console.log(e);
    }
});
}

// 계정 정보 수정
function update_account_info(e, name) {
    let element = e.target;
    const mall = $(element).data('mall');
    const brand = $(element).data('brand');
    const handler = name
    const id = $(element).parent().prev().prev().prev().children().val();
    const pw = $(element).parent().prev().prev().children().val();
    const nick = $(element).parent().prev().children().val();

    if(confirm("수정하시겠습니까 ? ")) {
        $.ajax({
            url: main_url + '/api/PlayAuto/Account/Info',
            type: 'POST',
            data: {
            'brand' : brand,
                'mall' : mall,
                'id' : id,
                'pw' : pw,
                'nick' : nick,
                'handler' : name
        },
        success:function (data) {
            alert(data);
            location.reload();
        },error(e) {
            alert("[에러 발생] 개발팀에 문의하세요.");
            console.log(e);
        }
    });
    }
}

// 채널 관리자 URL 팝업
function open_admin_url(url){
    open_win(url,"관리자"+cnt_num,1440,712,200,200);
    cnt_num++;
}

// 관리자 메모 모달 정보 전달
const adminModal = document.getElementById('admin_memo_modal');
if (adminModal) {
    adminModal.addEventListener('show.bs.modal', event => {

        const memo_button = event.relatedTarget
        const mall = memo_button.getAttribute('data-bs-mall')
        const brand = memo_button.getAttribute('data-bs-brand')
        const memo = memo_button.getAttribute('data-bs-memo')

        const input_mall = adminModal.querySelector('input[name="mall_code"]');
        const input_brand = adminModal.querySelector('input[name="brand_type_code"]');
        const textarea_memo = adminModal.querySelector('#admin_memo_content');

        input_mall.value = mall
        input_brand.value = brand
        textarea_memo.innerHTML = memo;
    })
}

// 관리자 메모 등록
function send_admin_memo() {
    if(confirm('메모를 등록하시겠습니까?')){
        $.ajax({
            url: main_url + '/api/PlayAuto/Account/Memo',
            method: 'POST',
            data: {
                'mall_code' : document.querySelector('input[name="mall_code"]').value,
                'brand_type_code' : document.querySelector('input[name="brand_type_code"]').value,
                'memo' : document.querySelector('textarea[name="admin_memo_content"]').value
            },
            success: function(data) {
                alert(data);
                location.reload();
            },
            error: function(e) {
                alert('[에러발생]개발팀에 문의하세요.');
                console.log(e)
            }
        })
    }
}

// 관리자 정보 모달 정보 전달
const infoModal = document.getElementById('show_pw_modal');
if (infoModal) {
    infoModal.addEventListener('show.bs.modal', event => {

        const button = event.relatedTarget
        const idx = button.getAttribute('data-list')

        const input_idx = infoModal.querySelector('input[name="list_idx"]');

        input_idx.value = idx
    })
}

// 비밀번호 보기 버튼
function check_info() {
    const idx_id = document.querySelector('input[name="list_idx"]').value

    $.ajax({
        url: main_url + '/api/Shop/Account/Check',
        method: 'POST',
        data: {
            'account_info' : document.querySelector('input[name="check_pw_input"]').value,
        },
        success: function(data) {
            if(data) {
                alert('확인 완료');
                show_pw_modal_close_btn.click();
                document.getElementById(idx_id).type = 'text';
            }else {
                alert('비밀번호를 다시 확인해주세요.')
            }
        },
        error: function(e) {
            alert('[에러발생] 개발팀에 문의하세요.');
            console.log(e)
        }
    })
}
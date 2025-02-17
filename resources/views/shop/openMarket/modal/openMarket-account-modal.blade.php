<!-- 브랜드 메모 등록 -->
<div class="modal fade" id="admin_memo_modal" tabindex="-1" aria-labelledby="account_memo_modal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">관리자 메모 등록</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="brand_type_code" aria-label="brand_type_code">
                <input type="hidden" name="mall_code" aria-label="mall_code">
                <textarea class="form-control" id="admin_memo_content" name="admin_memo_content" aria-label="memo_text" style="min-height: 200px;"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">닫기</button>
                <button type="button" class="btn btn-primary" onclick="send_admin_memo();">메모 등록</button>
            </div>
        </div>
    </div>
</div>
<!-- 브랜드 메모 등록 끝 -->

<!-- 비밀번호 보기 -->
<div class="modal fade" id="show_pw_modal" tabindex="-1" aria-labelledby="show_pw_modal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">관리자 비밀번호를 입력하세요.</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
{{--                <input type="hidden" name="brand_code" aria-label="brand_code">--}}
{{--                <input type="hidden" name="channel_code" aria-label="channel_code">--}}
                <input type="hidden" name="list_idx" aria-label="list_idx">
                <input class="form-control" name="check_pw_input" aria-label="pw_input">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="show_pw_modal_close_btn">닫기</button>
                <button type="button" class="btn btn-primary" onclick="check_info();">확인
                </button>
            </div>
        </div>
    </div>
</div>
<!-- 비밀번호 보기 끝 -->
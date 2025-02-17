<!-- 메모 등록/수정 모달 -->
<div class="modal" id="msg_form" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">자주쓰는 메모</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div>
                    <form id="upsert_form">
                        <select class="form-select" name="modal_brand" id="modal_brand" style="width: 50%; height: 30px; padding: 5px 15px; line-height: 16px; margin-bottom: 10px;">
                            <option value="BTCP">꽃파는총각</option>
                            <option value="BTCC">칙칙폭폭플라워</option>
                            <option value="BTSP">사팔플라워</option>
                            <option value="BTBR">바로플라워</option>
                            <option value="BTOM">오만플라워</option>
                            <option value="BTCS">꽃파는사람들</option>
                            <option value="BTFC">플라체인</option>
                        </select>
                        <textarea class="form-control" name="modal_msg" id="modal_msg" style="min-height: 300px"></textarea>
                        <input type="hidden" name="modal_id" id="modal_id" value="0">
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">닫기</button>
                <button type="button" class="btn btn-primary" onclick="upsert_msg()">저장</button>
            </div>
        </div>
    </div>
</div>
<!-- 메모 등록/수정 모달 끝 -->
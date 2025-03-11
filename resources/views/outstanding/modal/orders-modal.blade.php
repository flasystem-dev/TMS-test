<!-- 일괄 입금 모달 -->
<div class="modal fade" id="deposit_completed" tabindex="-1" aria-labelledby="deposit_completed" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="cancel_refuseLabel">선택 된 주문 일괄 처리</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="input-group mb-3">
                    <span class="input-group-text">입금자명</span>
                    <input type="text" class="form-control" name="deposit_name" aria-label="deposit_name">
                </div>
                <div class="input-group mb-3">
                    <span class="input-group-text">결제상태</span>
                    <select class="form-select" name="deposit_payment_state" id="deposit_payment_state" aria-label="deposit_payment_state">
                        <option value="PSUD">결제대기</option>
                        <option value="PSDN">결제완료</option>
                        <option value="PSCC">취소완료</option>
                    </select>
                </div>
                <div class="input-group mb-3">
                    <span class="input-group-text">결제일</span>
                    <input type="datetime-local" class="form-control" name="payment_payment_time" id="payment_payment_time" value="">
                </div>
                <div class="input-group mb-3">
                    <span class="input-group-text">결제메모</span>
                    <textarea class="form-control" name="payment_payment_memo" id="payment_payment_memo"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">취소</button>
                <button type="button" class="btn btn-primary" onclick="deposit_completed()">일괄 처리</button>
            </div>
        </div>
    </div>
</div>
<!-- 일괄 입금 모달 끝-->
<!-- 취소 요청 거절 모달 -->
<div class="modal fade" id="cancel_refuse" tabindex="-1" aria-labelledby="cancel_refuseLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="cancel_refuseLabel">거절 사유[관리자 메모]</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <textarea class="form-control" name="cancel_refuse_memo"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">취소</button>
                <button type="button" class="btn btn-primary" onclick="cancel_refuse('{{Str::ucfirst(Auth::user()->name)}}');">메모 등록</button>
            </div>
        </div>
    </div>
</div>
<!-- 취소 요청 거절 모달 끝 -->

<!-- 취소 완료 모달 -->
<div class="modal fade" id="cancel_complete" tabindex="-1" aria-labelledby="cancel_complete" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="cancel_refuseLabel">취소 완료 변경[관리자 메모]</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <textarea class="form-control" name="cancel_complete_memo"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">취소</button>
                <button type="button" class="btn btn-primary" onclick="cancel_complete('{{Str::ucfirst(Auth::user()->name)}}');">메모 등록</button>
            </div>
        </div>
    </div>
</div>
<!-- 취소 완료 모달 끝 -->

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

<!-- 일괄 입력 모달 -->
<div class="modal fade" id="batch_input" tabindex="-1" aria-labelledby="deposit_completed" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5">선택 된 주문 일괄 입력</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="input-group mb-3">
                    <span class="input-group-text">관리자메모</span>
                    <textarea class="form-control" name="order_admin_memo" id="order_admin_memo"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">취소</button>
                <button type="button" class="btn btn-primary" onclick="batch_input()">일괄 입력</button>
            </div>
        </div>
    </div>
</div>
<!-- 일괄 입력 모달 끝-->

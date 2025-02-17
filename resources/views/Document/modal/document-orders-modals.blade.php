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
<!-- 환불 요청 모달 -->
<div class="modal fade" id="complain_progress" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 620px;">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">환불 처리</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="refund_modal_body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">취소</button>
                <button type="button" class="btn btn-primary" onclick="complain_submit();">환불 처리</button>
            </div>
        </div>
    </div>
</div>
<!-- 환불 요청 모달 끝 -->
<!-- 현금 영수증 모달 -->
<div class="modal fade" id="cash_receipt" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel2">현금영수증 발급</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-check form-check-inline mb-2">
                        <input class="form-check-input" type="radio" name="cashReceipt_type" id="cashReceipt_type1" value="소득공제" checked>
                        <label class="form-check-label" for="cashReceipt_type1">소득공제</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="cashReceipt_type" id="cashReceipt_type2" value="지출증빙">
                        <label class="form-check-label" for="cashReceipt_type2">지출증빙</label>
                    </div>
                    <div>
                        <label for="cashReceipt_od_id" class="col-form-label">주문 번호</label>
                        <input type="text" class="form-control" name="cashReceipt_od_id" id="cashReceipt_od_id" >
                    </div>
                    <div>
                        <label for="cashReceipt_number" class="col-form-label">번호</label>
                        <input type="text" class="form-control" name="cashReceipt_number" id="cashReceipt_number" >
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">취소</button>
                <button type="button" class="btn btn-primary" onclick="cashReceipt_submit();">발급</button>
            </div>
        </div>
    </div>
</div>
<!-- 형금 영수증 모달 끝 -->

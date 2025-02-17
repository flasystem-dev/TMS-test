<form id="refund_form">
    <input type="hidden" class="form-control" name="order_idx" id="order_idx" value="{{$order->order_idx}}">
    <input type="hidden" class="form-control" name="payment_number" id="payment_number" value="{{$payment->payment_number}}">
    <input type="hidden" class="form-control" name="payment_pg" id="payment_pg" value="{{$payment->payment_pg}}">
    <div>
        <table class="table table-striped payment_tbl">
            <thead>
            <tr>
                <th class="tbl_num">번호</th>
                <th class="tbl_type">결제수단<br>결제상태</th>
                <th class="tbl_price">결제금액</th>
                <th class="tbl_name">결제상품명</th>
                <th class="tbl_memo">결제메모</th>
            </tr>
            </thead>
            <tbody>
                <tr class="tbl_tr">
                    <td class="tbl_num">{{$payment->payment_number}}</td>
                    <td class="tbl_type"><p class="state_p {{$payment->payment_type_code}}">{{CommonCodeName($payment->payment_type_code)}}</p><p class="state_p mt-1 {{$payment->payment_state_code}}">{{CommonCodeName($payment->payment_state_code)}}</p></td>
                    <td class="tbl_price"><span class="pay_price">{{number_format($payment->payment_amount)}}</span></td>
                    <td class="tbl_name">{{$payment->payment_item}}</td>
                    <td class="tbl_memo">{{$payment->payment_memo}}</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div>
        <p class="mb-1">&#8251; <span class="sub_text">전 취소(승인 취소/전체 취소)</span> 취소 요청 직후 즉시 (전 취소는 당일 취소건에 한함)</p>
        <p class="mb-1">&#8251; <span class="sub_text">부분취소(후 취소 처리됨)</span> 취소 요청 후 영업일 기준 3~4일</p>
        <p class="mb-1">&#8251; <span class="sub_text">후 취소</span> 취소 요청 후 영업일 기준 3~4일</p>
    </div>
    <div>
        <label for="register_name" class="col-form-label">처리자</label>
        <input type="text" class="form-control" name="refund_handler" value="{{ Auth::user()->name }}">
    </div>
    <div>
        <label for="refund_amount" class="col-form-label">환불금액</label>
        <div class="input-group" id="refund_area">
            <input type="number" class="form-control" name="refund_amount" id="refund_amount" value="{{ $payment->payment_amount }}" @if(!($order->order_quantity > 1 && $payment->payment_number === 1)) readonly @endif>
            <input type="checkbox" class="btn-check" name="partial_cancel" value="1" id="partial_cancel" autocomplete="off" @if($order->order_quantity > 1 && $payment->payment_number === 1) checked @endif>
            @if(!($order->order_quantity > 1 && $payment->payment_number === 1))
            <label class="btn btn-outline-warning" for="partial_cancel">부분 환불</label>
            @endif
        </div>
    </div>
    @if($order->order_quantity > 1 && $payment->payment_number === 1)
    <div>
        <p class="mb-2 text-danger"> &#8251; 해당 주문은 다수 건의 주문으로 부분 환불로 진행됩니다.</p>
        <div class="text-end">
            <input type="checkbox" class="btn-check" name="all_cancel" value="Y" id="all_cancel" autocomplete="off">
            <label class="btn btn-outline-danger" for="all_cancel">해당 결제 일괄 취소</label>
        </div>
    </div>
    @endif
    <div class="mb-1">
        <label for="reason" class="col-form-label">환불 사유</label>
        <textarea class="form-control" name="reason" id="reason"></textarea>
    </div>
    <div class="{{$payment->payment_type_code==="PTVA"? "" : "d-none"}}" id="complain_account_info">
        <div class="mb-2">
            <label for="bank_info" class="col-form-label">은행명</label>
            <select class="form-select" aria-label="bank_info" name="bank_code" id="bank_code">
                <option value=''>-은행 선택-</option>
                @foreach($banks as $bank)
                    <option value="{{$bank -> code_no}}">{{$bank -> code_name}}</option>;
                @endforeach
            </select>
        </div>
        <div>
            <label for="account_number" class="col-form-label">계좌번호</label>
            <input type="text" class="form-control" name="account_number" id="account_number" >
        </div>
        <div>
            <label for="account_holder" class="col-form-label">예금주</label>
            <input type="text" class="form-control" name="account_holder" id="account_holder" >
        </div>
    </div>
</form>
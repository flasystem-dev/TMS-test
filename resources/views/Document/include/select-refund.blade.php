<form>
    <input type="hidden" class="form-control" name="order_idx" id="order_idx" value="{{$order->order_idx}}">
    <div>
        <label for="bank_info" class="col-form-label">결제선택</label>
        <table class="table table-striped payment_tbl">
            <thead>
            <tr>
                <th class="tbl_radio"></th>
                <th class="tbl_num">번호</th>
                <th class="tbl_type">결제수단<br>결제상태</th>
                <th class="tbl_price">결제금액</th>
                <th class="tbl_name">결제상품명</th>
                <th class="tbl_memo">결제메모</th>
            </tr>
            </thead>
            <tbody>
            @foreach($order->payment as $payment)
                <tr class="tbl_tr">
                    <td class="tbl_radio">
                        <input type="radio" class="tbl_payment_radio" name="payment_number" value="{{$payment->payment_number}}" data-type="{{$payment->payment_type_code}}" data-pg="{{$payment->payment_pg}}" onchange="checkType(event)" {{empty($payment->payment_key ? "disabled": "")}}>
                    </td>
                    <td class="tbl_num">{{$payment->payment_number}}</td>
                    <td class="tbl_type"><p class="state_p {{$payment->payment_type_code}}">{{CommonCodeName($payment->payment_type_code)}}</p><p class="state_p mt-1 {{$payment->payment_state_code}}">{{CommonCodeName($payment->payment_state_code)}}</p></td>
                    <td class="tbl_price"><span class="pay_price">{{number_format($payment->payment_amount)}}</span></td>
                    <td class="tbl_name">{{$payment->payment_item}}</td>
                    <td class="tbl_memo">{{$payment->payment_memo}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div>
        <label for="register_name" class="col-form-label">처리자</label>
        <input type="text" class="form-control" name="register_name" id="register_name" value="{{ Auth::user()->name }}">
    </div>
    <div class="mb-1">
        <label for="reason" class="col-form-label">환불 사유</label>
        <textarea class="form-control" name="reason" id="reason"></textarea>
    </div>
    <div class="d-none" id="complain_account_info">
        <div class="mb-2">
            <label for="bank_info" class="col-form-label">은행명</label>
            <select class="form-select" aria-label="bank_info" name="bank_code" id="bank_code">

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
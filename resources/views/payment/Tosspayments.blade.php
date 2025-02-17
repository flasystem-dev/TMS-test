@extends('layouts.master-without-nav')
@inject('DB', 'Illuminate\Support\Facades\DB')
<script src="https://js.tosspayments.com/v1/payment-widget"></script>

<div class="row mt-3">
    <div class="offset-4 col-4">
        <h3>상품  :  {{ $delivery -> goods_name }}</h3>
    </div>
</div>
<div class="row mt-2 mb-3">
    <div class="offset-4 col-4">
        <h3>금액  :  {{ number_format($order -> total_amount) }}원</h3>
    </div>
</div>
<div id="payment-method"></div>
<div class="w-100 p-1">
    <button type="button" class="btn btn-outline-primary w-100 rounded-4 fs-2" id="payment_btn">결제하기</button>
</div>
<script>
    @php
       $com_info =  DB::table('code_of_company_info') -> select('toss_client_key', 'toss_secret_key') -> where ('brand_type_code', '=', $order -> brand_type_code ) -> first();
    @endphp
    const pay_amount = {{ $order -> total_amount }};
    const od_id = '{{ $order -> od_id }}';
    const item = '{{ $delivery -> goods_name }}';
    const email = '{{ $order -> orderer_email }}';
    const name = '{{ $order -> orderer_name }}';

    const clientKey = '{{ $com_info -> toss_client_key }}';

    // 2. 결제위젯 SDK 초기화
    const paymentWidget = PaymentWidget(clientKey, PaymentWidget.ANONYMOUS);

    paymentWidget.renderPaymentMethods(
        '#payment-method',
        {
            value: pay_amount,
            currency: 'KRW',
            country: 'KR',
        },
        { variantKey: '{{ $order -> brand_type_code }}-admin'}
    )

    document.querySelector("#payment_btn").addEventListener("click",()=>{
        paymentWidget.requestPayment({
            amount: pay_amount,
            orderId: od_id,
            orderName: item,
            successUrl: '{{ route('toss-success') }}?handler={{ Auth::user() -> name }}', // 성공 리다이렉트 URL
            failUrl: main_url, // 실패 리다이렉트 URL
            customerEmail : email,
            customerName : name,
        })
        .catch(function (error) {
            if (error.code === 'USER_CANCEL') {
                alert("결제가 취소되었습니다.");
            } if (error.code === 'INVALID_CARD_COMPANY') {
                alert("유효하지 않은 카드입니다.");
            }
        });
    });
</script>

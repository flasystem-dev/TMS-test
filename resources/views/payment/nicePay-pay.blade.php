@extends('layouts.master-without-nav')
<script src="https://pg-web.nicepay.co.kr/v3/common/js/nicepay-pgweb.js" type="text/javascript"></script>
<style>
    .radio_btn { width: 150px; }
</style>

<!-- 수기 결제 모달 -->
<div class="modal fade" id="cardKeyIn" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">수기 결제</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="keyIn_form">
                    <div>
                        <span class="col-form-text mb-2">카드 번호</span>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" name="cardNum1" id="cardNum1" maxlength="4" aria-label="card_num" value="" oninput="moveToNext(this, 4)">
                            <input type="text" class="form-control" name="cardNum2" id="cardNum2" maxlength="4" aria-label="card_num" value="" onkeydown="checkBackspace(event)" oninput="moveToNext(this, 4)">
                            <input type="text" class="form-control" name="cardNum3" id="cardNum3" maxlength="4" aria-label="card_num" value="" onkeydown="checkBackspace(event)" oninput="moveToNext(this, 4)">
                            <input type="text" class="form-control" name="cardNum4" id="cardNum4" maxlength="6" aria-label="card_num" value="" onkeydown="checkBackspace(event)">
                        </div>
                    </div>
                    <div class="mb-1">
                        <span class="col-form-text mb-2">만료 기간 (YY/MM)</span>
                        <div class="input-group w-50">
                            <input type="text" class="form-control" name="exYear" id="exYear" aria-label="card_num" value="" maxlength="2" placeholder="YY" oninput="moveToNext(this, 2)">
                            <input type="text" class="form-control" name="exMonth" id="exMonth" aria-label="card_num" value="" maxlength="2" placeholder="MM" onkeydown="checkBackspace(event)">
                        </div>
                    </div>
                    <div class="mb-1">
                        <span class="col-form-text mb-2">할부</span>
                        <div class="input-group w-50">
                            <select class="form-select" name="cardQuota" id="cardQuota" aria-label="example">
                                <option value="00">일시불</option>
                                <option value="02">2개월</option>
                                <option value="03">3개월</option>
                                <option value="04">4개월</option>
                                <option value="05">5개월</option>
                                <option value="06">6개월</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="pay_progress(0);">키인 결제</button>
            </div>
        </div>
    </div>
</div>
<!-- 수기 결제 모달 끝 -->

<div class="row px-5 pt-5">
    <div class="card ">
        <div class="card-body">
            <div class="col-12">
                <table class="table table-striped m-0">
                    <thead>
                        <tr class="border">
                            <th>상품명</th>
                            <th>상품금액</th>
                            <th>할인금액</th>
                            <th>결제금액</th>
                        </tr>
                    </thead>
                    <tbody class="border">
                        @foreach($orders as $order)
                        <tr>
                            <td class="ps-4 fw-bold">{{ $order->delivery->goods_name }}</td>
                            <td class="text-end pe-3">{{ number_format($order->total_amount) }}원</td>
                            <td class="text-end pe-3">{{ number_format($order->discount_amount) }}원</td>
                            <td class="text-end pe-3">{{ number_format($order->misu_amount) }}원</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                <p class="m-0 text-end pe-4 fw-bold fs-3">총 합계 <span class="text-primary">{{number_format($tatal_sum_amount)}}</span> 원</p>
            </div>
        </div>
    </div>
</div>

<div class="row px-5">
    <div class="card">
        <div class="card-body">
            <div class="col-12 text-center">
                <input type="radio" class="btn-check" name="payment_type_code" value="PTMN" id="PTMN" autocomplete="off" checked>
                <label class="btn btn-outline-primary radio_btn mx-1" for="PTMN">수기카드</label>
                <input type="radio" class="btn-check" name="payment_type_code" value="PTVA" id="PTVA" autocomplete="off" >
                <label class="btn btn-outline-primary radio_btn mx-1" for="PTVA">가상계좌</label>
                <input type="radio" class="btn-check" name="payment_type_code" value="PTDP" id="PTDP" autocomplete="off" >
                <label class="btn btn-outline-primary radio_btn mx-1" for="PTDP">법인미수</label>
            </div>
        </div>
    </div>
</div>


<div class="row px-5 pt-3">
    <div class="col-12 d-grid text-center">
        <button class="btn btn-primary fs-3 w-50 mx-auto" onclick="pay_progress(1);">결제</button>
    </div>
</div>

<div id="payment_area"></div>

<script>
    function pay_progress(check) {
        const payType = document.querySelector('input[name="payment_type_code"]:checked').value;

        if(payType === 'PTMN' && check === 1) {
            const keyIn = new bootstrap.Modal('#cardKeyIn');
            keyIn.show();
            return;
        }

        $.ajax({
            url : main_url + "/order/pay/after",
            type : "POST",
            data : {
                'payment_type_code' : payType,
                'order_number' : '{{ $orders[0]->order_number }}',
                'cardNum1' : document.getElementById('cardNum1').value,
                'cardNum2' : document.getElementById('cardNum2').value,
                'cardNum3' : document.getElementById('cardNum3').value,
                'cardNum4' : document.getElementById('cardNum4').value,
                'exYear' : document.getElementById('exYear').value,
                'exMonth' : document.getElementById('exMonth').value,
                'cardQuota' : document.getElementById('cardQuota').value
            },
            success: function(data) {
                if(payType=='PTVA') {
                    $('#payment_area').html(data);
                    nicepayStart();
                }else {
                    alert(data.msg);
                    window.close();
                }
            },
            error: function(e) {
                alert('[에러발생] 개발팀에 문의하세요.');
                console.log(e);
            }
        })
    }

    // 번호 입력 후 자동 이동
    function moveToNext(current, length) {
        current.value = current.value.replace(/[^0-9]/g, '');
        if(current.value.length >= length) {
            current.nextElementSibling.focus();
        }
    }
    
    // 번호 삭제 시 이전 포커스
    function checkBackspace(e) {
        if(e.target.value.length === 0 && e.keyCode === 8) {
            e.target.previousElementSibling.focus();
        }
    }
</script>
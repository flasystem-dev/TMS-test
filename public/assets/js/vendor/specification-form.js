$(document).ready(function(){
    $('#deposit_date').datepicker();
})

$('#sp_order_amount, #sp_vendor_amount, #sp_vendor_options_amount, #sp_service_percent, #sp_card_amount, #sp_card_fee, #sp_etc1, #sp_etc2, #sp_etc3, #sp_service_fee').on('input', function(){
    calc_specification();
})

// 명세서 계산
function calc_specification() {
    var order_amount = document.getElementById('sp_order_amount').value;
    var vendor_amount = document.getElementById('sp_vendor_amount').value;
    var option_amount = document.getElementById('sp_vendor_options_amount').value;

    var service_percent = document.getElementById('sp_service_percent').value;
    var card_amount = document.getElementById('sp_card_amount').value;
    var card_fee = document.getElementById('sp_card_fee').value;
    var etc1 = parseInt(document.getElementById('sp_etc1').value);
    var etc2 = parseInt(document.getElementById('sp_etc2').value);
    var etc3 = parseInt(document.getElementById('sp_etc3').value);
    var service_price = parseInt(document.getElementById('sp_service_fee').value);

    // 발주 수익
    var profit = vendor_amount / 100 * service_percent;
    document.getElementById('profit').innerText = profit.toLocaleString();
    // 차액 수익
    var diff_amount = order_amount - vendor_amount - option_amount;
    document.getElementById('diff_amount').innerText = diff_amount.toLocaleString();
    // 기타 금액1
    var etc_price1 = etc2 + etc3;
    document.getElementById('etc_price1').innerText = etc_price1.toLocaleString();
    // 수익 총액
    var revenue = profit + diff_amount + etc_price1 - card_fee;
    $('.revenue').text(revenue.toLocaleString());
    // 원천징수
    let totalTax_ele = document.getElementById('sp_tax_amount_text');
    var totalTax = 0;
    if(totalTax_ele.dataset.tax) {
        totalTax = Math.floor((revenue / 100 * 3) / 10) * 10;
        totalTax += Math.floor((revenue / 100 * 0.3) / 10) * 10;
    }
    totalTax_ele.innerText = totalTax.toLocaleString();
    // 기타 금액2
    var etc_price2 = -etc1;
    document.getElementById('etc_price2').innerText = etc_price2.toLocaleString();
    // 공제 총액
    var deduction = totalTax + service_price + etc_price2;
    $('.deduction').text(deduction.toLocaleString());
    // 실 지급액
    document.getElementById('payment_amount').innerText = (revenue - deduction).toLocaleString();
    document.getElementById('sp_settlement_amount').value = revenue - deduction;
}

document.getElementById('sp_card_amount').addEventListener('input', function(){
    var card_charge = parseInt(document.getElementById('sp_card_amount').value);
    var card_fee = Math.floor((card_charge * 0.033) / 10) * 10;
    document.getElementById('sp_card_charge_fee_text').innerText = card_fee.toLocaleString();
})

function update_specification() {
    if(confirm('명세서를 수정하시겠습니까?')) {
        var formData = new FormData(document.getElementById('specification_form'));

        $.ajax({
            url: main_url + "/vendor/specification-form/edit",
            type: "POST",
            data: formData,
            processData: false, // FormData 객체로 전송할 때는 false로 설정
            contentType: false, // contentType도 false로 설정
            success: function(data) {
                alert('수정 완료');
                location.reload();
            },
            error: function(e) {
                alert('수정 실패');
                console.log(e);
            }
        })
    }
}
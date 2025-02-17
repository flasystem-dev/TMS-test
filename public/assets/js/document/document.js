var fix=1;
$('#start_date').datepicker();
$('#end_date').datepicker();

function order_detail(order_idx){
    var url = '../order/order-detail/'+order_idx;
    $('#new_order'+order_idx).hide();
    //window.open(url,"주문서","height=712,width=1440,left=200,top=100,resizable=no");
    open_win(url,"주문서"+fix,1440,712,200,200);
    fix++;
}
function open_admin_url(url){
    open_win(url,"관리자"+fix,1440,712,200,200);
    fix++;
}
function select_btn(id,value,hidden){
    $('#'+id).val(hidden);
    $('#'+id+'_title').text(value);
    $('#'+id+'_view').val(value);
}

const refund_modal = document.getElementById('complain_progress');
refund_modal.addEventListener('show.bs.modal', event=> {
    let btn = event.relatedTarget;
    var order_idx = btn.dataset.id;

    $.ajax({
        url: main_url + "/Document/cancel/table",
        data: { 'order_idx': order_idx },
        success: function(data){
            refund_modal_body.innerHTML = data;
            tr_click();
            document.querySelector('input[name="payment_number"][value="1"]').click();
        },
        error: function(e){
            alert('[에러발생] 개발팀에 문의하세요')
            console.log(e)
        }
    })
});



function bank_code(pg){
    $.ajax({
        url: main_url + '/Document/cancel/bank',
        type: 'GET',
        data: {
        'pg' : pg
        },
        success: function(data) {
            $('#bank_code').html(data);
        },
        error: function(e){
            alert("[에러발생]\n개발팀에 문의하세요.");
            console.log(e)
        }
    })
}

function complain_submit(){
    let radio = $('input[name="payment_number"]:checked');


    const pg = radio.data('pg');
    console.log(pg)

    var url = '';

    if(pg === 'toss') {
        url = main_url + '/Payment/Complain/toss';
    }else if(pg === 'nice') {
        url = main_url + '/Payment/Complain/nice';
    }

    if(confirm('환불하시겠습니까?')){
        $.ajax({
            url: url,
            type: "POST",
            data: {
                'order_idx' : $('#order_idx').val(),
                'register_name' : $('#register_name').val(),
                'reason' : $('#reason').val(),
                'bank_code' : $('#bank_code').val(),
                'account_number' : $('#account_number').val(),
                'account_holder' : $('#account_holder').val(),
                'payment_number' : $(radio).val()
            },
            success: function(data) {
                alert(data);
                location.reload();
            },
            error: function(error){
                alert('[에러 발생] 개발팀에 문의하세요');
                console.log(error);
            }
        });
    }
}

function popup_receipt(url){
    open_win(url,"영수증",800,1000,200,200);
}

function market_open(url){
    window.open(url,"상품확인","height=712,width=1440,left=300,top=200,resizable=no");
}

$('input[name="od_id[]"]').on('click',function(){
    let tr = $(this).parent().parent()
    if($(this).is(':checked')){
        $(tr).addClass('checkBox_active');
    }else {
        $(tr).removeClass('checkBox_active');
    }
});

function checkType(e) {

    let type = event.target.dataset.type;
    let pg = event.target.dataset.pg;

    if(type==null) {
        type = e.dataset.type;
        pg = e.dataset.pg;
    }

    if(type==="PTVA") {
        document.getElementById('complain_account_info').classList.remove('d-none')
    }else {
        document.getElementById('complain_account_info').classList.add('d-none')
    }
    bank_code(pg);
}



function tr_click() {
    document.querySelectorAll(".tbl_tr").forEach(function(tr){
        tr.addEventListener('click', function(){
            var radio = this.querySelector("input[name='payment_number']");
            if(radio) {
                radio.checked = true;
            }
            checkType(radio)
        })
    })
}

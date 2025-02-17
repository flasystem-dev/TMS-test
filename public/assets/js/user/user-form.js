$('.datepicker').datepicker();

$("#user_form").on("submit", function (e) {
    e.preventDefault();
    if(confirm("저장하시겠습니까?")) {
        var $this = $(this).parent();
        $.ajax({
            type: "POST",
            url: $(this).prop("action"),
            data: new FormData(this),
            contentType: false,
            cache: false,
            processData: false,
            success: function (data) {
                if(data){
                    location.reload();
                }
            }, error: function (error) {
                console.log(error);
            },
        });
    }
});

// 거래처 select2
$('#client_list').select2({
    placeholder: "이름, 대표자명, 사업자번호",
    templateResult: formatState,
    templateSelection: formatResult,
});

if($('#client_list').data('client')!=0){
    $('#client_list').val($('#client_list').data('client')).trigger('change');
}

// 거래처 select 옵션 css
function formatState(state){
    if (!state.id) {
        return state.text;
    }

    var $state = $(
        '<span class="client_name">' + $(state.element).data('name') + '</span>' +
        '<span class="client_ceoName">' + $(state.element).data('ceo-name') + '</span>' +
        '<span class="client_bsNumber">' + $(state.element).data('bs-number') + '</span>'
    );

    return $state;
}

// 거래처 select 선택 후 css
function formatResult(state){
    if (!state.id) {
        return state.text;
    }

    var $state = $(
        '<span class="client_name">' + $(state.element).data('name') + '</span>' +
        '<span class="client_ceoName">' + $(state.element).data('ceo-name') + '</span>' +
        '<span class="client_bsNumber">' + $(state.element).data('bs-number') + '</span>'
    );

    return $state;
}
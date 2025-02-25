function upload_excel(type) {
    var file = $('#orderPayment')[0].files[0];
    var url = main_url;

    switch (type){
        case 'orderPayment':
            file = $('#orderPayment')[0].files[0];
            url += "/dev/orderPayment";
            break;
        case 'user':
            file = $('#user')[0].files[0];
            url += "/dev/user";
            break;
        case 'vendor':
            file = $('#vendor')[0].files[0];
            url += "/dev/vendor";
            break;
    }

    if(file) {
        if(confirm('파일을 업로드 하시겠습니까?')) {
            var formData = new FormData();

            formData.append('files', file);
            $.ajax({
                url: url,
                method: "POST",
                data: formData,
                processData: false,  // false =>  formData를 string으로 변환하지 않음
                contentType: false,  // false =>  헤더가 multipart/form-data로 전송
                cache: false,
                success: function(data) {
                    alert(data);
                },
                error: function(e) {
                    alert('업로드 실패');
                    console.log(e)
                }
            })
        }
    } else {
        alert('파일을 선택해주세요.')
    }
}

function statistics_url() {
    $.ajax({
        url : main_url + "/dev/statistics/url",
        method: "GET",
    }).then(data => {
        if(data) {
            alert("계산 완료");
            location.reload();
        }else {
            alert("계산 실패")
        }
    }).catch(e => {
        alert('계산 실패');
        console.log(e)
    })
}

function upsert_orderItem() {
    $.ajax({
        url : main_url + "/dev/orderItem",
        method : "POST",
        data : { order_idx : document.querySelector('input[name="order_id"]').value },
        success: function(data) {
            if(data) {
                alert("업데이트 완료");
            }
        },
        error: function(e) {
            alert("업데이트 실패!")
        }
    });
}

// 상품 상세 이미지 업로드
function uploadImage(file, editor) {
    data = new FormData();
    data.append("detail_img", file);

    $.ajax({
        url: main_url+'/shop/product/upload/img',
        type: "POST",
        data: data,
        cache: false,
        contentType: false,
        processData: false,
        success: function(data) {
            if(data.state) {
                $('#description_area').summernote('insertImage', data.url);
            }else{
                alert("사진 업로드 실패");
            }
        }
    });
}

// 상품 대표 사진 변경
function change_img() {
    document.querySelector('input[name="img"]').click();
}

// 상품 대표 사진 변경 취소
function reset_img_src(url) {
    document.querySelector('#img_box').src = url;
    $('#product-img').val() = '';
}

// 상품 등록/수정
var pr_submit = function() {
    const check = $('#duplicate_code').text();
    const code = $('#product-code').val();
    
    if(check) {
        alert('이미 존재하는 상품코드입니다.');
        return;
    }
    
    if(!code) {
        alert('상품코드를 입력해주세요.');
        return;
    }

    // 상품가격 중복 값 확인
    const priceTypeValues = Array.from(document.querySelectorAll('select[name="price_type[]"]')).map(select => select.value);
    const hasDuplicates = priceTypeValues.some((item, index) => priceTypeValues.indexOf(item) !== index);
    const hasMandatoryValue = priceTypeValues.includes('1');

    if (!hasMandatoryValue) {
        alert('기본가격은 반드시 있어야 합니다.');
        return;
    }

    if (hasDuplicates) {
        alert('가격은 타입별 하나만 설정 가능합니다.');
        return;
    }

    if(confirm("상품을 등록/수정 하시겠습니까?")) {
        const content = $('#description_area').summernote('code');
        document.querySelector('textarea[name="description"]').innerHTML = content

        document.querySelector('#pr_form').submit();
    }
}

// 상품 삭제
function remove_product(id) {
    if(confirm("정말 상품을 삭제하시겠습니까?")) {
        $.ajax({
            url: main_url + "/shop/product/"+ id,
            type: 'delete',
            success: function(data) {
                if(data) {
                    alert("삭제 완료");
                    window.close();
                    
                }else {
                    alert("삭제 실패")
                }
            },
            error: function(error) {
                alert('[에러발생]개발팀에 문의하세요.');
                console.log(error);
            }
        })
    }
}

// 상품 코드 중복 확인
$('#product-code').on('keyup', function(){
    var message = '<p class="text-success" style="margin: 0 0 0 110px; height: 30px">사용 가능</p>';
    var error = '<p class="text-danger" id="duplicate_code" style="margin: 0 0 0 110px; height: 30px">이미 존재하는 코드입니다.</p>'

    if($(this).val() != '') {
        $.ajax({
            url: main_url + "/shop/product/check/duplicate-code",
            data: { code: $(this).val() },
            method: "GET",
            success: function(data) {
                console.log(data);

                if(data) {
                    document.querySelector('#error').innerHTML = error;
                }else {
                    document.querySelector('#error').innerHTML = message;
                }
            },
            error: function(error) {
                alert('중복 체크 검사 오류 발생');
                console.log(error);
            }
        })
    }else {
        document.querySelector('#error').innerHTML = '';
    }
})

// 검색어 추가
$('.add-search-word').on('click', function(){
    const word = $(this).closest('.row').find('.search-word').val();

    if(confirm('검색어를 추가하시겠습니까?')){
        $.ajax({
            url: main_url + "/shop/product/function/search-word",
            method: "POST",
            data: {
                'id' : document.querySelector('input[name="id"]').value,
                'word' : word,
            },
            success: function(data) {
                if(data) {
                    alert("추가 완료");
                    location.reload();
                }
            },
            error: function(error) {
                alert('[에러발생]개발팀에 문의하세요.');
                console.log(error)
            }
        })
    }
})


// 검색어 수정
$('.edit-search-word').on('click', function(){
    const pre_word = $(this).closest('.row').find('.previous-word').val();
    const now_word = $(this).closest('.row').find('.search-word').val();

    if(confirm('검색어를 수정하시겠습니까?')){
        $.ajax({
            url: main_url + "/shop/product/function/search-word",
            method: "PATCH",
            data: {
                'id' : document.querySelector('input[name="id"]').value,
                'pre_word' : pre_word,
                'now_word' : now_word
            },
            success: function(data) {
                if(data) {
                    alert("수정 완료");
                    location.reload();
                }
            },
            error: function(error) {
                alert('[에러발생]개발팀에 문의하세요.');
                console.log(error)
            }
        })
    }
});

// 검색어 삭제
$('.delete-search-word').on('click', function(){
    const word = $(this).closest('.row').find('.search-word').val();

    if(confirm('검색어를 삭제하시겠습니까?')){
        $.ajax({
            url: main_url + "/shop/product/function/search-word",
            method: "DELETE",
            data: {
                'id' : document.querySelector('input[name="id"]').value,
                'word' : word,
            },
            success: function(data) {
                if(data) {
                    alert('삭제 완료')
                    location.reload();
                }
            },
            error: function(error) {
                alert('[에러발생]개발팀에 문의하세요.');
                console.log(error)
            }
        })
    }
})


// 가격 타입 추가
function add_product_price() {
    let template = $('#product-price').html();
    $('#product-price-container').append(template);
}

// 가격 타입 삭제
$('#product-price-container').on('click', '.remove-product-price', function () {
    if (confirm("해당 상품 가격을 삭제 하시겠습니까?")) {
        $(this).parent().parent().remove();
    }
});

// 상품 옵션 추가
$('#add-product-option').on('click', function(){
    let template = $('#product-option').html();
    $('#product-options-container').append(template);
});

// 상품 옵션 삭제
$('#product-options-container').on('click', '.remove-product-option', function () {
    if (confirm("해당 상품 옵션을 삭제 하시겠습니까?")) {
        $(this).parent().parent().parent().parent().remove();
    }
});
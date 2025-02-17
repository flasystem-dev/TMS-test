function view_specification(id) {
    const url = main_url + "/vendor/specification-form/"+id;
    open_win(url,"명세서" ,900, 900, 0, 40);
}

// 연도 변경 시 페이지 이동
$('select[name="select_year"]').on('change', function(){
    const year = $('#select_year').val();

    const params = new URLSearchParams({
        year: year,
    });

    const targetUrl = `${window.location.pathname}?${params.toString()}`;

    window.location.href = targetUrl;
})
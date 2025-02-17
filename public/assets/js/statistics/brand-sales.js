// 매출 데이터테이블
const table = $('#sales_table').DataTable({
    paging : false,
    lengthChange: false,
    info: false,
    searching: false,
    processing: true,       // 로딩 스피너 표시
    serverSide: false,       // 서버 측 처리 활성화 ( 차순 변경 버튼 )
    order: [],              // 서버 측 orderBy 적용
    ajax: {
        url: main_url + '/api/statistics/brand/sales/table-data',
        method: 'GET',
        dataSrc: '',
        data: function(d) {
            const dateType = $('input[name="tbl_date_type"]:checked').val();
            // 현재 선택된 radio 버튼의 값을 파라미터로 추가
            d.dateType = dateType;
        }
    },
    columns: [
        { data: 'brand'         , className: 'brand_name'},
        { data: 'today_cnt'     , className: 'amount today_data'},
        { data: 'today_sales'   , className: 'amount today_data'},
        { data: 'monthly_cnt'   , className: 'amount monthly_data'},
        { data: 'monthly_sales' , className: 'amount monthly_data'},
        { data: 'yearly_cnt'    , className: 'amount yearly_data'},
        { data: 'yearly_sales'  , className: 'amount yearly_data'},
        { data: 'yearAgo_cnt'   , className: 'amount yearAgo_data'},
        { data: 'yearAgo_sales' , className: 'amount yearAgo_data'},
    ],
    createdRow: function(row, data, dataIndex) {
        // `data.brand` 값에 따라 클래스 추가
        if (data.brand === '꽃파는총각') {
            $(row).find('td:eq(0)').addClass('BTCP');
        } else if (data.brand === '칙칙폭폭플라워') {
            $(row).find('td:eq(0)').addClass('BTCC');
        } else if (data.brand === '사팔플라워') {
            $(row).find('td:eq(0)').addClass('BTSP');
        } else if (data.brand === '바로플라워') {
            $(row).find('td:eq(0)').addClass('BTBR');
        } else if (data.brand === '오만플라워') {
            $(row).find('td:eq(0)').addClass('BTOM');
        } else if (data.brand === '꽃파는사람들') {
            $(row).find('td:eq(0)').addClass('BTCS');
        } else if (data.brand === '플라체인') {
            $(row).find('td:eq(0)').addClass('BTFC');
        }
    }
});

// 매출 풀캘린더
const calendarEl = document.getElementById('calendar');
let calendar;
calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    height: 630,
    locale: 'ko',
    headerToolbar: {
        left: 'prev',           // 왼쪽에 이전 달 버튼
        center: 'title',        // 가운데에 년/월 타이틀
        right: 'next'           // 오른쪽에 다음 달 버튼
    },
    events: function( fetchInfo, successCallback, failureCallback ) {
        const start = fetchInfo.startStr;
        const end = fetchInfo.endStr;

        const brand = $('input[name="brand_btns"]:checked').val();
        const dateType = $('input[name="date_type"]:checked').val();

        $.ajax({
            url: main_url + "/api/statistics/brand/sales/calendar-data",
            method: "GET",
            data: {
                brand: brand,
                dateType: dateType,
                start: start,
                end: end
            },
            success: function (data) {
                document.getElementById('monthly_sales_count').innerText = parseInt(data.month_sales.order_count).toLocaleString();
                document.getElementById('monthly_sales_amount').innerText = parseInt(data.month_sales.order_amount).toLocaleString();

                successCallback(data.events);
            },
            error: function (e) {
                console.log(e)
                alert("캘린더 렌더링 실패")
                failureCallback(e);
            }
        });
    },
    eventDidMount: function(info) {
        // title이 HTML을 해석하도록 설정
        info.el.querySelector('.fc-event-title').innerHTML = info.event.title;
    }
});
calendar.render();

// 차트 데이터 API
function chart_dataParam() {
    const brand = $('input[name="chart_brand_btns"]:checked').val();
    const year = $('select[name="chart_year"]').val();
    const dateType = $('input[name="chart_date_type"]:checked').val();

    const params = new URLSearchParams();
    params.append('brand', brand)
    params.append('year', year)
    params.append('dateType', dateType)

    return params.toString();
}

// 브랜드, 날짜 기준 변경 시 풀캘린더 리로드
$('input[name="brand_btns"], input[name="date_type"]').on('change', function() {
    calendar.refetchEvents();
});

// 데이터 테이블 변경 시 페이지 이동
$('input[name="tbl_date_type"]').on('change', function() {
    table.ajax.reload(null, false);
});
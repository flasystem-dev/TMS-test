// 매출 풀캘린더
const calendarEl = document.getElementById('calendar');

const selected_month = $('input[name="selected_month"]').val();
const calendar = new FullCalendar.Calendar(calendarEl, {
    initialDate: selected_month,
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

        const vendor = $('input[name="vendor"]').val();
        const dateType = $('input[name="date_type"]:checked').val();

        $.ajax({
            url: main_url + "/api/statistics/vendor/sales/calendar-data",
            method: "GET",
            data: {
                vendor: vendor,
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

// 브랜드, 날짜 기준 변경 시 풀캘린더 리로드
$('input[name="date_type"]').on('change', function() {
    calendar.refetchEvents();
});

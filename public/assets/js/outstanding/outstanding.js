// 전전달 마지막 날 가져오기
function get2MonthAgoLastDay() {
    let today = new Date();
    let lastDayOfPreviousMonth = new Date(today.getFullYear(), today.getMonth() - 1, 0);

    let year = lastDayOfPreviousMonth.getFullYear();
    let month = String(lastDayOfPreviousMonth.getMonth() + 1).padStart(2, '0'); // 1~9월 앞에 0 추가
    let day = String(lastDayOfPreviousMonth.getDate()).padStart(2, '0');

    return `${year}-${month}-${day}`;
}

// 1년 전의 날짜 구하기
function getYearAgoDate() {
    let today = new Date();
    // 현재 연도에서 1년 빼고, 월과 일은 그대로 사용
    let oneYearAgo = new Date(today.getFullYear() - 1, today.getMonth(), today.getDate());

    let year = oneYearAgo.getFullYear();
    let month = String(oneYearAgo.getMonth() + 1).padStart(2, '0'); // 월을 01~12 형식으로 변환
    let day = String(oneYearAgo.getDate()).padStart(2, '0');

    return `${year}-${month}-${day}`;
}

// 전달의 첫번째 날 구하기
function getPreviousMonthFirstDay() {
    let today = new Date();
    // 현재 월에서 1을 빼고, 날짜는 1일로 설정
    let firstDayOfPreviousMonth = new Date(today.getFullYear(), today.getMonth() - 1, 1);

    let year = firstDayOfPreviousMonth.getFullYear();
    let month = String(firstDayOfPreviousMonth.getMonth() + 1).padStart(2, '0');
    let day = String(firstDayOfPreviousMonth.getDate()).padStart(2, '0');

    return `${year}-${month}-${day}`;
}

// 전달의 마지막 날 구하기
function getPreviousMonthLastDay() {
    let today = new Date();
    // 현재 월에서 1을 빼고, 날짜를 0으로 설정하여 이전 달의 마지막 날 계산
    let lastDayOfPreviousMonth = new Date(today.getFullYear(), today.getMonth() - 1, 0);

    let year = lastDayOfPreviousMonth.getFullYear();
    let month = String(lastDayOfPreviousMonth.getMonth() + 1).padStart(2, '0');
    let day = String(lastDayOfPreviousMonth.getDate()).padStart(2, '0');

    return `${year}-${month}-${day}`;
}
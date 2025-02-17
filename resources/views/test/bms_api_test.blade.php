<!DOCTYPE html>
<html>
<head>
    <title>Redirecting...</title>
</head>
<body>
<form id="dataForm" method="POST" action="https://partner.flabiz.kr:3000/api/bms/oi">

</form>

<script type="text/javascript">
    const data = {
        "key": "testtest",
        "order_number": "1234-1234",
        "event_URL": "https://test.com",
        "groom_info": [
            {
                "title": "신랑",
                "name": "테스트",
                "phone": "010-1111-1111",
            },
            {
                "title": "신랑측 아버지",
                "name": "이인수",
                "phone": "010-2222-2222",
            },
            {
                "title": "신랑측 어머니",
                "name": "이영희",
                "phone": "010-3333-3333",
            }
        ],
        "bride_info": [
            {
                "title": "신부",
                "name": "김은재",
                "phone": "010-4444-4444",
            },
            {
                "title": "신부측 아버지",
                "name": "김희승",
                "phone": "010-5555-5555",
            },
            // {
            //     "title": "신랑측 어머니",
            //     "name": "김수연",
            //     "phone": "010-6666-6666",
            // }
        ],
        "funeral_info": [
            {
                "title": "상주",
                "name": "홍길동",
                "phone": "010-1234-1234",
            }
        ],
        "delivery_post": "123-45",
        "delivery_address": "부산광역시 중앙대로 623 6층 (주)플라시스템",
        "delivery_date": "2024-11-11",
        "delivery_time": "11:30:00"
    };

    // 폼에 데이터를 추가하는 함수
    function appendHiddenFields(form, key, value) {
        if (typeof value === 'object' && !Array.isArray(value)) {
            // 객체일 경우, 재귀적으로 필드를 추가
            for (const subKey in value) {
                appendHiddenFields(form, `${key}[${subKey}]`, value[subKey]);
            }
        } else if (Array.isArray(value)) {
            // 배열일 경우, 각 배열 항목을 객체로 처리
            value.forEach((item, index) => {
                appendHiddenFields(form, `${key}[${index}]`, item);
            });
        } else {
            // 일반 값일 경우, hidden input 필드를 추가
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = key;
            input.value = value;
            form.appendChild(input);
        }
    }

    // 폼에 데이터를 넣고 자동 제출
    window.onload = function() {
        const form = document.getElementById('dataForm');
        for (const key in data) {
            appendHiddenFields(form, key, data[key]);
        }

        // 폼을 자동으로 제출
        form.submit();
    };
</script>
</body>
</html>
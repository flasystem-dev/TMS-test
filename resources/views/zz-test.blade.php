<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Popbill Kakao Response.</title>
</head>
<body>
<div>
    <p>Response</p>
    <fieldset>
        <ul>
            @if ( $value != "error" )
                <li>접수번호(ReceiptNum) :  {{ $value }}</li>
            @else
                <li>오류 코드(code) :  {{ $code }}</li>
                <li>오류 메시지(message) :  {{ $message }}</li>
            @endif
        </ul>
    </fieldset>
</div>
</body>
</html>
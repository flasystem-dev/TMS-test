<style>
    table thead th { padding: 10px; font-size: 20px; }
    table tbody td { text-align: center; padding: 10px; border-radius: 10px; font-weight: bolder; }
    .th_brand { color: #f65151; }
    .th_mall { color: #00b41d; }
    .brand { border: 3px solid #f65151; }
    .mall { border: 3px solid #00b41d; }
    .container { text-align: center; }
    .btnLightBlue { background: #5DC8CD; }
    .btnLightBlue.btnPush {  box-shadow: 0px 5px 0px 0px #1E8185; }
    .btnLightBlue.btnPush:hover { box-shadow: 0px 0px 0px 0px #1E8185; }
    .btnPush:hover { margin-top: 15px; margin-bottom: 5px; }
    a.button {
        display: inline-block;
        position: relative;
        width: 150px;
        padding: 0;
        margin: 10px 20px 10px 0;
        font-weight: 600;
        text-align: center;
        line-height: 50px;
        color: #FFF;
        border-radius: 5px;
        transition: all 0.2s ;
    }
</style>

<div style="width: 70%; margin: 50px auto; border: 10px solid #ddd; border-radius: 10px; padding: 50px; box-sizing: border-box">
    <h1 style="text-align: center">쇼핑몰 비밀번호 변경</h1>
    <div style="margin-top: 30px;">
        <h3 style="color: #eeac50; text-align: center">비밀번호 변경이 확인되어 자동메일링</h3>
    </div>
    <table style="width: 50%; margin: 30px auto; border-spacing: 10px;">
        <thead>
            <tr>
                <th class="th_brand">브랜드</th>
                <th class="th_mall">쇼핑몰</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="brand">{{ $brand }}</td>
                <td class="mall">{{ $mall }}</td>
            </tr>
        </tbody>
    </table>
    <div class="container">
        <a href="https://tms.flabiz.kr" title="Button push lightblue" class="button btnPush btnLightBlue">TMS로 이동</a>
    </div>
</div>
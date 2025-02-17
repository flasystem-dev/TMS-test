<div style = 'width:70%;height:400px;background:#FFF;margin:40px auto;position:relative;border:solid 8px #ccc;border-radius:10px;'>
    <h2 style='text-align:center;position:relative;;margin:50px;font-size:20px;border-bottom:solid 1px #ccc;padding-bottom:20px'>안녕하세요. {{ $vendor->brand_type === "BTCS"? "꽃파는사람들" : "플라체인" }} 입니다.</h2>
    <p style='text-align:center;position:relative;font-size:13px;'>{{ $vendor->brand_type === "BTCS"? "꽃파는사람들" : "플라체인" }}을 이용해주셔서 감사합니다. </p>
    <p style='text-align:center;position:relative;font-size:13px;margin-top:10px'><b style="font-size: 15px; color: #4280fb">{{ $year. "년 " . $month . "월" }}</b> 지급액 명세서 보내드립니다. </p>
    <p style='text-align:center;position:relative;font-size:13px;margin-top:10px'>확인 및 요청사항이 있으신 경우  <b style="font-size: 15px; color: #4280fb">{{ $vendor->brand_type === "BTCS"? "1877-8228" : "1811-2666" }}</b> 또는 본 메일로 회신 부탁드립니다.</p>
    <p style='text-align:center;position:relative;font-size:13px;margin-top:10px'>감사합니다.  </p>
    <div style='text-align:center;margin-bottom: 10px;'>
        <a style='text-decoration:none;text-align:center;position:relative;top:50px;font-size:20px;color:#fff;background-color:#164cd3;padding:15px;border-radius:5px' href="{{ $vendor -> specification_url }}">
            지급액 명세서 확인하기
        </a>
    </div>
    <br>
</div>
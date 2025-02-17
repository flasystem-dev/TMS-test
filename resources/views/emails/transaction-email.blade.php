<div style = 'width:70%;height:570px;background:#FFF;margin:40px auto;position:relative;border:solid 8px #ccc;border-radius:10px;'>
    <h2 style='text-align:center;position:relative;;margin:50px;font-size:20px;border-bottom:solid 1px #ccc;padding-bottom:20px'>안녕하세요. {{ $brand }} 입니다.</h2>
    <p style='text-align:center;position:relative;font-size:13px;'>{{ $brand }}을 이용해주셔서 감사합니다. </p>
    <p style='text-align:center;position:relative;font-size:13px;margin-top:10px'><b style="font-size: 15px; color: #4280fb">{{ $receipt_date }}</b> 거래내역서 보내드립니다. </p>
    <p style='text-align:center;position:relative;font-size:13px;margin-top:10px'>확인 및 요청사항이 있으신 경우  <b style="font-size: 15px; color: #4280fb">{{ $brand_tel }}</b> 또는 본 메일로 회신 부탁드립니다.</p>
    <p style='text-align:center;position:relative;font-size:13px;margin-top:10px'>감사합니다.  </p>
    <div style='text-align:center;margin-bottom: 10px;'>
        <a style='text-decoration:none;text-align:center;position:relative;top:50px;font-size:20px;color:#fff;background-color:#164cd3;padding:15px;border-radius:5px' href="{{ $link }}">
            거래내역서 확인하기
        </a>
    </div>
    <br>
    @if($brand == '오만플라워')
        <div style='text-align:center;'>
            <a style='text-decoration:none;text-align:center;position:relative;top:60px;font-size:20px;color:#2d2c26;background-color:#ffda00;font-weight: 540;padding:10px 40px;border-radius:30px' href='http://pf.kakao.com/_elxftd'>카카오채널</a>
        </div>
    @elseif($brand == '바로플라워')
        <div style='text-align:center;'>
            <a style='text-decoration:none;text-align:center;position:relative;top:60px;font-size:20px;color:#2d2c26;background-color:#ffda00;font-weight: 540;padding:10px 40px;border-radius:30px' href='http://pf.kakao.com/_xkxddPM'>카카오채널</a>
        </div>
    @endif
</div>
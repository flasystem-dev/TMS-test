<script type="text/javascript">
    //결제창 최초 요청시 실행됩니다.
    function nicepayStart(){
        goPay(document.payForm);
    }

    //[PC 결제창 전용]결제 최종 요청시 실행됩니다. <<'nicepaySubmit()' 이름 수정 불가능>>
    function nicepaySubmit(){
        document.payForm.submit();
    }

    //[PC 결제창 전용]결제창 종료 함수 <<'nicepayClose()' 이름 수정 불가능>>
    function nicepayClose(){
        alert("결제가 취소 되었습니다");
    }
</script>
<form name="payForm" class="d-none" method="post" action="{{ $targetURL }}">
    @csrf
    <input type="hidden" name="PayMethod" value=""> <!-- 결제 수단 -->
    <input type="hidden" name="GoodsName" value="{{$goodsName}}"> <!-- 결제 상품명 -->
    <input type="hidden" name="Amt" value="{{$price}}"> <!-- 결제 상품금액 -->
    <input type="hidden" name="MID" value="{{$MID}}"> <!-- 상점 아이디 -->
    <input type="hidden" name="Moid" value="{{$moid}}"> <!-- 상점 주문번호 -->
    <input type="hidden" name="BuyerName" value="{{$buyerName}}"> <!-- 구매자명 -->
    <input type="hidden" name="BuyerEmail" value="{{$buyerEmail}}"> <!-- 구매자명 이메일 -->
    <input type="hidden" name="BuyerTel" value="{{$buyerTel}}"> <!-- 구매자 연락처 -->
    <input type="hidden" name="ReturnURL" value="{{$returnURL}}"> <!-- 인증완료 결과처리 URL (모바일 결제창 전용)PC 결제창 사용시 필요 없음 -->
    <input type="hidden" name="VbankExpDate" value=""> <!-- 가상계좌입금만료일(YYYYMMDD) -->

    <!-- 옵션 -->
    <input type="hidden" name="GoodsCl" value="1"/>						<!-- 상품구분(실물(1),컨텐츠(0)) -->
    <input type="hidden" name="TransType" value="0"/>					<!-- 일반(0)/에스크로(1) -->
    <input type="hidden" name="CharSet" value="utf-8"/>				<!-- 응답 파라미터 인코딩 방식 -->
    <input type="hidden" name="ReqReserved" value=""/>					<!-- 상점 예약필드 -->

    <!-- 변경 불가능 -->
    <input type="hidden" name="EdiDate" value="{{$ediDate}}"/>			<!-- 전문 생성일시 -->
    <input type="hidden" name="SignData" value="{{$hashString}}"/>	<!-- 해쉬값 -->
</form>


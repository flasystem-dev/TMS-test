<style>
    table th, table td {
        border: 1px solid #000000
    }
</style>

<table>
    <thead>
    <tr>
        <th>{{count($orders)}} 건</th>
    </tr>
    <tr>
        <th>번호</th>
        <th>주문번호</th>
        <th>주문일시</th>
        <th>주문자ID</th>
        <th>회원구분</th>
        <th>사업자명</th>
        <th>주문자연락처1</th>
        <th>주문자연락처2</th>
        <th>배송상태</th>
        <th>상품명</th>
        <th>수량</th>
        <th>단가</th>
        <th>사용적립금</th>
        <th>적립적립금</th>
        <th>결제방법</th>
        <th>결제금액</th>
        <th>결제상태</th>
        <th>승인번호</th>
        <th>입금은행</th>
        <th>입금일자</th>
        <th>입금자명</th>
        <th>주문자명</th>
        <th>희망배송일</th>
        <th>받는사람</th>
        <th>받는사람연락처1</th>
        <th>받는사람연락처2</th>
        <th>받는사람주소</th>
        <th>메세지타입</th>
        <th>메세지</th>
        <th>요구사항</th>
        <th>관리자메모</th>
        <th>증빙서류(발행여부)</th>
        <th>수기주문</th>
        <th>발주여부</th>
        <th>사업자발주</th>
        <th>사업자옵션</th>
        <th>화원사발주</th>
        <th>화원사옵션</th>
        <th>경조사어</th>
        <th>보내는분</th>
    </tr>
    </thead>
    <tbody>
    @foreach($orders as $order)
        <tr>
            <!-- 번호 -->
            <td>{{$loop->index + 1}}</td>
            <!-- 주문번호 -->
            <td style="text-align: center">{{"'" . $order->od_id."'"}}</td>
            <!-- 주문일시 -->
            <td style="text-align: center">{{$order->order_time}}</td>
            <!-- 주문자ID -->
            <td style="text-align: center">{{$order->orderer_mall_id ?? ''}}</td>
            <!-- 회원구분 -->
            <td>{{!empty($order->orderer_mall_id)? "일반회원" : "비회원"}}</td>
            <!-- 사업자명 [5]-->
            <td>{{$order->vendor->rep_name ?? ""}}</td>
            <!-- 주문자연락처1 -->
            <td style="text-align: center">{{$order->orderer_phone}}</td>
            <!-- 주문자연락처2 -->
            <td style="text-align: center">{{$order->orderer_tel}}</td>
            <!-- 배송상태 -->
            <td style="text-align: center">{{CommonCodeName($order->delivery->delivery_state_code)}}</td>
            <!-- 상품명 -->
            <td>{{$order->delivery->goods_name}}</td>
            <!-- 수량 [10]-->
            <td>1</td>
            <!-- 단가 -->
            <td>{{$order->item->item_total_amount}}</td>
            <!-- 사용적립금 -->
            <td>0</td>
            <!-- 적립적립금 -->
            <td>0</td>
            <!-- 결제방법 -->
            <td style="text-align: center">{{CommonCodeName($order->payment_type_code)}}</td>
            <!-- 결제금액 [15]-->
            <td>{{$order->total_amount}}</td>
            <!-- 결제상태 -->
            <td style="text-align: center">{{CommonCodeName($order->payment_state_code)}}</td>
            <!-- 승인번호 -->
            <td></td>
            <!-- 입금은행 -->
            <td></td>
            <!-- 입금일자 -->
            <td style="text-align: center">{{$order->payment_date}}</td>
            <!-- 입금자명 [20]-->
            <td>{{$order->payments->first()?->deposit_name ?? ''}}</td>
            <!-- 보내는사람 -->
            <td>{{$order->orderer_name}}</td>
            <!-- 희망배송일 -->
            <td style="text-align: center">{{$order->delivery->delivery_date}}</td>
            <!-- 받는사람 -->
            <td>{{$order->delivery->receiver_name}}</td>
            <!-- 받는사람연락처1 -->
            <td style="text-align: center">{{$order->delivery->receiver_phone}}</td>
            <!-- 받는사람연락처2 [25]-->
            <td style="text-align: center">{{$order->delivery->receiver_tel}}</td>
            <!-- 받는사람주소 -->
            <td>{{$order->delivery->delivery_address}}</td>
            <!-- 메세지타입 -->
            <td style="text-align: center">{{empty($order->delivery->delivery_card)? "리본":"카드"}}</td>
            @php
                $right = $order->delivery->delivery_ribbon_right;
                $left = !empty($order->delivery->delivery_ribbon_left) ? "(".$order->delivery->delivery_ribbon_left.")": "";
            @endphp
                    <!-- 메세지 -->
            <td>{{empty($order->delivery->delivery_card)? $order->delivery->delivery_ribbon_right.$left :$order->delivery->delivery_card}}</td>
            <!-- 요구사항 -->
            <td>{{$order->delivery_message}}</td>
            <!-- 관리자메모 [30]-->
            <td>{{$order->admin_memo}}</td>
            <!-- 증빙서류(발행여부) -->
            <td></td>
            <!-- 수기주문 -->
            <td>{{$order->handler}}</td>
            <!-- 발주여부-->
            <td style="text-align: center">{{$order->delivery->is_balju===1 ? "발주" : "미발주"}}</td>
            <!-- 사업자발주 -->
            <td>{{$order->vendor_amount}}</td>
            <!-- 사업자옵션 [35]-->
            <td>{{$order->item->vendor_options_amount}}</td>
            <!-- 화원사발주 -->
            <td>{{$order->balju_amount}}</td>
            <!-- 화원사옵션 -->
            <td>{{$order->item->balju_options_amount}}</td>
            <!-- 경조사어 -->
            <td>{{$order->delivery->delivery_ribbon_right}}</td>
            <!-- 보내는분 -->
            <td>{{$order->delivery->delivery_ribbon_left}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
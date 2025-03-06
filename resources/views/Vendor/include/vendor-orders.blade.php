<div class="row mt-2" >
<div class="col-12 pt-2 px-5">
<div class="card m-0">
<div class="card-body py-2">
<table class="table table-striped">
    <thead>
    <tr>
        <th style="width: 70px">번호</th>
        <th style="width: 100px">수집일<br>배송일</th>
        <th style="width: 120px">주문자<br>연락처</th>
        <th style="width: 120px">받는분<br>연락처</th>
        <th style="width: 150px">주문상품<br>결제금액</th>
        <th style="width: 200px">배송지<br>보내는분</th>
        <th style="width: 96px;">결제수단</th>
        <th style="width: 96px">결제상태<br>배송상태</th>
        <th>담당자</th>
        <th>기타</th>
    </tr>
    </thead>
    <tbody>
    @if(!$orders -> isEmpty())
    @foreach($orders as $order)
        <tr>
            <!-- 번호 -->
            <td>{{$order->order_idx}}</td>
            <!-- 수집일 / 배송일 -->
            <td>
                <a class="cursor_pointer text-dark" onclick="order_detail('{{$order->order_idx}}');">{{Carbon\Carbon::parse($order->create_ts)->format('Y-m-d')}}<br>
                    <p class="deli_date">{{$order->delivery->delivery_date}}</p>
                </a>
            </td>
            <!-- 주문자 / 연락처 -->
            <td><p class="gs_name" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="right" data-bs-content="{{$order->orderer_name}}">{{$order->orderer_name}}</p>{{$order->orderer_phone}}</td>
            <!-- 받는분 / 연락처 -->
            <td><p class="gs_name" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="right" data-bs-content="{{$order->delivery->receiver_name}}">{{$order->delivery->receiver_name}}</p>{{$order->delivery->receiver_phone}}</td>
            <!-- 주문상품 / 결제금액 -->
            <td><p class="gs_name" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="right" data-bs-content="{{$order->delivery->goods_name}}">{{$order->delivery->goods_name}}</p><p class="amount">{{number_format($order->pay_amount)}} 원</p></td>
            <!-- 배송지 / 보내는분 -->
            <td>
                <p class="addr cursor_p" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="right" data-bs-content="{{$order->delivery->delivery_address}}" onclick="clipBoardCopy(event)">{{$order->delivery->delivery_address}}</p>
                <p class="left_ribbon cursor_p" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="right" data-bs-content="{{$order->delivery->delivery_ribbon_left}}" onclick="clipBoardCopy(event)">{{$order->delivery->delivery_ribbon_left}}</p>
            </td>
            <!-- 결제수단 -->
            <td><p class="state_p {{$order->payment_type_code}}" >{{ CommonCodeName($order->payment_type_code) }}</p></td>
            <!-- 결제상태 / 배송상태 -->
            <td>
                <p class="state_p {{$order->payment_state_code}}" >{{CommonCodeName($order->payment_state_code)}}</p>
                <p class="state_p mt-1 {{$order->delivery->delivery_state_code}}">{{CommonCodeName($order->delivery->delivery_state_code)}}</p>
            </td>
            <!-- 담당자 -->
            <td><p class="m-0 fw-bold">{{$order->handler}}</p></td>
            <!-- 기타 -->
            <td>
{{--                <button type="button" class="btn btn-outline-secondary btn-sm">주문</button>--}}
            </td>
        </tr>
    @endforeach
    @else
        <tr>
            <td colspan="10">
                <p style="padding: 50px; font-size: 20px; font-weight: bold">주문이 존재하지 않습니다.</p>
            </td>
        </tr>
    @endif
    </tbody>
</table>
</div>
</div>
</div>
</div>
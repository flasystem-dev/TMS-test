<table>
    <thead>
    <tr>
        <th>차례</th>
        <th>아이디</th>
        <th>화원명</th>
        <th>대표자</th>
        <th>전화번호</th>
        <th>은행</th>
        <th>계좌</th>
        <th>예금주</th>
        <th>원천징수(3.3)공제</th>
        <th>총발주건수</th>
        <th>당월<br>원청금액</th>
        <th>당월<br>화훼금액</th>
        <th>당월<br>옵션금액</th>
        <th>수수료%</th>
        <th>발주수익</th>
        <th>원청금액과<br>화훼금액의차액</th>
        <th>수익총액</th>
        <th>원천징수<br>소득세(3%)</th>
        <th>원천징수<br>주민세(0.3%)</th>
        <th>원천징수<br>합계</th>
        <th>서비스이용료</th>
        <th>분양몰관리비</th>
        <th>당월<br>가맹관리비</th>
        <th>카드<br>총액</th>
        <th>당월주문<br>미입금액</th>
        <th>기타금액(1)</th>
        <th>기타금액(2)</th>
        <th>기타금액(3)</th>
        <th>실제지급액<br>(반올림)</th>
        <th>발급여부</th>
        <th>입금예정일</th>
    </tr>
    </thead>
    <tbody>
    @foreach($vendors as $vendor)
        <tr>
            <td style="text-align: center">{{$loop->index + 1}}</td>
            <td style="text-align: center">{{$vendor->vendor_id}}</td>
            <td style="text-align: center">{{$vendor->mall_name}}</td>
            <td style="text-align: center">{{$vendor->rep_name}}</td>
            <td style="text-align: center">{{$vendor->gen_number}}</td>
            <td style="text-align: center">{{$vendor->bank_name}}</td>
            <td style="text-align: center">{{$vendor->bank_number}}</td>
            <td style="text-align: center; width: 300px;">{{$vendor->name_of_deposit}}</td>
            <td style="text-align: center">{{$vendor->rep_type==="개인"? "" : "원천징수 미공제"}}</td>
            <td>{{$vendor->order_count()}}</td>
            <td>{{$vendor->order_amount()}}</td>
            <td>{{$vendor->vendor_amount()}}</td>
            <td>{{$vendor->option_amount()}}</td>
            <td style="text-align: right;">{{$vendor->service_percent()}} %</td>
            <td>{{$vendor->profit_amount()}}</td>
            <td>{{$vendor->difference_amount()}}</td>
            <td>{{$vendor->revenue_amount()}}</td>
            @php
                $tax = $vendor->rep_type==='개인'? floor(($vendor->revenue/100*3)/10)*10 : 0;
                $tax2 = $vendor->rep_type==='개인'? floor(($tax/10)/10)*10 : 0;
            @endphp
            <td>{{$tax}}</td>
            <td>{{$tax2}}</td>
            <td>{{$tax + $tax2}}</td>
            <td>{{$vendor->service_fee}}</td>
            <!-- 분양몰 관리비 -->
            <td>0</td>
            <td>{{$vendor->service_fee}}</td>
            <td>{{$vendor->card_amount()}}</td>
            <td>{{$vendor->outstanding_amount()}}</td>
            <td>{{$vendor->etc1_amount()}}</td>
            <td>{{$vendor->etc2_amount()}}</td>
            <td>{{$vendor->etc3_amount()}}</td>
            <td style="color: red; font-weight: bold">{{$vendor->settlement_amount}}</td>
            <td style="text-align: center">{{!is_null($vendor->sp_order_cnt) ? "발급" : "미발급"}}</td>
            <td>{{!is_null($vendor->deposit_date) ? $vendor->deposit_date : ""}}</td>
            <td></td>
        </tr>
    @endforeach
    </tbody>
</table>
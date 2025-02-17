<table>
    <thead>
        <tr>
            <th>순서</th>
            <th>화원명</th>
            <th>대표자</th>
            <th>대표번호</th>
            <th>지급액</th>
            <th>지급명세서</th>
            <th>이메일</th>
            <th>연락처</th>
        </tr>
    </thead>
    <tbody>
    @foreach($vendors as $vendor)
        <tr>
            <td style="text-align: center">{{$loop->index + 1}}</td>
            <td>{{$vendor->mall_name}}</td>
            <td>{{$vendor->rep_name}}</td>
            <td style="text-align: center">{{$vendor->gen_number}}</td>
            <td>{{$vendor->sp_settlement_amount}}</td>
            <td>{{$vendor->specification_url}}</td>
            <td>{{$vendor->rep_email}}</td>
            <td style="text-align: center">{{$vendor->rep_tel}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
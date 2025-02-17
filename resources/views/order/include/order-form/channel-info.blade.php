@php $vendor = $vendors[1] @endphp

@if($vendor)
<div class="card">
    <div class="card-body">
        <h3>{{$vendor->mall_name}}</h3>
        <p>
            <span style="display: inline-block; width: 200px">대표자명</span>
            <span>{{$vendor -> rep_name}}</span>
        </p>
        <p>
            <span style="display: inline-block; width: 200px">대표번호</span>
            <span>{{$vendor -> gen_number}}</span>
        </p>
        <p>
            <span style="display: inline-block; width: 200px">보증만료일</span>
            <span>{{$vendor -> assurance_ex_date}}</span>
        </p>
        <p>
            <span style="display: inline-block; width: 200px">보증금액</span>
            <span>{{number_format($vendor -> assurance_amount)}}원</span>
        </p>
        <p>
            <span style="display: inline-block; width: 200px">미수가능금액</span>
            <span>{{number_format($vendor -> possible_misu())}}원</span>
        </p>
    </div>
</div>
@endif
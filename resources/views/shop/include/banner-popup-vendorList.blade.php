    <option value="basic"
            data-mall="전체몰"
            data-name="모두"
            data-domain="모든 도메인"
    >
        <span class="vendor_mall">전체몰</span>
        <span class="vendor_name">모두</span>
        <span class="vendor_domain">모든 도메인</span>
    </option>
@if($vendors)
    @foreach($vendors as $vendor)
    <option value="{{$vendor->domain}}"
            data-mall="{{$vendor->mall_name}}"
            data-name="{{$vendor->name}}"
            data-domain="{{$vendor->domain}}"
    >
        <span class="vendor_mall">{{$vendor->mall_name}}</span>
        <span class="vendor_name">{{$vendor->name}}</span>
        <span class="vendor_domain">{{$vendor->domain}}</span>
    </option>
    @endforeach
@endif
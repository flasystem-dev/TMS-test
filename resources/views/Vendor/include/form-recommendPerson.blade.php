<option value=""></option>
@foreach($vendors as $vendor)
    <option value="{{$vendor->idx}}" data-mall="{{$vendor->mall_name}}"
            data-name="{{$vendor->rep_name}}" data-partner="{{$vendor->partner_name}}">
        <span class="shop_name">{{$vendor->mall_name}}</span>
        <span class="rep_name">{{$vendor->rep_name}}</span>
        <span class="partner_name">{{$vendor->partner_name}}</span>
    </option>
@endforeach

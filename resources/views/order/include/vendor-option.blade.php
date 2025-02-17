@if(!$vendors->isEmpty())
    <option></option>
    @foreach($vendors as $vendor)
        <option value="{{$vendor->idx}}"
                data-did="{{$vendor->did_number}}"
                data-mall="{{$vendor->mall_name}}"
                data-number="{{$vendor->gen_number}}"
                data-name="{{$vendor->rep_name}}">
            <span class="did_number">{{$vendor->did_number}}</span>
            <span class="shop_name">{{$vendor->mall_name}}</span>
            <span class="gen_number">{{$vendor->gen_number}}</span>
            <span class="rep_name">{{$vendor->rep_name}}</span>
        </option>
    @endforeach
@endif

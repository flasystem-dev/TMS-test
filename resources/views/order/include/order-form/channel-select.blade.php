@if($vendors->isNotEmpty())
<div class="card">
    <div class="card-body py-2">
        <div class="mb-1 row">
            <h5 class="card-title m-2">채널 선택</h5>
        </div>

        <div class="mb-2 row">
            <div class="col-7">
                <select class="form-select" name="mall_code" id="channel-vendor">
                    <option></option>
                    @foreach($vendors as $vendor)
                        <option value="{{$vendor->idx}}" data-did="{{$vendor->did_number}}" data-mall="{{$vendor->mall_name}}"
                                data-number="{{$vendor->gen_number}}" data-name="{{$vendor->rep_name}}" data-phone="{{$vendor->rep_tel}}"
                                data-valid="{{$vendor->is_valid}}" data-type="{{$vendor->price_type}}" data-type-Name="{{priceTypeName($vendor->price_type)}}" data-credit="{{$vendor->is_credit}}">
                            <span class="did_number">{{$vendor->did_number}}</span>
                            <span class="shop_name">{{$vendor->mall_name}}</span>
                            <span class="gen_number">{{$vendor->gen_number}}</span>
                            <span class="rep_name">{{$vendor->rep_name}}</span>
                            <span class="is_valid">{{$vendor->is_valid}}</span>
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-5">
                <select class="form-select" name="orderer_mall_id" id="orderer_mall_id" aria-label="orderer_mall_id">

                </select>
            </div>
        </div>
        <div class="row">
            <p class="m-0" id="remainCredit"></p>
        </div>
    </div>
</div>
@endif
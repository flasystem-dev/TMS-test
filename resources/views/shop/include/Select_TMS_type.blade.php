<div class="row">
    <div class="col-4">
        @php
            $brands = DB::table('playauto2_api') -> select('brand_type_code') -> groupBy('brand_type_code') -> get();
        @endphp

        <select class="form-select" name="brand_type" aria-label="brand_type">
            @foreach($brands as $brand)
                @php
                    $code_name = DB::table('common_code') -> select('code_name') -> where('code', $brand -> brand_type_code ) -> first();
                @endphp
                <option value="{{ $brand -> brand_type_code }}">{{ $code_name -> code_name }}</option>
            @endforeach

        </select>
    </div>
    <div class="col-4">
        <select class="form-select" name="mall_type" aria-label="mall_type">
            <option value=""></option>
        </select>
    </div>
</div>
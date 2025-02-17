@if(isset($orderProduct) && $orderProduct)
    <tr>
        <td class='text-center product_table_name fw-bold' id="product-name" data-product-type="{{$orderProduct->product_type}}">{{$orderProduct->product_name}}
            @if(!empty($orderProduct -> options))
            <div id="option_area">
                @foreach($orderProduct->options as $option)
                    <p class="product_table_option m-0 fw-normal">{{$option['option_name']}} <span>( +{{number_format($option['option_price'])}})</span></p>
                @endforeach
            </div>
            @endif
        </td>
        <td class='text-center'>1</td>
        <td class='text-end' id="product_price" data-price="{{$orderProduct->product_price}}">{{number_format($orderProduct->product_price)}}</td>
        <td class='text-end' id="options_amount" data-price="{{$orderProduct->options_amount}}">{{number_format($orderProduct->options_amount) }}</td>
        <td class='text-end' id="item_total_amount" data-price="{{$orderProduct->item_total_amount}}">{{number_format($orderProduct->item_total_amount)}}</td>
        <input type="hidden" name="orderProduct_json" id="orderProduct_json" value="{{ json_encode($orderProduct, JSON_UNESCAPED_UNICODE) }}">
    </tr>
@else
    <tr>
        <td colspan="6">
            <h4 class="text-center">등록 된 상품이 없습니다.</h4>
        </td>
    </tr>
@endif
@if(isset($products) && $products->isNotEmpty())
    @foreach($products as $product)
        <div class="row rounded-3 mx-2 mb-3 product_select_table" style="background-color: #f5f6f8">
            <form class="p-0">
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <div class="product_main">
                    <table class="table m-0">
                        <tr>
                            <td rowspan="2" class="product_img">
                                <img src="{{$product -> thumbnail}}" width="100" height="100">
                            </td>
                            <th class="product_name">
                                {{ $product-> name}}
                            </th>
                        </tr>
                        <tr>
                            <td class="product_name text-center">
                                <span class="form-check-label fw-bold">
                                    <input type="number" class="form-control product-price" name="product_price" value="{{ $product -> prices[0]->product_price }}"> 원</span>
                                <button type="button" class="btn btn-outline-secondary add-option-btn">+ 옵션</button>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="product_option">
                    <table class="table m-0">
                        <template class="custom-option-template">
                            <tr>
                                <td class="px-0 pt-2 pb-0">
                                    <input type="text" class="form-control custom-option-name" name="option_name[]" value="" placeholder="옵션명">
                                    <input type="number" class="form-control custom-option-price" name="option_price[]"  value="0">
                                    <button type="button" class="btn btn-danger custom-option-delete">X</button>
                                </td>
                            </tr>
                        </template>
                        <tbody class="product-option-table">
                            @if($product -> options->isNotEmpty())
                            @foreach($product->groupedOptions() as $options)
                                <tr>
                                    <td class="px-0 pt-2 pb-0">
                                        <select class="form-select product-option-select" name="select_options[]" aria-label="Default select">
                                            @foreach($options as $option)
                                            <option value="{{$option['id']}}">[{{optionTypeName($option['option_type_id'])}}] {{$option['name']}} (+{{number_format($option['option_price'])}}원)</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                            @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
                <div class="product_btn">
                    <table class="table m-0">
                        <tr><td class="text-center" style="vertical-align: middle; width: 80px"><button type="button" class="btn btn-outline-warning" onclick="update_product('{{$product->id}}');">수정</button></td></tr>
                        <tr><td class="text-center"><button type="button" class="btn btn-outline-primary add-product-btn">선택</button></td></tr>
                    </table>
                </div>
            </form>
        </div>
    @endforeach
@else
    <div class="row">
        <div class="offset-md-5 col-md-7">
            <span>검색 결과가 없습니다.</span>
        </div>
    </div>
@endif

<script>
    function update_product(idx) {
        if(confirm("상품 수정 페이지를 팝업하시겠습니까?\n(수정 후에는 다시 검색해주세요.)")){
            url = main_url + "/shop/product/" + idx
            open_win(url,'상품정보', 1500, 850, 50, 50);
        }
    }

    $('.add-option-btn').on('click', function(){
        let template = $('.custom-option-template').html();
        let table = $(this).closest('.product_select_table');
        table.find('.product-option-table').append(template)
    });

    $('.product_option').on('click', '.custom-option-delete', function(event){
        event.preventDefault();
        event.stopPropagation();
        $(this).closest('tr').remove();
    });
</script>
@extends('layouts.master')
@section('title')
    상품 리스트
@endsection
@section('content')
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/assets/css/shop/shop-product.css') }}" rel="stylesheet">
    @include('shop.product.modal.index-modals')
    <div class="col-12">
        <div class="card">
            <div class="card-body pb-1">
                <form method="get">
                    <div class="row">
                        <div class="col-4 py-1">
                            <div class="input-group">
                                <a class="input-group-text" href="{{ url('Shop/Product') }}">브랜드</a>
                                    @foreach($brands as $brand)
                                        <input type="checkbox" class="btn-check" name="brand[]" id="brand_{{ $brand -> brand_type_code }}" value="{{ $brand -> brand_type_code }}" autocomplete="off" @if(!empty(request()->brand) && in_array($brand -> brand_type_code , request()->brand)) checked @endif>
                                        <label class="btn btn-outline-primary" for="brand_{{ $brand -> brand_type_code }}">{{ $brand -> brand_ini }}</label>
                                    @endforeach
                            </div>
                        </div>

                        <div class="col-2 py-1">
                            <div class="input-group">
                                <span class="input-group-text">타입1</span>
                                <select class="form-select" name="category1" id="category1" aria-label="category1">
                                    <option value="">- 전체 -</option>
                                    <option value="A" {{ request()->category1 === 'A' ? 'selected' : '' }}>종류별</option>
                                    <option value="B" {{ request()->category1 === 'B' ? 'selected' : '' }}>테마별</option>
                                    <option value="C" {{ request()->category1 === 'C' ? 'selected' : '' }}>이벤트별</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-2 py-1">
                            <div class="input-group">
                                <span class="input-group-text">타입2</span>
                                <select class="form-select" name="category2" id="category2" aria-label="category2">
                                    {!! $ctgy2_options !!}
                                </select>
                            </div>
                        </div>

                        <div class="col-2 py-1">
                            <div class="input-group">
                                <span class="input-group-text" style="cursor: pointer" data-bs-toggle="dropdown" aria-expanded="false">상품명</span>
                                <input type="text" class="form-control" name="name" aria-label="name" value="{{ request()->name }}">
                            </div>
                        </div>
                        <div class="col-2 py-1 text-end">
                            <div class="row mb-2" style="display: flex;">
                                <div style="flex-basis: 50%">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_not_use" value="1" role="switch" id="is_not_use" {{ request()->is_not_view ? "checked":"" }}>
                                        <label class="form-check-label" for="is_not_use">미사용 포함</label>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_not_view" value="1" role="switch" id="is_not_view" {{ request()->is_not_view ? "checked":"" }}>
                                        <label class="form-check-label" for="is_not_view">숨김 포함</label>
                                    </div>
                                </div>
                                <div style="flex-basis: 50%">
                                    <button style="border-radius:3px;" class="btn btn-secondary waves-effect waves-light me-2">검색하기</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div><!-- end card-body -->
        </div><!-- end card -->

        <div class="col-12">
            <div class="card">
                <div class="row">
                    <div class="col-12 px-4 py-3">
                        <table id="product_table" class="table table-striped table-bordered " style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr class="px-0">
                                    <th style="width: 3%"></th>
                                    <th style="width: 7%">상품코드</th>
                                    <th style="width: 5%">브랜드</th>
                                    <th>상품명</th>
                                    <th style="width: 12%">기본가</th>
                                    <th style="width: 6%">사용</th>
                                    <th style="width: 5%">보이기</th>
                                </tr>
                            </thead>
                            <tbody>
                            @if($products->isNotEmpty())
                            @foreach($products as $product)
                                <tr class="text-center align-middle px-0">
                                    <td class="px-0">
                                        <input type="checkbox" aria-label="products" data-id="{{$product->id}}">
                                    </td>
                                    <!-- 상품코드 -->
                                    <td>
                                        <span onclick="product_edit('{{ $product -> code }}');" style="cursor: pointer">{{ $product -> code }}</span>
                                    </td>
                                    <!-- 브랜드 -->
                                    <td>
                                        <p class="brand_type {{ $product -> brand }}">{{ BrandAbbr($product -> brand) }}</p>
                                    </td>
                                    <!-- 상품명 -->
                                    <td class="text-start">
                                        <img src="{{ $product -> thumbnail }}" width="40" height="40" data-bs-toggle="popover" data-bs-html="true" data-bs-trigger="hover focus"
                                             data-bs-placement="right" data-bs-content="<img src='{{ $product -> thumbnail }}' alt='상품사진' width='200px' height='200px'>" onclick="photo_popup('{{ $product -> thumbnail }}');">
                                        <span class="ms-2" onclick="product_form('{{ $product -> id }}');" style="cursor: pointer">{{ $product -> name }}</span>
                                    </td>
                                    <!-- 기본가 / 발주가 -->
                                    <td>
                                        <p>{{ number_format(optional($product -> prices[0]) -> product_price ?? 0) }}</p>
                                    </td>
                                    <!-- 사용 -->
                                    <td class="fs-3 p-0" data-order="{{ $product -> is_used ? 1: 0 }}">
                                        <input class="checkbox_toggle product-change-state" type="checkbox" value="1" id="product_is_used{{$loop->index}}" data-column="is_used" data-index="{{$product->id}}" {{ $product->is_used ? "checked" : "" }}>
                                        <label class="checkbox_toggle_label" for="product_is_used{{$loop->index}}"></label>
                                    </td>
                                    <!-- 삭제 -->
                                    <td class="p-0" data-order="{{ $product -> is_view ? 1: 0 }}">
                                        <input class="checkbox_toggle product-change-state" type="checkbox" value="1" id="product_is_view{{$loop->index}}" data-column="is_view" data-index="{{$product->id}}" {{ $product->is_view ? "checked" : "" }}>
                                        <label class="checkbox_toggle_label" for="product_is_view{{$loop->index}}"></label>
                                    </td>
                                </tr>
                            @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- end col -->
    <button class="fixed_btn" data-bs-toggle="modal" data-bs-target="#add_product_modal"><i class="uil-box fixed_btn_icon"></i><span class="fixed_btn_text ms-1">+ 상품 추가</span></button>
@endsection
@section('script')
    <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/product/index.js') }}"></script>
@endsection
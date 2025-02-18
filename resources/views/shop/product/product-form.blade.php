@extends('layouts.master-without-nav')
@section('title')
    상품 정보
@endsection
@section('content')
    <link href="{{ asset('/assets/css/product/product-form.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('/assets/libs/summernote/summernote-lite.min.css') }}" rel="stylesheet" type="text/css" />
    <script src="{{ URL::asset('/assets/libs/summernote/summernote-lite.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/summernote/lang/summernote-ko-KR2.js') }}"></script>
    @include('shop.product.modal.product-form-modal')
    @if(session('update'))
        <script>
            showToast('수정 완료');
        </script>
    @endif
@php
    use Illuminate\Support\Str;
@endphp
    <div class="row">
        <div class="col-12 p-3">
            <form id="pr_form" action="{{ url('/shop/product') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-8">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-10">
                                        <h4 class="card-title mb-3">상품 정보</h4>
                                        <input type="hidden" name="id" id="product-id" value="{{ optional($product) -> id ?? "0" }}">
                                    </div>
                                    <div class="col-2">
                                        <div class="form-check form-switch fs-5">
                                            <label for="is_used" class="form-check-label">사용</label>
                                            <input class="form-check-input" name="is_used" id="is_used" type="checkbox" role="switch" value="1" aria-label="is_used" {{optional($product) -> is_used ? "checked" : ""}}>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="input-group mb-3">
                                            <label class="input-group-text" for="pr_brand">브랜드</label>
                                            <select class="form-select" name="brand">
                                                <option value="BTCP" {{optional($product) -> brand === 'BTCP'? "selected" : ""}}>꽃파는총각</option>
                                                <option value="BTCC" {{optional($product) -> brand === 'BTCC'? "selected" : ""}}>칙칙폭폭플라워</option>
                                                <option value="BTSP" {{optional($product) -> brand === 'BTSP'? "selected" : ""}}>사팔플라워</option>
                                                <option value="BTBR" {{optional($product) -> brand === 'BTBR'? "selected" : ""}}>바로플라워</option>
                                                <option value="BTOM" {{optional($product) -> brand === 'BTOM'? "selected" : ""}}>오만플라워</option>
                                                <option value="BTCS" {{optional($product) -> brand === 'BTCS'? "selected" : ""}}>꽃파는사람들</option>
                                                <option value="BTFC" {{optional($product) -> brand === 'BTFC'? "selected" : ""}}>플라체인</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <div class="input-group">
                                            <span class="input-group-text">상품코드</span>
                                            <input type="text" class="form-control" name="code" id="product-code" value="{{ optional($product) -> code ?? "" }}" {{ empty(optional($product) -> code) ? "" : "readonly" }}>
                                        </div>
                                        <div id="error">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-6">
                                        <div class="input-group mb-3">
                                            <label class="input-group-text" for="pr_name">상품명</label>
                                            <input type="text" class="form-control" name="name" id="pr_name" value="{{ optional($product) -> name ?? "" }}" required>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text" >배송 방법</span>
                                            <input type="radio" class="btn-check" name="delivery_type" id="DTDD" value="DTDD" checked>
                                            <label class="btn btn-outline-primary form-control" for="DTDD">직접배송</label>
                                            <input type="radio" class="btn-check" name="delivery_type" id="DTPD" value="DTPD">
                                            <label class="btn btn-outline-primary form-control" for="DTPD">택배배송</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <div class="input-group d-flex mb-3">
                                            <span class="input-group-text">종류별</span>
                                            @foreach($ctgy1 as $ct)
                                                <input type="radio" class="btn-check" name="ctgyA" id="{{ $ct -> ct1.$ct -> ct2 }}" value="{{ $ct -> ct1.$ct -> ct2 }}" {{optional($product) -> ctgyA === $ct -> ct1.$ct -> ct2? "checked" : "" }}>
                                                <label class="btn btn-outline-secondary flex-fill" for="{{ $ct -> ct1.$ct -> ct2 }}">{{ $ct -> ct_name }}</label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="input-group d-flex mb-3">
                                            <span class="input-group-text">상황별</span>
                                            @foreach($ctgy2 as $ct)
                                                <input type="checkbox" class="btn-check" name="ctgyB[]" id="{{ $ct -> ct1.$ct -> ct2 }}" value="{{ $ct -> ct1.$ct -> ct2 }}"
                                                    @if(Str::contains(optional($product) -> ctgyB ?? "" , $ct -> ct1.$ct -> ct2 )) checked @endif>
                                                <label class="btn btn-outline-primary flex-fill" for="{{ $ct -> ct1.$ct -> ct2 }}">{{ $ct -> ct_name }}</label>
                                            @endforeach

                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="input-group d-flex mb-3">
                                            <span class="input-group-text">이벤트별</span>
                                            @foreach($ctgy3 as $ct)
                                                <input type="checkbox" class="btn-check" name="ctgyC[]" id="{{ $ct -> ct1.$ct -> ct2 }}" value="{{ $ct -> ct1.$ct -> ct2 }}"
                                                       @if(Str::contains(optional($product) -> ctgyC ?? "" , $ct -> ct1.$ct -> ct2 )) checked @endif>
                                                <label class="btn btn-outline-warning flex-fill" for="{{ $ct -> ct1.$ct -> ct2 }}">{{ $ct -> ct_name }}</label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <div class="input-group d-flex mb-3">
                                            <span class="input-group-text">타입별</span>
                                            <input type="radio" class="btn-check" name="type" id="pr_type_CH" value="CH" {{optional($product)->type==="CH" ? "checked":""}}>
                                            <label class="btn btn-outline-info flex-fill" for="pr_type_CH">축하화환</label>

                                            <input type="radio" class="btn-check" name="type" id="pr_type_CO" value="CO" {{optional($product)->type==="CO" ? "checked":""}}>
                                            <label class="btn btn-outline-info flex-fill" for="pr_type_CO">축하오브제</label>

                                            <input type="radio" class="btn-check" name="type" id="pr_type_CS" value="CS" {{optional($product)->type==="CS" ? "checked":""}}>
                                            <label class="btn btn-outline-info flex-fill" for="pr_type_CS">축하쌀화환</label>

                                            <input type="radio" class="btn-check" name="type" id="pr_type_GH" value="GH" {{optional($product)->type==="GH" ? "checked":""}}>
                                            <label class="btn btn-outline-info flex-fill" for="pr_type_GH">근조화환</label>

                                            <input type="radio" class="btn-check" name="type" id="pr_type_GO" value="GO" {{optional($product)->type==="GO" ? "checked":""}}>
                                            <label class="btn btn-outline-info flex-fill" for="pr_type_GO">근조오브제</label>

                                            <input type="radio" class="btn-check" name="type" id="pr_type_BG" value="BG" {{optional($product)->type==="BG" ? "checked":""}}>
                                            <label class="btn btn-outline-info flex-fill" for="pr_type_BG">근조바구니</label>

                                            <input type="radio" class="btn-check" name="type" id="pr_type_GS" value="GS" {{optional($product)->type==="GS" ? "checked":""}}>
                                            <label class="btn btn-outline-info flex-fill" for="pr_type_GS">근조쌀화환</label>

                                            <input type="radio" class="btn-check" name="type" id="pr_type_ET" value="ET" {{optional($product)->type==="ET" ? "checked":""}}>
                                            <label class="btn btn-outline-info flex-fill" for="pr_type_ET">기타</label>
                                            <td colspan="3"><p>&#8251 해당 타입은 추가배송비를 위한 타입구분입니다.</p></td>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <h4 class="card-title mb-3">상품 가격 <button type="button" class="btn btn-secondary btn-sm price-add-btn" onclick="add_product_price()">가격 추가</button></h4>

                                    <div class="product-price-area mb-2">
                                        <div class="input-group">
                                            <input type="text" class="form-control title-input price-type-title" value="가격 타입">
                                            <input type="text" class="form-control title-input" value="상품 금액">
                                            <input type="text" class="form-control title-input" value="화원사 금액">
                                            <input type="text" class="form-control title-input" value="사업자 금액">
                                            <button type="button" class="btn btn-secondary" disabled>-</button>
                                        </div>
                                    </div>

                                    <template id="product-price">
                                        <div class="product-price-area">
                                            <div class="input-group">
                                                <select class="form-select price_type_select" name="price_type[]">
                                                    @foreach($price_types as $type)
                                                        <option value="{{$type->id}}">{{$type->name}}</option>
                                                    @endforeach
                                                </select>
                                                <input type="number" class="form-control text-end" name="product_price[]" value="0">
                                                <input type="number" class="form-control text-end" name="balju_price[]" value="0">
                                                <input type="number" class="form-control text-end" name="vendor_price[]" value="0">
                                                <button type="button" class="btn btn-outline-danger remove-product-price">X</button>
                                            </div>
                                        </div>
                                    </template>

                                    <div id="product-price-container">
                                        @if($product && !empty($product->prices))
                                        @foreach($product->prices as $price)
                                            <div class="product-price-area">
                                                <div class="input-group">
                                                    <select class="form-select price_type_select" name="price_type[]">
                                                        @foreach($price_types as $type)
                                                            <option value="{{$type->id}}" {{$price->price_type_id === $type->id ? "selected" : ""}}>{{$type->name}}</option>
                                                        @endforeach
                                                    </select>
                                                    <input type="number" class="form-control text-end" name="product_price[]" value="{{ optional($price) -> product_price ?? 0 }}">
                                                    <input type="number" class="form-control text-end" name="balju_price[]" value="{{ optional($price) -> balju_price ?? 0 }}">
                                                    <input type="number" class="form-control text-end" name="vendor_price[]" value="{{ optional($price) -> vendor_price ?? 0 }}">
                                                    <button type="button" class="btn btn-outline-danger remove-product-price">X</button>
                                                </div>
                                            </div>
                                        @endforeach
                                        @else
                                            <div class="product-price-area">
                                                <div class="input-group">
                                                    <select class="form-select price_type_select" name="price_type[]">
                                                        @foreach($price_types as $type)
                                                            <option value="{{$type->id}}">{{$type->name}}</option>
                                                        @endforeach
                                                    </select>
                                                    <input type="number" class="form-control text-end" name="product_price[]" value="0">
                                                    <input type="number" class="form-control text-end" name="balju_price[]" value="0">
                                                    <input type="number" class="form-control text-end" name="vendor_price[]" value="0">
                                                    <button type="button" class="btn btn-outline-danger remove-product-price">X</button>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <h4 class="card-title mb-3">옵션</h4>
                                <div class="border p-3 rounded" style="background-color: #fafcff">
                                    <div id="product-options-container">
                                        <div class="row">
                                            <div class="col-3">
                                                <div class="input-group">
                                                    <input class="form-control title-input" value="옵션 타입">
                                                </div>
                                            </div>
                                            <div class="col-9 option_values_area">
                                                <div class="row option-values">
                                                    <div class="col-10">
                                                        <div class="input-group">
                                                            <input type="text" class="form-control title-input" value="옵션 명" >
                                                            <input type="text" class="form-control option-prices title-input" value="옵션 금액" >
                                                            <input type="text" class="form-control option-prices title-input" value="화원사 옵션금액" >
                                                            <input type="text" class="form-control option-prices title-input" value="사업자 옵션금액">
                                                        </div>
                                                    </div>
                                                    <div class="col-2">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <template id="product-option">
                                            <div class="row">
                                                <div class="col-3">
                                                    <div class="input-group mb-2">
                                                        <span class="input-group-text">타입</span>
                                                        <select class="form-select" name="option_type_id[]">
                                                            @foreach($option_types as $option_type)
                                                                <option value="{{$option_type->id}}" >{{$option_type->name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-9 option_values_area">
                                                    <div class="row option-values">
                                                        <div class="col-10">
                                                            <div class="input-group mb-2">
                                                                <input type="text" class="form-control" name="option_name[]" value="">
                                                                <input type="number" class="form-control option-prices" name="option_price[]" value="">
                                                                <input type="number" class="form-control option-prices" name="balju_option_price[]" value="">
                                                                <input type="number" class="form-control option-prices" name="vendor_option_price[]" value="">
                                                            </div>
                                                        </div>
                                                        <div class="col-2">
                                                            <button type="button" class="btn btn-outline-danger remove-product-option">삭제</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    @if($product && !empty($product->options))
                                    @foreach($product -> options as $option)
                                        <div class="row">
                                            <div class="col-3">
                                                <div class="input-group mb-2">
                                                    <span class="input-group-text">타입</span>
                                                    <select class="form-select" name="option_type[]">
                                                        @foreach($option_types as $option_type)
                                                            <option value="{{$option_type->id}}" {{ $option->option_type_id === $option_type->id ? "selected" : "" }}>{{$option_type->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-9 option_values_area">
                                                <div class="row option-values">
                                                    <div class="col-10">
                                                        <div class="input-group mb-2">
                                                            <input type="text" class="form-control" name="option_name[]" value="{{$option->name}}">
                                                            <input type="number" class="form-control option-prices" name="option_price[]" value="{{$option->option_price}}">
                                                            <input type="number" class="form-control option-prices" name="balju_option_price[]" value="{{$option->balju_option_price}}">
                                                            <input type="number" class="form-control option-prices" name="vendor_option_price[]" value="{{$option->vendor_option_price}}">
                                                        </div>
                                                    </div>
                                                    <div class="col-2">
                                                        <button type="button" class="btn btn-outline-danger remove-product-option">삭제</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    @endif
                                    </div>
                                    <div class="row">
                                        <div class="offset-4 col-4 text-center">
                                            <button type="button" class="btn btn-outline-primary w-100" id="add-product-option">옵션 추가</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <h4 class="card-title mb-3">기타 설정</h4>
                                    <div class="col-6">
                                        <div class="input-group mb-3">
                                            <div class="form-check form-switch me-4">
                                                <label for="is_popular" class="form-check-label">인기상품(메인페이지 베스트셀러 노출)</label>
                                                <input class="form-check-input" name="is_popular" id="is_popular" value="1" type="checkbox" role="switch" {{optional($product) -> is_popular ? "checked" : "" }} aria-label="pr_popular">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-check form-switch me-4">
                                            <label for="is_discount" class="form-check-label">세일상품(세일상품 표시)</label>
                                            <input class="form-check-input" name="is_discount" id="is_discount" value="Y" type="checkbox" role="switch" {{optional($product) -> is_discount ? "checked" : "" }} aria-label="pr_discount">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6 d-grid">
                                        <button type="button" class="btn btn-success btn-lg" onclick="pr_submit();">{{isset($product) ? "상품 수정" : "상품 등록" }}</button>
                                    </div>
                                    @if(isset($product))
                                    <div class="col-6 d-grid">
                                        <button type="button" class="btn btn-danger btn-lg" onclick="remove_product('{{ optional($product) -> id }}');">상품 삭제</button>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="row mb-2">
                                            <div class="col-9">
                                                <h4 class="card-title">상품 대표 사진</h4>
                                            </div>
                                            <div class="col-3 text-end d-none" id="cancel_change">
                                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="reset_img_src('{{ optional($product) -> pr_img ?? "" }}');">취소</button>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-12">
                                                <img src="{{ optional($product) -> img ?? "" }}" id="img_box" width="400" height="400" onclick="change_img();" style="cursor: pointer">
                                                <input type="file" name="img" class="d-none" id="product-img" accept="image/*">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="row">
                                            <div class="col-8">
                                                <h4 class="card-title mb-3 mt-3">상품 메모</h4>
                                            </div>
                                            @isset($product)
                                            <div class="col-4 text-end">
                                                <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#search_word_modal">검색어 관리</button>
                                            </div>
                                            @endisset
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <textarea class="form-control" name="memo" aria-label="pr_memo">{{ optional($product) -> pr_memo ?? "" }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-8">
                                        <h4 class="my-3 fw-bold">상품 상세 정보</h4>
                                    </div>
                                    <div class="col-4 my-3 text-end">
                                        <button type="button" class="btn btn-outline-danger me-3" onclick="reset_note();">초기화</button>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div id="description_area"></div>
                                        <textarea class="d-none" name="description"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>

@endsection
@section('script')
    <script>
        $(document).ready(function() {
            $('#description_area').summernote({
                height: 500,
                lang: 'ko-KR',
                callbacks: {
                    onImageUpload : function(files) {
                        uploadImage(files[0], this);
                    }
                }
            });
            @if(!empty(optional($product) -> description))
            $('#description_area').summernote('code', '{!! optional($product) -> description ?? "" !!}');
            @endif

            @if(session('update-search-word'))
                var search_word_modal = new bootstrap.Modal(document.getElementById('search_word_modal'));
                search_word_modal.show();
            @endif
        });

        // 상품 사진 변경
        const product_img = document.querySelector('input[name="img"]');
        product_img.addEventListener('change', () => {
            if(product_img.files[0]) {
                document.querySelector('#img_box').src = URL.createObjectURL(product_img.files[0]);
                $('#cancel_change').removeClass('d-none');
            } else {
                document.querySelector('#img_box').src = '{{ optional($product) -> img ?? "" }}';
                $('#cancel_change').addClass('d-none');
            }
        });

        // 상품 상세 리셋
        function reset_note() {
            $('#description_area').summernote('reset');
            @if(!empty(optional($product) -> description))
            $('#description_area').summernote('code', '{!! optional($product) -> description ?? "" !!} ');
            @endif
        }
    </script>
    <script src="{{ URL::asset('/assets/js/product/product-form.js') }}"></script>
@endsection
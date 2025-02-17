@extends('layouts.master-without-nav')
@section('css')
{{--    <link href="{{ URL::asset('/assets/libs/cropper/cropper.min.css') }}" rel="stylesheet" />--}}
@endsection
@section('title')
    상품 정보
@endsection
@section('content')
    <link href="{{ asset('/assets/css/shop/shop-product-edit.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('/assets/libs/summernote/summernote-lite.min.css') }}" rel="stylesheet" type="text/css" />
    <script src="{{ URL::asset('/assets/libs/summernote/summernote-lite.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/summernote/lang/summernote-ko-KR2.js') }}"></script>
    @include('Shop.Product.modal.Product-edit-modal')

    <div class="row">
        <div class="col-12 p-3">
            <form id="pr_form" action="{{ url('/Shop/Product/Edit') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-8">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-10">
                                        <h4 class="card-title mb-3">상품 정보</h4>
                                        <input type="hidden" name="idx" value="{{ optional($product) -> idx ?? "0" }}">
                                    </div>
                                    <div class="col-2">
                                        <div class="form-check form-switch fs-5">
                                            <label for="is_used" class="form-check-label">사용</label>
                                            <input class="form-check-input" name="is_used" id="is_used" type="checkbox" role="switch" value="Y" aria-label="is_used" {{optional($product) -> is_used == 'Y' ? "checked" : ""}}>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="input-group mb-3">
                                            <label class="input-group-text" for="pr_brand">브랜드</label>
                                            <select class="form-select" name="pr_brand" id="pr_brand">
                                                <option value="BTCP" {{optional($product) -> pr_brand === 'BTCP'? "selected" : ""}}>꽃파는총각</option>
                                                <option value="BTCC" {{optional($product) -> pr_brand === 'BTCC'? "selected" : ""}}>칙칙폭폭플라워</option>
                                                <option value="BTSP" {{optional($product) -> pr_brand === 'BTSP'? "selected" : ""}}>사팔플라워</option>
                                                <option value="BTBR" {{optional($product) -> pr_brand === 'BTBR'? "selected" : ""}}>바로플라워</option>
                                                <option value="BTOM" {{optional($product) -> pr_brand === 'BTOM'? "selected" : ""}}>오만플라워</option>
                                                <option value="BTCS" {{optional($product) -> pr_brand === 'BTCS'? "selected" : ""}}>꽃파는사람들</option>
                                                <option value="BTFC" {{optional($product) -> pr_brand === 'BTFC'? "selected" : ""}}>플라체인</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <div class="input-group">
                                            <label class="input-group-text" for="pr_id">상품코드</label>
                                            <input type="text" class="form-control" name="pr_id" id="pr_id" value="{{ optional($product) -> pr_id ?? "" }}" {{ empty(optional($product) -> pr_id) ? "" : "readonly" }}>
                                        </div>
                                        <div id="error">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-6">
                                        <div class="input-group mb-3">
                                            <label class="input-group-text" for="pr_name">상품명</label>
                                            <input type="text" class="form-control" name="pr_name" id="pr_name" value="{{ optional($product) -> pr_name ?? "" }}" required>
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
                                                <input type="radio" class="btn-check" name="pr_ctgy1" id="{{ $ct -> ct1.$ct -> ct2 }}" value="{{ $ct -> ct1.$ct -> ct2 }}" {{optional($product) -> pr_ctgy1 == $ct -> ct1.$ct -> ct2? "checked" : "" }}>
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

                                                <input type="checkbox" class="btn-check" name="pr_ctgy2[]" id="{{ $ct -> ct1.$ct -> ct2 }}" value="{{ $ct -> ct1.$ct -> ct2 }}"
                                                        @if(Illuminate\Support\Str::contains(optional($product) -> pr_ctgy2 ?? "" , $ct -> ct1.$ct -> ct2 )) checked @endif>
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
                                                <input type="checkbox" class="btn-check" name="pr_ctgy3[]" id="{{ $ct -> ct1.$ct -> ct2 }}" value="{{ $ct -> ct1.$ct -> ct2 }}"
                                                       @if(Illuminate\Support\Str::contains(optional($product) -> pr_ctgy3 ?? "" , $ct -> ct1.$ct -> ct2 )) checked @endif>
                                                <label class="btn btn-outline-warning flex-fill" for="{{ $ct -> ct1.$ct -> ct2 }}">{{ $ct -> ct_name }}</label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <div class="input-group d-flex mb-3">
                                            <span class="input-group-text">타입별</span>
                                            <input type="radio" class="btn-check" name="pr_type" id="pr_type_CH" value="CH" {{optional($product)->pr_type==="CH" ? "checked":""}}>
                                            <label class="btn btn-outline-info flex-fill" for="pr_type_CH">축하화환</label>

                                            <input type="radio" class="btn-check" name="pr_type" id="pr_type_CO" value="CO" {{optional($product)->pr_type==="CO" ? "checked":""}}>
                                            <label class="btn btn-outline-info flex-fill" for="pr_type_CO">축하오브제</label>

                                            <input type="radio" class="btn-check" name="pr_type" id="pr_type_CS" value="CS" {{optional($product)->pr_type==="CS" ? "checked":""}}>
                                            <label class="btn btn-outline-info flex-fill" for="pr_type_CS">축하쌀화환</label>

                                            <input type="radio" class="btn-check" name="pr_type" id="pr_type_GH" value="GH" {{optional($product)->pr_type==="GH" ? "checked":""}}>
                                            <label class="btn btn-outline-info flex-fill" for="pr_type_GH">근조화환</label>

                                            <input type="radio" class="btn-check" name="pr_type" id="pr_type_GO" value="GO" {{optional($product)->pr_type==="GO" ? "checked":""}}>
                                            <label class="btn btn-outline-info flex-fill" for="pr_type_GO">근조오브제</label>

                                            <input type="radio" class="btn-check" name="pr_type" id="pr_type_BG" value="BG" {{optional($product)->pr_type==="BG" ? "checked":""}}>
                                            <label class="btn btn-outline-info flex-fill" for="pr_type_BG">근조바구니</label>

                                            <input type="radio" class="btn-check" name="pr_type" id="pr_type_GS" value="GS" {{optional($product)->pr_type==="GS" ? "checked":""}}>
                                            <label class="btn btn-outline-info flex-fill" for="pr_type_GS">근조쌀화환</label>

                                            <input type="radio" class="btn-check" name="pr_type" id="pr_type_ET" value="ET" {{optional($product)->pr_type==="ET" ? "checked":""}}>
                                            <label class="btn btn-outline-info flex-fill" for="pr_type_ET">기타</label>
                                            <td colspan="3"><p>&#8251 해당 타입은 추가배송비를 위한 타입구분입니다.</p></td>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <h4 class="card-title mb-3">상품 가격</h4>
                                    <div class="col-6">
                                        <div class="input-group mb-3">
                                            <label class="input-group-text" for="pr_amount_type1">판매가(기본)</label>
                                            <input type="number" class="form-control" name="pr_amount_type1" id="pr_amount_type1" value="{{ optional($product) -> pr_amount_type1 ?? 0 }}">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="input-group mb-3">
                                            <label class="input-group-text" for="pr_order_amount">발주가</label>
                                            <input type="radio" class="btn-check" name="order_amount_type" id="fix" value="A" {{optional($product) -> order_amount_type == 'A' ? "checked" : "" }}>
                                            <label class="btn btn-outline-primary" for="fix" style="width: 70px;">금액</label>
                                            <input type="radio" class="btn-check" name="order_amount_type" id="percentage" value="R" {{optional($product) -> order_amount_type == 'R' ? "checked" : "" }}>
                                            <label class="btn btn-outline-primary" for="percentage" style="width: 70px;">비율</label>
                                            <input type="number" class="form-control text-end" name="pr_order_amount" id="pr_order_amount" value="{{ optional($product) -> pr_order_amount ?? 0 }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-3">
                                        <div class="input-group mb-3">
                                            <label class="input-group-text" for="pr_amount_type2">판매가2</label>
                                            <input type="number" class="form-control" name="pr_amount_type2" id="pr_amount_type2" value="{{ optional($product) -> pr_amount_type2 ?? 0 }}">
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="input-group mb-3">
                                            <label class="input-group-text" for="pr_amount_type3">판매가3</label>
                                            <input type="number" class="form-control" name="pr_amount_type3" id="pr_amount_type3" value="{{ optional($product) -> pr_amount_type3 ?? 0 }}">
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="input-group mb-3">
                                            <label class="input-group-text" for="pr_amount_type4">판매가4</label>
                                            <input type="number" class="form-control" name="pr_amount_type4" id="pr_amount_type4" value="{{ optional($product) -> pr_amount_type4 ?? 0 }}">
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="input-group mb-3">
                                            <label class="input-group-text" for="pr_amount_type5">판매가5</label>
                                            <input type="number" class="form-control" name="pr_amount_type5" id="pr_amount_type5" value="{{ optional($product) -> pr_amount_type5 ?? 0 }}">
                                        </div>
                                    </div>
                                </div>

                                <h4 class="card-title mb-3">옵션 <button type="button" class="btn btn-primary ms-3" id="add_option_row">옵션 종류 추가</button></h4>
                                <div id="option_area">

                                @if($product && optional($product -> options) ->isNotEmpty())
                                @foreach(optional($product) -> options as $option)
                                    <div class="row border p-3 rounded option_container mb-2" style="background-color: #fafcff">
                                        <div class="col-4">
                                            <div class="input-group mb-3">
                                                <span class="input-group-text">옵션타이틀</span>
                                                <input type="text" class="form-control" name="option_title[]" aria-label="option_title" value="{{$option->option_title}}">
                                            </div>
                                        </div>
                                    @php $values = json_decode($option -> option_value) @endphp
                                        <div class="col-8 option_values_area">
                                        @foreach($values as $key => $value)
                                            <div class="row option_values">
                                                <div class="col-10">
                                                    <div class="input-group mb-3">
                                                        <input type="text" class="form-control" name="option_name{{$loop->parent->index}}[]" aria-label="option_name" value="{{$key}}">
                                                        <input type="number" class="form-control" name="option_price{{$loop->parent->index}}[]" aria-label="option_price" value="{{$value}}">
                                                    </div>
                                                </div>
                                                <div class="col-2">
                                                    <button type="button" class="btn btn-outline-danger option_value_delete">삭제</button>
                                                </div>
                                            </div>
                                        @endforeach
                                        </div>
                                        <div class="row">
                                            <div class="offset-4 col-6 text-center ps-5">
                                                <button type="button" class="btn btn-outline-primary w-75 add_option_value">추가</button>
                                            </div>
                                            <div class="col-2 text-end">
                                                <button type="button" class="btn btn-outline-danger remove_option_cont">옵션종류 삭제</button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                @else
                                    <div class="row border p-3 rounded option_container mb-2" style="background-color: #fafcff">
                                        <div class="col-4">
                                            <div class="input-group mb-3">
                                                <span class="input-group-text">옵션타이틀</span>
                                                <input type="text" class="form-control" name="option_title[]" aria-label="option_title" value="">
                                            </div>
                                        </div>
                                        <div class="col-8 option_values_area">
                                            <div class="row option_values">
                                                <div class="col-10">
                                                    <div class="input-group mb-3">
                                                        <input type="text" class="form-control" name="option_name0[]" aria-label="option_name" placeholder="옵션명" value="">
                                                        <input type="number" class="form-control" name="option_price0[]" aria-label="option_price" placeholder="옵션가격" value="">
                                                    </div>
                                                </div>
                                                <div class="col-2">
                                                    <button type="button" class="btn btn-outline-danger option_value_delete">삭제</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="offset-4 col-6 text-center ps-5">
                                                <button type="button" class="btn btn-outline-primary w-75 add_option_value">추가</button>
                                            </div>
                                            <div class="col-2 text-end">
                                                <button type="button" class="btn btn-outline-danger remove_option_cont">옵션종류 삭제</button>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                </div>
                                <div class="row mt-3">
                                    <h4 class="card-title mb-3">기타 설정</h4>
                                    <div class="col-6">
                                        <div class="input-group mb-3">
                                            <div class="form-check form-switch me-4">
                                                <label for="pr_popular" class="form-check-label">인기상품(메인페이지 베스트셀러 노출)</label>
                                                <input class="form-check-input" name="pr_popular" id="pr_popular" value="Y" type="checkbox" role="switch" {{optional($product) -> pr_popular=='Y' ? "checked" : "" }} aria-label="pr_popular">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-check form-switch me-4">
                                            <label for="pr_discount" class="form-check-label">세일상품(세일상품 표시)</label>
                                            <input class="form-check-input" name="pr_discount" id="pr_discount" value="Y" type="checkbox" role="switch" {{optional($product) -> pr_discount=='Y' ? "checked" : "" }} aria-label="pr_discount">
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
                                        <input type="hidden" name="pr_handler" value="{{ Auth::user() -> name }}">
                                        <button type="button" class="btn btn-success btn-lg" onclick="pr_submit();">{{isset($product) ? "상품 수정" : "상품 등록" }}</button>
                                    </div>
                                    @if(isset($product))
                                    <div class="col-6 d-grid">
                                        <button type="button" class="btn btn-danger btn-lg" onclick="remove_product('{{ optional($product) -> idx ?? 0 }}');">상품 삭제</button>
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
{{--                                                <img src="{{ optional($product) -> pr_img }}" width="400" height="400" data-bs-target="#modal" data-bs-toggle="modal" >--}}
                                                <img src="{{ optional($product) -> pr_img ?? "" }}" id="img_box" width="400" height="400" onclick="change_img();" style="cursor: pointer">
                                                <input type="file" name="pr_img" class="d-none" accept="image/*">
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
                                            <div class="col-4 text-end">
                                                <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#search_word_modal">검색어 관리</button>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <textarea class="form-control" name="pr_memo" aria-label="pr_memo">{{ optional($product) -> pr_memo ?? "" }}</textarea>
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
                                        <textarea class="d-none" name="pr_description" aria-label="pr_description"></textarea>
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
{{--    <script src="{{ URL::asset('/assets/libs/cropper/cropper.min.js') }}"></script>--}}
    <script>
        const original_id = '{{ optional($product) -> pr_id }}';
        $(document).ready(function() {
            var $summernote = $('#description_area').summernote({
                height: 500,
                lang: 'ko-KR',
                callbacks: {
                    onImageUpload : function(files) {
                        sendFile(files[0], this);
                    }
                }
            });
            @if(!empty(optional($product) -> pr_description))
            $('#description_area').summernote('code', '{!! optional($product) -> pr_description ?? "" !!}');
            @endif
        });


        const pr_img = document.querySelector('input[name="pr_img"]');

        pr_img.addEventListener('change', () => {
            if(pr_img.files[0]) {
                document.querySelector('#img_box').src = URL.createObjectURL(pr_img.files[0]);
                $('#cancel_change').removeClass('d-none');
            } else {
                document.querySelector('#img_box').src = '{{ optional($product) -> pr_img ?? "" }}';
                $('#cancel_change').addClass('d-none');
            }
        });

        function reset_note() {
            $('#description_area').summernote('reset');
            @if(!empty(optional($product) -> pr_description))
            $('#description_area').summernote('code', '{!! optional($product) -> pr_description ?? "" !!} ');
            @endif
        }

    </script>
    <script src="{{ URL::asset('/assets/js/product/product-edit.js') }}"></script>
@endsection
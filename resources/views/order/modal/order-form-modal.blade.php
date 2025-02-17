<!-- 경조사어 모달 창 -->
<div class="modal fade event-msg-modal-center" id="find_ribbon" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <!-- 경조사어 모달 header -->
            <div class="modal-header">
                <h5 class="modal-title">경조사어 찾기</h5>
                <button type="button" class="btn-close" id="center_modal_close" data-bs-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <!-- 경조사어 모달 body -->
            <div class="modal-body">
                <div class="row">
                    <!-- DB에 있는 카테고리 버튼 -->
                    <div class="col-md-12">
                        @foreach($msg_templates as $template)
                            <button type="button" class="btn btn-outline-secondary btn-sm mb-1" onclick="getMsgList('{{ $template-> code }}')">{{ $template -> code_name }}</button>
                        @endforeach
                    </div>
                </div>
                <hr>
                <!-- 해당 카테고리 내 목록 -->
                <div class="row mx-2" id="event_msg_btns">
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- 경조사어 모달 창 끝 -->

<!-- 아이템 리스트 모달 창 -->
<div class="modal fade" id="product-list-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl goods_modal">
        <div class="modal-content">
            <!-- 모달 헤더 -->
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="myExtraLargeModalLabel">상품 추가 <span class="price-type-text">기본값</span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="product_modal_close">
                </button>
            </div>
            <div class="modal-body">
                <!-- 상품 검색 라인 -->
                <form id="product-search-form">
                    <div class="row">
                        <!-- 카테고리 select -->
                        <div class="col-4">
                            <select class="form-select" name="product_ctgy" id="product_ctgy">
                                <option value="">카테고리</option>
                                @foreach($product_ctgy as $ctgy)
                                    <option value="{{ $ctgy -> ct2 }}">{{ $ctgy -> ct_name }}</option>
                                @endforeach
                            </select>
                        </div>
{{--                        <!-- 가격대별 select -->--}}
{{--                        <div class="col-3 px-0">--}}
{{--                            <select class="form-select" name="goods_price_list" id="goods_price_list" aria-label="product_price">--}}
{{--                                <option value="">- 가격대별 - </option>--}}
{{--                                <option value="30">30,000원 이하</option>--}}
{{--                                <option value="70">30,000 ~ 70,000원</option>--}}
{{--                                <option value="100">70,000 ~ 100,000원</option>--}}
{{--                                <option value="150">100,000 ~ 150,000원</option>--}}
{{--                                <option value="151">150,000원 이상</option>--}}
{{--                            </select>--}}
{{--                        </div>--}}
                        <!-- 직접 입력 input -->
                        <div class="col-6">
                            <input type="text" class="form-control" name="search_word" aria-label="goods_search" placeholder="상품명 or 검색어">
                        </div>
                        <!-- 검색 버튼 -->
                        <div class="col-2">
                            <a type="button" class="btn btn-outline-secondary fs-5 py-1 h-100" id="search_btn">검색</a>
                        </div>
                    </div>
                </form>
                <!-- 상품 리스트 결과 창 -->
                <hr>
                <div class="row">
                    <div class="col-12" id="product-list">
                        <!-- 아이템 리스트 검색 결과 목록 -->
                        @include('order.include.order-form.products-list')
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->

</div><!-- /.modal -->
<!-- 아이템 리스트 모달 창 끝 -->

<!-- 수기 결제 모달 -->
<div class="modal fade" id="cardKeyIn" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">수기 결제</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="keyIn_form">
                <div>
                    <span class="col-form-text mb-2">카드 번호</span>
                    <div class="input-group mb-3">
                        <input type="number" class="form-control" name="cardNum1" id="cardNum1" maxlength="4" aria-label="card_num" value="" oninput="moveToNext(this, 4)">
                        <input type="number" class="form-control" name="cardNum2" id="cardNum2" maxlength="4" aria-label="card_num" value="" onkeydown="checkBackspace(event)" oninput="moveToNext(this, 4)">
                        <input type="number" class="form-control" name="cardNum3" id="cardNum3" maxlength="4" aria-label="card_num" value="" onkeydown="checkBackspace(event)" oninput="moveToNext(this, 4)">
                        <input type="number" class="form-control" name="cardNum4" id="cardNum4" maxlength="6" aria-label="card_num" value="" onkeydown="checkBackspace(event)">
                    </div>
                </div>
                <div class="mb-1">
                    <span class="col-form-text mb-2">만료 기간 (MM/YY)</span>
                    <div class="input-group w-50">
                        <input type="text" class="form-control" name="exMonth" id="exMonth" aria-label="card_num" value="" maxlength="2" placeholder="MM" onkeydown="checkBackspace(event)">
                        <input type="text" class="form-control" name="exYear" id="exYear" aria-label="card_num" value="" maxlength="2" placeholder="YY" oninput="moveToNext(this, 2)">
                    </div>
                </div>
                <div class="mb-1">
                    <span class="col-form-text mb-2">할부</span>
                    <div class="input-group w-50">
                        <select class="form-select" name="cardQuota" aria-label="example">
                            <option value="00" selected>일시불</option>
                            <option value="02">2개월</option>
                            <option value="03">3개월</option>
                            <option value="04">4개월</option>
                            <option value="05">5개월</option>
                            <option value="06">6개월</option>
                        </select>
                    </div>
                </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="pay_keyIn(event);">키인 결제</button>
            </div>
        </div>
    </div>
</div>
<!-- 수기 결제 모달 끝 -->

<!-- 수기 결제 모달 -->
<div class="modal fade" id="virtualAccount-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">가상계좌</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="virtualAccount_form">
                    <div>
                        <span class="col-form-text mb-2">구매자 명</span>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" name="paymentName" id="paymentName">
                            <button type="button" class="btn btn-secondary" style="width: 150px;" onclick="get_ordererName()">주문자명 가져오기</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="pay_virtualAccount(event);">가상계좌 결제</button>
            </div>
        </div>
    </div>
</div>
<!-- 수기 결제 모달 끝 -->


<!-- 지역추가금 모달 창 -->
<div class="modal fade event-msg-modal-center" id="location_price" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <!-- 지역추가금 모달 header -->
            <div class="modal-header">
                <h5 class="modal-title">지역추가금</h5>
                <button type="button" class="btn-close" id="location_modal_close" data-bs-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <!-- 지역추가금 모달 body -->
            <div class="modal-body">
                <div class="row">
                    <!-- DB에 있는 카테고리 버튼 -->
                    <div class="col-12" id="select_location_area">
                        <select id="select-location" style="width: 60%; font-size: 16px">
                            <option value=""></option>
                            @foreach($locations as $location)
                                <option value="{{$location->sido."/".$location->sigungu}}">{{$location->sido." ".$location->sigungu}}</option>
                            @endforeach
                        </select>
                        <button type="button" class="btn btn-outline-secondary" style="height: 40px;" onclick="add_loc_price()">추가</button>
                        <span id="loc_price_text"></span>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- 지역추가금 모달 창 끝 -->
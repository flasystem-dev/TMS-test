<div class="card-body">
    <!-- right offcanvas -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel" style="width:350px;">
        <div class="offcanvas-header">
            <h5 id="offcanvasRightLabel"><b>간편주문 링크 전송</b></h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <h5 class="card-title m-3">카테고리</h5>
            <div>
                <div class="col-12">
                    <button type="button" onclick="item_ajax('축하화환','{{$brand}}');" class="btn btn-outline-secondary btn-sm mb-1 ctgy_type1">축하화환</button>
                    <button type="button" onclick="item_ajax('근조화환','{{$brand}}');" class="btn btn-outline-secondary btn-sm mb-1 ctgy_type1">근조화환</button>
                    <button type="button" onclick="item_ajax('서양란','{{$brand}}');" class="btn btn-outline-secondary btn-sm mb-1 ctgy_type1">서양란</button>
                    <button type="button" onclick="item_ajax('동양란','{{$brand}}');" class="btn btn-outline-secondary btn-sm mb-1 ctgy_type1">동양란</button>
                    <!-- 줄바꿈 위치 -->
                    <button type="button" onclick="item_ajax('꽃다발','{{$brand}}');" class="btn btn-outline-secondary btn-sm mb-1 ctgy_type1">꽃다발</button>
                    <button type="button" onclick="item_ajax('꽃바구니','{{$brand}}');" class="btn btn-outline-secondary btn-sm mb-1 ctgy_type1">꽃바구니</button>
                    <button type="button" onclick="item_ajax('관엽(일반분)','{{$brand}}');" class="btn btn-outline-secondary btn-sm mb-1 ctgy_type2">관엽(일반분)</button>
                    <button type="button" onclick="item_ajax('관엽(탁상용)','{{$brand}}');" class="btn btn-outline-secondary btn-sm mb-1 ctgy_type2">관엽(탁상용)</button>
                </div>
            </div>
            <h5 class="card-title m-3">상품선택</h5>
            <div>
                <label>
                    <select class="select" name="goods_select" id="app_items">
                        <option value="">상품을 선택하세요</option>
                    </select>
                </label>
            </div>
            <p class="explain">*상품을 선택하지 않으면 메인화면 링크가 전송됩니다.</p>
            <h5 class="card-title m-3">연락처</h5>
            <div>
                <input type="text" class="app_text" id="send_phone_num">
            </div>
            <div style="text-align: center;">
                <button type='button' class='btn btn-secondary waves-effect waves-light col-10 m-4' onclick="link_send();">간편주문 링크전송</button>
            </div>
        </div>
    </div>
</div>
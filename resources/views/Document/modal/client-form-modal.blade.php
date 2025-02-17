<!-- 담당자 모달 -->
<div class="modal fade" id="manager_form_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 620px;">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">담당자 정보</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="manager_form">
            <input type="hidden" name="client" value="{{optional($client) -> id }}">
            <div class="modal-body" id="manager_form_body">
            </div>
            </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">취소</button>
                <button type="button" class="btn btn-primary" onclick="managerUpsert()">저장</button>
            </div>
        </div>
    </div>
</div>
<!-- 담당자 모달 끝 -->

<!-- 회원 모달 -->
<div class="modal fade" id="user_form_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 620px;">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5">거래처 회원등록</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="user_form">
                <input type="hidden" name="client" value="{{optional($client) -> id }}">
                <div class="modal-body" id="user_register_body">
                    <div class="form_row">
                        <div class="mb-3 input-group">
                            <span class="input-group-text">브랜드</span>
                            <select class="form-select" id="user_brand">
                                <option value="">- 브랜드 선택 -</option>
                                @foreach($brands as $brand)
                                    <option value="{{$brand->brand_type_code}}">{{CommonCodeName($brand->brand_type_code)}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form_row">
                        <div class="mb-3 input-group">
                            <span class="input-group-text">회원</span>
                            <select id="user_select" class="form-select" name="id">

                            </select>
                        </div>
                    </div>
                </div>
            </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">취소</button>
                <button type="button" class="btn btn-primary" onclick="userRegister()">등록</button>
            </div>
        </div>
    </div>
</div>
<!-- 회원 모달 끝 -->
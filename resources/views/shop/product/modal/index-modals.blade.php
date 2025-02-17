<!-- 상품 추가 모달 -->
<div class="modal fade" id="add_product_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">상품 추가</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-12 text-end d-grid">
                        <button type="button" class="btn btn-outline-success waves-effect waves-light" onclick="add_product();" >상품 개별 추가</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="row mb-2">
                            <p class="mb-1 ms-2 fw-bold">상품 엑셀업로드</p>
                        </div>
                        <div class="row">
                            <div class="input-group mb-3">
                                <input type="file" class="form-control" name="excel_file" id="excel_file" aria-label="excel_file">
                                <input type="hidden" name="handler" value="{{ Auth::user() -> name }}">
                                <button type="button" class="btn btn-outline-secondary" onclick="send_file()">엑셀 업로드</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="row mb-2">
                            <p class="mb-1 ms-2 fw-bold">상품옵션 엑셀업로드</p>
                        </div>
                        <div class="row">
                            <div class="input-group mb-3">
                                <input type="file" class="form-control" name="option_file" id="option_file" aria-label="option_file">
                                <button type="button" class="btn btn-outline-secondary" onclick="send_optionFile()">엑셀 업로드</button>
                            </div>
                        </div>
                    </div>
                </div>
                @if(Auth::user()->auth > 9)
                <div class="row">
                    <div class="col-12">
                        <div class="row mb-2">
                            <p class="mb-1 ms-2 fw-bold">상품기타수정 엑셀업로드</p>
                        </div>
                        <div class="row">
                            <div class="input-group mb-3">
                                <input type="file" class="form-control" name="etc_file" id="etc_file" aria-label="etc_file">
                                <button type="button" class="btn btn-outline-secondary" onclick="send_etcFile()">엑셀 업로드</button>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
<!-- 상품 추가 모달 끝 -->
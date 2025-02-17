@if(isset($template_info))

    <!-- 개발 변수 설정 모달 -->
    <div class="modal fade" id="dev_setting_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">개발 변수 설정</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="dev_variable_close_btn"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <form id="variable_tbl">
                                <input type="hidden" name="templateCode" value="{{ $template_info -> templateCode }}">
                            @foreach($variables as $variable)
                                <div class="row">
                                    <div class="col-12">
                                        <div class="input-group mb-3">
                                            <input type="text" class="form-control" name="variables[]" value="{{ $variable }}" aria-label="variables" readonly>
                                            <span class="input-group-text w-0" style="width: 40px;"> => </span>
                                            <select class="form-select table_name" name="table_name[]" aria-label="Default select example" data-index="{{$loop->index}}" id="table_name{{$loop->index}}">
                                                <option value="">- 테이블 선택 -</option>
                                                <option value="order_data">주문 정보</option>
                                                <option value="order_delivery">배송 정보</option>
                                                <option value="order_payment">결제 정보</option>
                                                <option value="code_of_company_info">브랜드 정보</option>
                                            </select>
                                            <select class="form-select column_name" name="column_name[]" aria-label="Default select example" id="column_name{{$loop->index}}">
                                                <option value=""></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success" data-code="{{ $template_info -> templateCode }}" style="width: 150px;" onclick="edit_values();" >수정</button>
                </div>
            </div>
        </div>
    </div>
    <!-- 개발 변수 설정 모달 끝-->

    <div class="card">
        <div class="card-body p-3">
            <div class="row w-95 mx-auto mb-2">
                <div class="col-6 mt-2">
                    <h5 id="templateCode" data-code="{{ $template_info -> templateCode }}">템플릿코드 : {{ $template_info -> templateCode }}</h5>
                </div>
                <div class="col-6 text-end">
{{--                    <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#send_talk_modal">보내기</button>--}}
                    @if(Auth::user() -> auth > 9)
                    <button type="button" class="btn btn-outline-danger" id="variables_area_btn" data-bs-toggle="modal" data-bs-target="#dev_setting_modal">개발 설정</button>
                    @endif
                </div>
            </div>
            <div class="row w-95 mx-auto border rounded-3 p-2 mt-3">
                <div class="col-7 mt-2 p-3">
                    <pre id="template_view_area">{{ $template_info -> template }}</pre>
                </div>
                <div class="col-5 border rounded-3 pt-3" id="variables_area">
                    <div>
                        <div class="row">
                            <div class="col-12">
                                @foreach($variables as $variable)
                                    <div class="row">
                                        <div class="col-12">

                                            <div class="input-group mb-3">
                                                <input type="text" class="form-control" name="variables_list" value="{{ $variable ?? '' }}" readonly>
                                                <span class="input-group-text w-0"> => </span>
                                                <input type="text" name="tmp_values" class="form-control ps-3" value="{{ $values[$loop->index]['column'] ?? '' }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
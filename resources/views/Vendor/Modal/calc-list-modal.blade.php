<div class="modal fade" id="excel_form_modal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">기타금액 엑셀 업로드</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body">
                <form name="excel_upload_form" id="excel_upload_form" enctype="multipart/form-data">
                    @csrf
                    <div class="dropdown">
                        <div><button type="button" class="brand_btn {{request()->brand ?? "BTCS"}}" onclick="vendorExcelDownload('{{request()->brand?? "BTCS"}}');">{{CommonCodeName(request()->brand ?? "BTCS") ?? "플라체인"}} 목록 다운로드</button></div>
                        <div><input type="file" name="file" id="file" class="form-control"></div>
                        <div class="input-group me-2 py-2">
                            <select class="form-control rounded mx-1" name="excel_year" id="selectedName" aria-label="word1">
                                @for($i=date('Y')-2;$i<=date('Y');$i++)
                                    <option value="{{$i}}" {{$year==$i? "selected" : ""}}>{{$i}}년</option>
                                @endfor
                            </select>
                            <select class="form-control rounded" name="excel_month"  id="selectedName" aria-label="word1">
                                @for($i=1;$i<13;$i++)
                                    <option value="{{$i}}" {{$month==$i? "selected" : ""}}>{{$i}}월</option>
                                @endfor
                            </select>
                        </div>
                        <div class="me-2 py-2" style="text-align: center">
                            <button type="button" class="btn btn-success waves-effect waves-light" id="etc_excel_upload">
                                <i class="uil uil-check me-2"></i> 기타금액 엑셀 업로드
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@php
    $did_num = DB::table('did_number') -> where('is_used', 'N') -> orderBy('update_date', 'asc') -> get();
@endphp

<div class="modal" tabindex="-1" id="gen_did_modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header px-5">
                <h3 class="modal-title fw-bolder">번호 선택</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-light">
                <div class="row">
                    <div class="col-11 m-auto">
                        <div class="card card-body">
                            <div class="row">
                                <div class="col-12 ps-3">
                                    <h4 class="fw-bolder">DID 번호</h4>
                                </div>
                            </div>
                            <div class="row mt-3 px-3">
                                <div class="col-12 border p-3 btn_container" >
                                    @foreach($did_num as $did)
                                        <button type="button" class="btn btn-outline-secondary m-2" onclick="select_did(event);">{{ $did -> did_num }}</button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
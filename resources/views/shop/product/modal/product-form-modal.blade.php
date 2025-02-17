<!-- 검색어 등록 모달 -->
<div class="modal fade" id="search_word_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">검색어 등록</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
{{--                @php dd($search_words) @endphp--}}
                @if(count($search_words) !== 0 && !empty($search_words[0]))
                    @foreach($search_words as $word)
                        <div class="row mb-3">
                            <div class="col-8">
                                <input type="text" class="form-control search-word" aria-label="search_word" value="{{ $word }}">
                                <input type="hidden" class="previous-word" value="{{ $word }}">
                            </div>
                            <div class="col-2 d-grid">
                                <button type="button" class="btn btn-outline-success edit-search-word">수정</button>
                            </div>
                            <div class="col-2 d-grid">
                                <button type="button" class="btn btn-outline-danger delete-search-word">삭제</button>
                            </div>
                        </div>
                    @endforeach
                @endif
                <div class="row">
                    <div class="col-8">
                        <input type="text" class="form-control search-word" aria-label="search_word">
                    </div>
                    <div class="col-2 d-grid pe-1">
                        <button type="button" class="btn btn-primary add-search-word">추가</button>
                    </div>
                    <div class="col-2 d-grid ps-1">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- 검색어 등록 모달 끝 -->
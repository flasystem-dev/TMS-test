@if ($paginator->hasPages())
    <div>
        <ul class="pagination">
            @php
                // 10페이지 단위로 계산하여 10페이지 이전으로 이동하는 링크
                $previousPageSet = max(1, $paginator->currentPage() - 10);
                $startPage = (int)(($paginator->currentPage() - 1) / 10) * 10 + 1;  // 시작 페이지 계산
                $endPage = $startPage + 9;
            @endphp
            @if ($paginator->currentPage() > 10)
                <li><a href="{{ $paginator->url($previousPageSet) }}" rel="prev"><<</a></li>
            @else
                <li class="disabled"><span><<</span></li>
            @endif

            @php
                // 페이지네이션 링크 범위를 10개 단위로 설정
                $startPage = max(1, $paginator->currentPage() - ($paginator->currentPage() % 10) + 1);
                $endPage = $startPage + 9;

                if($orders_count!==0) {
                    $perPage = session('perPage') ?? 20;
                    $lastPage = ceil((int)$orders_count / $perPage);
                    $endPage = min($startPage + 9, $lastPage);
                }
            @endphp

            {{-- Pagination Elements --}}
            @for ($page = $startPage; $page <= $endPage; $page++)
                @if ($page === $paginator->currentPage())
                    <li class="active"><span>{{ $page }}</span></li>
                @else
                    {{-- 더 이상 페이지가 없으면 루프 중단 --}}
                    @if ($page > $paginator->currentPage() && !$paginator->hasMorePages())
                        @break
                    @endif
                    <li><a href="{{ $paginator->url($page) }}">{{ $page }}</a></li>
                @endif
            @endfor

            @php
                // 10페이지 단위로 계산하여 10페이지 이후로 이동하는 링크
                $nextPageSet = $paginator->currentPage() + 10;
            @endphp
            @if ($paginator->hasMorePages())
                <li><a href="{{ $paginator->url($nextPageSet) }}" rel="next">>></a></li>
            @else
                <li class="disabled"><span>>></span></li>
            @endif
        </ul>
    </div>
@endif

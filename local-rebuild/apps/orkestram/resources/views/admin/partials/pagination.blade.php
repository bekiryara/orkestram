@if(isset($paginator) && $paginator->lastPage() > 1)
    <div class="admin-pagination">
        @if($paginator->onFirstPage())
            <span class="btn disabled">Onceki</span>
        @else
            <a class="btn" href="{{ $paginator->previousPageUrl() }}">Onceki</a>
        @endif

        <span class="page-meta">Sayfa {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}</span>

        @if($paginator->hasMorePages())
            <a class="btn" href="{{ $paginator->nextPageUrl() }}">Sonraki</a>
        @else
            <span class="btn disabled">Sonraki</span>
        @endif
    </div>
@endif

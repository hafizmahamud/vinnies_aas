<div class="pagination pagination-basic">
    <span class="page-total">{{ $paginator->total() }} item{{ $paginator->total() > 1 ? 's' : '' }}</span>

    @if ($paginator->hasPages())
        <a href="{{ $paginator->url(1) }}" class="page-first js-table-pager" data-page="first">&laquo;</a>
        <a href="{{ $paginator->previousPageUrl() }}" class="page-prev js-table-pager" data-page="previous">&lsaquo;</a>
        <span class="page-status">{{ $paginator->currentPage() }} of {{ $paginator->lastPage() }}</span>
        <a href="{{ $paginator->nextPageUrl() }}" class="page-next js-table-pager" data-page="next">&rsaquo;</a>
        <a href="{{ $paginator->url($paginator->lastPage()) }}" class="page-last js-table-pager" data-page="last">&raquo;</a>
    @endif
</div>

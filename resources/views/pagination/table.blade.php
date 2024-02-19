@if ($paginator->hasPages())
    {{-- <p class="pagination-text">Pages: </p> --}}
    <ul class="pagination pagination-table scrollToBottom">
        {{-- Pagination Elements --}}
       <li><a href="{{ $paginator->url(1) }}" class="page-first js-table-pager" data-page="first">&laquo;</a></li> 
       <li><a href="{{ $paginator->previousPageUrl() }}" class="page-prev js-table-pager" data-page="previous">&lsaquo;</a></li>

        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <li class="disabled"><span>{{ $element }}</span></li>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="active"><span>{{ $page }}</span></li>
                    @else
                        <li><a href="{{ $url }}" class="js-table-pager" data-page="{{ $page - 1 }}">{{ $page }}</a></li>
                    @endif
                @endforeach
            @endif
        @endforeach
        <li><a href="{{ $paginator->nextPageUrl() }}" class="page-next js-table-pager" data-page="next">&rsaquo;</a></li>
        <li><a href="{{ $paginator->url($paginator->lastPage()) }}" class="page-last js-table-pager" data-page="last">&raquo;</a></li>
    </ul>
@endif

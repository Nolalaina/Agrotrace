@if ($paginator->hasPages())
    <nav class="pagination-nav" role="navigation" aria-label="Pagination">
        <p class="pagination-info">
            {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} sur {{ $paginator->total() }}
        </p>

        <ul class="pagination">
            @if ($paginator->onFirstPage())
                <li><span class="page disabled" aria-disabled="true">‹</span></li>
            @else
                <li><a class="page" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Page précédente">‹</a></li>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <li><span class="page disabled">{{ $element }}</span></li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li><span class="page current" aria-current="page">{{ $page }}</span></li>
                        @else
                            <li><a class="page" href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <li><a class="page" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Page suivante">›</a></li>
            @else
                <li><span class="page disabled" aria-disabled="true">›</span></li>
            @endif
        </ul>
    </nav>
@endif

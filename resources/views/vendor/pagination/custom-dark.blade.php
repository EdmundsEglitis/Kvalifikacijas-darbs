@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center space-x-2">
        @if ($paginator->onFirstPage())
            <span class="px-3 py-1 rounded-md bg-[#1f2937] text-gray-500 cursor-not-allowed">‹</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-1 rounded-md bg-[#1f2937] text-[#84CC16] hover:bg-[#374151]">‹</a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="px-3 py-1 text-gray-400">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="px-3 py-1 rounded-md bg-[#84CC16] text-[#111827] font-bold">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="px-3 py-1 rounded-md bg-[#1f2937] text-white hover:bg-[#374151]">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-1 rounded-md bg-[#1f2937] text-[#84CC16] hover:bg-[#374151]">›</a>
        @else
            <span class="px-3 py-1 rounded-md bg-[#1f2937] text-gray-500 cursor-not-allowed">›</span>
        @endif
    </nav>
@endif

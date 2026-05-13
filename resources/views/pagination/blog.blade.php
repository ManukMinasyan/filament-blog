@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Blog pagination" class="flex items-center justify-between mt-10">
        <div class="flex flex-1 items-center justify-between">
            @if ($paginator->onFirstPage())
                <span class="text-sm text-zinc-400" aria-hidden="true">Previous</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}"
                   rel="prev"
                   wire:navigate
                   aria-label="Go to previous page"
                   class="text-sm text-zinc-600 hover:text-zinc-900">Previous</a>
            @endif

            <ol class="flex items-center gap-2">
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <li class="text-sm text-zinc-400" aria-hidden="true">{{ $element }}</li>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            <li>
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page"
                                          class="inline-flex items-center justify-center w-8 h-8 text-sm font-medium text-white bg-zinc-900 rounded">
                                        {{ $page }}
                                    </span>
                                @else
                                    <a href="{{ $url }}"
                                       wire:navigate
                                       aria-label="Go to page {{ $page }}"
                                       class="inline-flex items-center justify-center w-8 h-8 text-sm text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 rounded">
                                        {{ $page }}
                                    </a>
                                @endif
                            </li>
                        @endforeach
                    @endif
                @endforeach
            </ol>

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}"
                   rel="next"
                   wire:navigate
                   aria-label="Go to next page"
                   class="text-sm text-zinc-600 hover:text-zinc-900">Next</a>
            @else
                <span class="text-sm text-zinc-400" aria-hidden="true">Next</span>
            @endif
        </div>
    </nav>
@endif

<div>
    <label for="blog-search" class="sr-only">Search blog posts</label>
    <input
        type="search"
        id="blog-search"
        wire:model.live.debounce.400ms="query"
        placeholder="Search posts…"
        class="w-full rounded-md border border-zinc-200 px-4 py-2 text-sm focus:border-zinc-400 focus:outline-none"
        aria-label="Search blog posts"
    />

    @if ($query !== '')
        <div class="mt-6">
            @if ($results->isEmpty())
                <p class="text-sm text-zinc-500">No posts match "{{ $query }}".</p>
            @else
                <ul class="space-y-4">
                    @foreach ($results as $post)
                        <li>
                            <a href="{{ $post->getUrl() }}" wire:navigate class="block">
                                <h3 class="text-base font-medium text-zinc-900">{{ $post->title }}</h3>
                                @if ($post->excerpt)
                                    <p class="mt-1 text-sm text-zinc-600">{{ $post->excerpt }}</p>
                                @endif
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endif
</div>

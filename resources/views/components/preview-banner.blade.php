@push('head')
    <meta name="robots" content="noindex, nofollow">
@endpush

<div class="sticky top-0 z-[60] bg-amber-500 text-white py-2 px-4 text-sm font-medium flex items-center justify-between">
    @if($editUrl)
        <a href="{{ $editUrl }}" class="inline-flex items-center gap-1.5 text-white/90 hover:text-white transition-colors">
            &larr; Edit Post
        </a>
    @else
        <span></span>
    @endif
    <span>Preview &mdash; {{ $post->published_at ? 'viewing published post' : 'this post is not published' }}</span>
</div>

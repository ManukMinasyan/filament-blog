@if($post->featured_image)
    <img
        src="{{ asset('storage/' . $post->featured_image) }}"
        alt="{{ $post->title }}"
        class="w-full aspect-video object-cover rounded-xl mb-10"
    >
@endif

<div class="prose prose-lg dark:prose-invert max-w-none prose-headings:font-semibold prose-headings:tracking-tight prose-a:text-primary-600 dark:prose-a:text-primary-400 prose-a:no-underline hover:prose-a:underline prose-code:text-sm">
    {!! $post->renderedContent() !!}
</div>

@php echo '<?xml version="1.0" encoding="UTF-8"?>'; @endphp
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title>{{ config('filament-blog.feed.title', config('app.name') . ' Blog') }}</title>
        <link>{{ \Illuminate\Support\Facades\Route::has('blog.index') ? route('blog.index') : url('/') }}</link>
        <description>{{ config('filament-blog.feed.description', '') }}</description>
        <language>en</language>
        <lastBuildDate>{{ $posts->first()?->published_at?->toRfc2822String() }}</lastBuildDate>
        @if(\Illuminate\Support\Facades\Route::has('blog.feed'))
            <atom:link href="{{ route('blog.feed') }}" rel="self" type="application/rss+xml" />
        @endif

        @foreach($posts as $post)
            <item>
                <title>{{ $post->title }}</title>
                @if(\Illuminate\Support\Facades\Route::has('blog.show'))
                    <link>{{ route('blog.show', $post->slug) }}</link>
                    <guid isPermaLink="true">{{ route('blog.show', $post->slug) }}</guid>
                @endif
                <description><![CDATA[{{ $post->excerpt ?: Str::limit(strip_tags($post->content), 300) }}]]></description>
                <pubDate>{{ $post->published_at->toRfc2822String() }}</pubDate>
                @if($post->category)
                    <category>{{ $post->category->name }}</category>
                @endif
                @if(config('filament-blog.feed.author_email'))
                    <author>{{ config('filament-blog.feed.author_email') }} ({{ $post->author?->name }})</author>
                @endif
            </item>
        @endforeach
    </channel>
</rss>

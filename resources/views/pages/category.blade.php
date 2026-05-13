@extends(config('ink.layout', 'layouts.app'))

@section('content')
<div class="max-w-3xl mx-auto px-4 py-12">
    <h1 class="text-3xl font-bold mb-8">{{ $category->name }}</h1>

    <div class="space-y-8">
        @forelse ($posts as $post)
            <x-ink::post-card :post="$post" />
        @empty
            <p class="text-gray-500">No posts in this category yet.</p>
        @endforelse
    </div>

    <div class="mt-12">{{ $posts->links() }}</div>
</div>
@endsection

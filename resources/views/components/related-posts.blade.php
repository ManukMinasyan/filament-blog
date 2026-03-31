<section class="mt-10 pt-10 border-t border-gray-200 dark:border-gray-800">
    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Related posts</h2>
    <div class="divide-y divide-gray-100 dark:divide-gray-800">
        @foreach($posts as $relatedPost)
            <x-blog::post-card :post="$relatedPost" />
        @endforeach
    </div>
</section>

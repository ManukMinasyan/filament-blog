<?php

namespace ManukMinasyan\FilamentBlog\Mcp\Tools;

use ManukMinasyan\FilamentBlog\Enums\PostStatus;
use ManukMinasyan\FilamentBlog\Models\Post;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Description('List blog posts with optional filters for status, category, and search term.')]
#[IsReadOnly]
class ListPostsTool extends Tool
{
    public function handle(Request $request): Response|ResponseFactory
    {
        if (! $request->user()?->is_admin) {
            return Response::error('Permission denied. Admin access required.');
        }

        if (! $request->user()->tokenCan('posts:read')) {
            return Response::error('Token missing required ability: posts:read');
        }

        $query = Post::query()->with('category');

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($categoryId = $request->get('category_id')) {
            $query->where('category_id', $categoryId);
        }

        if ($search = $request->get('search')) {
            $query->where('title', 'like', "%{$search}%");
        }

        $perPage = min((int) ($request->get('per_page') ?? 20), 50);
        $page = max((int) ($request->get('page') ?? 1), 1);

        $paginator = $query->latest()->paginate($perPage, ['*'], 'page', $page);

        if ($paginator->isEmpty()) {
            return Response::text('No posts found.');
        }

        return Response::structured([
            'data' => $paginator->map(fn (Post $post) => [
                'id' => $post->id,
                'title' => $post->title,
                'slug' => $post->slug,
                'excerpt' => $post->excerpt,
                'status' => $post->status->value,
                'category' => $post->category?->name,
                'author_id' => $post->author_id,
                'published_at' => $post->published_at?->toIso8601String(),
                'created_at' => $post->created_at->toIso8601String(),
            ])->all(),
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }

    /** @return array<string, \Illuminate\JsonSchema\Types\Type> */
    public function schema(JsonSchema $schema): array
    {
        return [
            'status' => $schema->string()
                ->enum(array_column(PostStatus::cases(), 'value'))
                ->description('Filter by post status.'),
            'category_id' => $schema->integer()
                ->description('Filter by category ID.'),
            'search' => $schema->string()
                ->description('Search posts by title.'),
            'page' => $schema->integer()
                ->description('Page number. Defaults to 1.'),
            'per_page' => $schema->integer()
                ->description('Results per page (1-50). Defaults to 20.'),
        ];
    }
}

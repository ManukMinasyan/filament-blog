<?php

declare(strict_types=1);

namespace ManukMinasyan\FilamentBlog\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;
use ManukMinasyan\FilamentBlog\Models\Category;

#[Description('List all blog categories with their post counts.')]
#[IsReadOnly]
class ListCategoriesTool extends Tool
{
    public function handle(Request $request): Response|ResponseFactory
    {
        if (! $request->user()?->is_admin) {
            return Response::error('Permission denied. Admin access required.');
        }

        if (! $request->user()->tokenCan('categories:read')) {
            return Response::error('Token missing required ability: categories:read');
        }

        $perPage = min((int) ($request->get('per_page') ?? 20), 50);
        $page = max((int) ($request->get('page') ?? 1), 1);

        $paginator = Category::withCount('posts')
            ->orderBy('name')
            ->paginate($perPage, ['*'], 'page', $page);

        if ($paginator->isEmpty()) {
            return Response::text('No categories found.');
        }

        return Response::structured([
            'data' => $paginator->map(fn (Category $category) => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'posts_count' => $category->posts_count,
            ])->all(),
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }

    /** @return array<string, Type> */
    public function schema(JsonSchema $schema): array
    {
        return [
            'page' => $schema->integer()
                ->description('Page number. Defaults to 1.'),
            'per_page' => $schema->integer()
                ->description('Results per page (1-50). Defaults to 20.'),
        ];
    }
}

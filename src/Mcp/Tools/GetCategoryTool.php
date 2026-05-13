<?php

declare(strict_types=1);

namespace Relaticle\Ink\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;
use Relaticle\Ink\Models\Category;

#[Description('Get a single blog category by ID or slug.')]
#[IsReadOnly]
class GetCategoryTool extends Tool
{
    public function handle(Request $request): Response|ResponseFactory
    {
        if (! $request->user()?->is_admin) {
            return Response::error('Permission denied. Admin access required.');
        }

        if (! $request->user()->tokenCan('categories:read')) {
            return Response::error('Token missing required ability: categories:read');
        }

        $category = null;

        if ($id = $request->get('id')) {
            $category = Category::withCount('posts')->find($id);
        } elseif ($slug = $request->get('slug')) {
            $category = Category::withCount('posts')->where('slug', $slug)->first();
        }

        if (! $category) {
            return Response::error('Category not found. Provide a valid id or slug.');
        }

        return Response::structured([
            'id' => $category->id,
            'name' => $category->name,
            'slug' => $category->slug,
            'posts_count' => $category->posts_count,
            'created_at' => $category->created_at->toIso8601String(),
        ]);
    }

    /** @return array<string, Type> */
    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()
                ->description('The category ID. Provide either id or slug.'),
            'slug' => $schema->string()
                ->description('The category slug. Provide either id or slug.'),
        ];
    }
}

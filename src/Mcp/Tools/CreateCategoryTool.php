<?php

namespace ManukMinasyan\FilamentBlog\Mcp\Tools;

use ManukMinasyan\FilamentBlog\Models\Category;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Create a new blog category. Slug is auto-generated from the name.')]
class CreateCategoryTool extends Tool
{
    public function handle(Request $request): Response|ResponseFactory
    {
        if (! $request->user()?->is_admin) {
            return Response::error('Permission denied. Admin access required.');
        }

        if (! $request->user()->tokenCan('categories:create')) {
            return Response::error('Token missing required ability: categories:create');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ], [
            'name.required' => 'A name is required to create a category.',
        ]);

        $category = Category::create([
            'name' => $validated['name'],
        ]);

        return Response::structured([
            'id' => $category->id,
            'name' => $category->name,
            'slug' => $category->slug,
            'created_at' => $category->created_at->toIso8601String(),
        ]);
    }

    /** @return array<string, \Illuminate\JsonSchema\Types\Type> */
    public function schema(JsonSchema $schema): array
    {
        return [
            'name' => $schema->string()->description('The category name.')->required(),
        ];
    }
}

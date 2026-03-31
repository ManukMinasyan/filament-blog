<?php

namespace ManukMinasyan\FilamentBlog\Mcp\Tools;

use ManukMinasyan\FilamentBlog\Models\Category;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsIdempotent;

#[Description('Update a blog category name by ID.')]
#[IsIdempotent]
class UpdateCategoryTool extends Tool
{
    public function handle(Request $request): Response|ResponseFactory
    {
        if (! $request->user()?->is_admin) {
            return Response::error('Permission denied. Admin access required.');
        }

        if (! $request->user()->tokenCan('categories:update')) {
            return Response::error('Token missing required ability: categories:update');
        }

        $validated = $request->validate([
            'id' => ['required', 'integer'],
            'name' => ['required', 'string', 'max:255'],
        ], [
            'id.required' => 'You must provide the category ID to update.',
            'name.required' => 'A name is required to update a category.',
        ]);

        $category = Category::find($validated['id']);

        if (! $category) {
            return Response::error('Category not found.');
        }

        $category->update([
            'name' => $validated['name'],
        ]);

        return Response::structured([
            'id' => $category->id,
            'name' => $category->name,
            'slug' => $category->slug,
            'updated_at' => $category->updated_at->toIso8601String(),
        ]);
    }

    /** @return array<string, \Illuminate\JsonSchema\Types\Type> */
    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()->description('The category ID to update.')->required(),
            'name' => $schema->string()->description('New category name.')->required(),
        ];
    }
}

<?php

namespace ManukMinasyan\FilamentBlog\Mcp\Tools;

use ManukMinasyan\FilamentBlog\Models\Category;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Restore a previously soft-deleted blog category by ID.')]
class RestoreCategoryTool extends Tool
{
    public function handle(Request $request): Response|ResponseFactory
    {
        if (! $request->user()?->is_admin) {
            return Response::error('Permission denied. Admin access required.');
        }

        if (! $request->user()->tokenCan('categories:delete')) {
            return Response::error('Token missing required ability: categories:delete');
        }

        $validated = $request->validate([
            'id' => ['required', 'integer'],
        ], [
            'id.required' => 'You must provide the category ID to restore.',
        ]);

        $category = Category::withTrashed()->find($validated['id']);

        if (! $category) {
            return Response::error('Category not found.');
        }

        if (! $category->trashed()) {
            return Response::error('Category is not deleted. Nothing to restore.');
        }

        $category->restore();

        return Response::structured([
            'id' => $category->id,
            'name' => $category->name,
            'restored' => true,
            'message' => "Category '{$category->name}' has been restored.",
        ]);
    }

    /** @return array<string, \Illuminate\JsonSchema\Types\Type> */
    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()->description('The ID of the trashed category to restore.')->required(),
        ];
    }
}

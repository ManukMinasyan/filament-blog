<?php

namespace ManukMinasyan\FilamentBlog\Mcp\Tools;

use ManukMinasyan\FilamentBlog\Models\Post;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Soft delete a blog post by ID. The post can be restored later. This does NOT permanently delete.')]
class DeletePostTool extends Tool
{
    public function handle(Request $request): Response|ResponseFactory
    {
        if (! $request->user()?->is_admin) {
            return Response::error('Permission denied. Admin access required.');
        }

        if (! $request->user()->tokenCan('posts:delete')) {
            return Response::error('Token missing required ability: posts:delete');
        }

        $validated = $request->validate([
            'id' => ['required', 'integer'],
        ], [
            'id.required' => 'You must provide the post ID to delete.',
        ]);

        $post = Post::find($validated['id']);

        if (! $post) {
            return Response::error('Post not found.');
        }

        $post->delete();

        return Response::structured([
            'id' => $post->id,
            'title' => $post->title,
            'deleted' => true,
            'message' => "Post '{$post->title}' has been soft deleted. Use restore-post to undo.",
        ]);
    }

    /** @return array<string, \Illuminate\JsonSchema\Types\Type> */
    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()->description('The post ID to soft delete.')->required(),
        ];
    }
}

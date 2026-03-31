<?php

namespace ManukMinasyan\FilamentBlog\Mcp\Tools;

use ManukMinasyan\FilamentBlog\Models\Post;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Restore a previously soft-deleted blog post by ID.')]
class RestorePostTool extends Tool
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
            'id.required' => 'You must provide the post ID to restore.',
        ]);

        $post = Post::withTrashed()->find($validated['id']);

        if (! $post) {
            return Response::error('Post not found.');
        }

        if (! $post->trashed()) {
            return Response::error('Post is not deleted. Nothing to restore.');
        }

        $post->restore();

        return Response::structured([
            'id' => $post->id,
            'title' => $post->title,
            'restored' => true,
            'message' => "Post '{$post->title}' has been restored.",
        ]);
    }

    /** @return array<string, \Illuminate\JsonSchema\Types\Type> */
    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()->description('The ID of the trashed post to restore.')->required(),
        ];
    }
}

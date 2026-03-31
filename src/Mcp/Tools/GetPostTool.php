<?php

namespace ManukMinasyan\FilamentBlog\Mcp\Tools;

use ManukMinasyan\FilamentBlog\Models\Post;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Description('Get a single blog post by ID or slug. Returns full post details including content.')]
#[IsReadOnly]
class GetPostTool extends Tool
{
    public function handle(Request $request): Response|ResponseFactory
    {
        if (! $request->user()?->is_admin) {
            return Response::error('Permission denied. Admin access required.');
        }

        if (! $request->user()->tokenCan('posts:read')) {
            return Response::error('Token missing required ability: posts:read');
        }

        $post = null;

        if ($id = $request->get('id')) {
            $post = Post::with(['category', 'seo'])->find($id);
        } elseif ($slug = $request->get('slug')) {
            $post = Post::with(['category', 'seo'])->where('slug', $slug)->first();
        }

        if (! $post) {
            return Response::error('Post not found. Provide a valid id or slug.');
        }

        return Response::structured([
            'id' => $post->id,
            'title' => $post->title,
            'slug' => $post->slug,
            'content' => $post->content,
            'excerpt' => $post->excerpt,
            'featured_image' => $post->featured_image,
            'status' => $post->status->value,
            'category_id' => $post->category_id,
            'category' => $post->category?->name,
            'author_id' => $post->author_id,
            'seo_title' => $post->seo->title,
            'seo_description' => $post->seo->description,
            'published_at' => $post->published_at?->toIso8601String(),
            'created_at' => $post->created_at->toIso8601String(),
            'updated_at' => $post->updated_at->toIso8601String(),
        ]);
    }

    /** @return array<string, \Illuminate\JsonSchema\Types\Type> */
    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()
                ->description('The post ID. Provide either id or slug.'),
            'slug' => $schema->string()
                ->description('The post slug. Provide either id or slug.'),
        ];
    }
}

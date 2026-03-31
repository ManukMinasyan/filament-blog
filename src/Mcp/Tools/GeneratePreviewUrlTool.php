<?php

namespace ManukMinasyan\FilamentBlog\Mcp\Tools;

use ManukMinasyan\FilamentBlog\Models\Post;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\URL;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Description('Generate a temporary signed preview URL for a blog post. The URL expires in 1 hour and can be opened in a browser to visually verify the post rendering.')]
#[IsReadOnly]
class GeneratePreviewUrlTool extends Tool
{
    public function handle(Request $request): Response
    {
        if (! $request->user()?->is_admin) {
            return Response::error('Permission denied. Admin access required.');
        }

        if (! $request->user()->tokenCan('posts:read')) {
            return Response::error('Token missing required ability: posts:read');
        }

        $post = Post::find($request->get('id'));

        if (! $post) {
            return Response::error('Post not found.');
        }

        $url = URL::temporarySignedRoute('blog.preview', now()->addHour(), ['post' => $post]);

        return Response::text($url);
    }

    /** @return array<string, \Illuminate\JsonSchema\Types\Type> */
    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()
                ->description('The post ID to generate a preview URL for.')
                ->required(),
        ];
    }
}

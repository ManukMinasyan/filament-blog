<?php

namespace ManukMinasyan\FilamentBlog\Mcp\Tools;

use ManukMinasyan\FilamentBlog\Enums\PostStatus;
use ManukMinasyan\FilamentBlog\Models\Post;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsIdempotent;

#[Description('Update an existing blog post by ID. Only provided fields are updated.')]
#[IsIdempotent]
class UpdatePostTool extends Tool
{
    public function handle(Request $request): Response|ResponseFactory
    {
        if (! $request->user()?->is_admin) {
            return Response::error('Permission denied. Admin access required.');
        }

        if (! $request->user()->tokenCan('posts:update')) {
            return Response::error('Token missing required ability: posts:update');
        }

        $validated = $request->validate([
            'id' => ['required', 'integer'],
            'title' => ['nullable', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'category_id' => ['sometimes', 'integer', 'exists:blog_categories,id'],
            'status' => ['nullable', 'string', Rule::enum(PostStatus::class)],
            'published_at' => ['nullable', 'date'],
            'seo_title' => ['nullable', 'string', 'max:60'],
            'seo_description' => ['nullable', 'string', 'max:160'],
        ], [
            'id.required' => 'You must provide the post ID to update.',
        ]);

        $post = Post::find($validated['id']);

        if (! $post) {
            return Response::error('Post not found.');
        }

        $content = isset($validated['content'])
            ? Str::markdown($validated['content'], ['html_input' => 'strip', 'allow_unsafe_links' => false])
            : null;

        $data = array_filter([
            'title' => $validated['title'] ?? null,
            'content' => $content,
            'excerpt' => $validated['excerpt'] ?? null,
            'category_id' => $validated['category_id'] ?? null,
        ], fn ($value) => $value !== null);

        if (isset($validated['status'])) {
            $data['status'] = PostStatus::from($validated['status']);
        }

        if (isset($validated['published_at'])) {
            $data['published_at'] = Carbon::parse($validated['published_at']);
        }

        $post->update($data);

        if (isset($validated['seo_title']) || isset($validated['seo_description'])) {
            $seoData = [];

            if (isset($validated['seo_title'])) {
                $seoData['title'] = $validated['seo_title'];
            }

            if (isset($validated['seo_description'])) {
                $seoData['description'] = $validated['seo_description'];
            }

            $post->seo->update($seoData);
        }

        $post->load('category');

        return Response::structured([
            'id' => $post->id,
            'title' => $post->title,
            'slug' => $post->slug,
            'status' => $post->status->value,
            'category' => $post->category?->name,
            'seo_title' => $post->seo->title,
            'seo_description' => $post->seo->description,
            'published_at' => $post->published_at?->toIso8601String(),
            'updated_at' => $post->updated_at->toIso8601String(),
        ]);
    }

    /** @return array<string, \Illuminate\JsonSchema\Types\Type> */
    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()->description('The post ID to update.')->required(),
            'title' => $schema->string()->description('New title.'),
            'content' => $schema->string()->description('New content in Markdown. Converted to HTML on save.'),
            'excerpt' => $schema->string()->description('New excerpt.'),
            'category_id' => $schema->integer()->description('New category ID.'),
            'status' => $schema->string()->enum(array_column(PostStatus::cases(), 'value'))->description('New status.'),
            'published_at' => $schema->string()->description('New publish date (ISO 8601).'),
            'seo_title' => $schema->string()->description('Custom SEO meta title (max 60 chars).'),
            'seo_description' => $schema->string()->description('Custom SEO meta description (max 160 chars).'),
        ];
    }
}

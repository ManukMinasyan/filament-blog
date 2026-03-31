<?php

declare(strict_types=1);

namespace ManukMinasyan\FilamentBlog\Components;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Illuminate\View\Component;
use ManukMinasyan\FilamentBlog\Models\Post;

class StructuredData extends Component
{
    /** @var array<string, mixed> */
    public array $schema;

    public function __construct(
        public Post $post,
    ) {
        $this->schema = $this->buildSchema();
    }

    public function render(): View
    {
        return view('blog::components.structured-data');
    }

    /** @return array<string, mixed> */
    private function buildSchema(): array
    {
        $publisherConfig = config('filament-blog.publisher');

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'headline' => $this->post->title,
            'description' => $this->post->excerpt,
            'datePublished' => $this->post->published_at?->toIso8601String(),
            'dateModified' => $this->post->updated_at?->toIso8601String(),
            'articleBody' => Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags($this->post->content))), 200),
        ];

        if ($this->post->author) {
            $schema['author'] = [
                '@type' => 'Person',
                'name' => $this->post->author->name,
            ];
        }

        if ($this->post->featured_image) {
            $schema['image'] = asset("storage/{$this->post->featured_image}");
        }

        if ($this->post->category) {
            $schema['articleSection'] = $this->post->category->name;
        }

        if ($publisherConfig['name']) {
            $schema['publisher'] = [
                '@type' => 'Organization',
                'name' => $publisherConfig['name'],
                'url' => $publisherConfig['url'],
            ];

            if ($publisherConfig['logo']) {
                $schema['publisher']['logo'] = [
                    '@type' => 'ImageObject',
                    'url' => asset($publisherConfig['logo']),
                ];
            }
        }

        return $schema;
    }
}

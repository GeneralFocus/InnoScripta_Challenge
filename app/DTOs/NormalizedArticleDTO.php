<?php

declare(strict_types=1);

namespace App\DTOs;

class NormalizedArticleDTO
{
    public function __construct(
        public readonly string $externalId,
        public readonly string $title,
        public readonly ?string $description,
        public readonly ?string $content,
        public readonly ?string $author,
        public readonly string $url,
        public readonly ?string $imageUrl,
        public readonly string $publishedAt,
        public readonly string $sourceName,
        public readonly string $provider,
        public readonly ?string $categoryName = null,
    ) {
    }

    public function toArray(): array
    {
        return [
            'external_id' => $this->externalId,
            'title' => $this->title,
            'description' => $this->description,
            'content' => $this->content,
            'author' => $this->author,
            'url' => $this->url,
            'image_url' => $this->imageUrl,
            'published_at' => $this->publishedAt,
            'source_name' => $this->sourceName,
            'provider' => $this->provider,
            'category_name' => $this->categoryName,
        ];
    }
}

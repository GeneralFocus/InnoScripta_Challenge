<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'title'       => $this->title,
            'summary'     => $this->summary,
            'content'     => $this->content,
            'source'      => $this->source?->name ?? $this->source,
            'category'    => $this->category?->name ?? $this->category,
            'author'      => $this->author,
            'url'         => $this->url,
            'image_url'   => $this->image_url,
            'published_at' => $this->published_at,
        ];
    }
}

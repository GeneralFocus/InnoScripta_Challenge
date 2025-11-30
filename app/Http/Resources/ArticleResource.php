<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'title'       => $this->title,
            'description' => $this->description,
            'content'     => $this->content,
            'source'      => $this->source?->name,
            'category'    => $this->category?->name,
            'author'      => $this->author,
            'url'         => $this->url,
            'image_url'   => $this->image_url,
            'published_at' => $this->published_at,
        ];
    }
}

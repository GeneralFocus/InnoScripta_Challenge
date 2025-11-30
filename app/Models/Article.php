<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Article extends Model
{
    protected $fillable = [
        'external_id',
        'source_id',
        'category_id',
        'title',
        'description',
        'content',
        'author',
        'url',
        'image_url',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function source(): BelongsTo
    {
        return $this->belongsTo(Source::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (empty($search)) {
            return $query;
        }

        return $query->whereFullText(['title', 'description', 'content'], $search);
    }

    public function scopeFilterBySource(Builder $query, ?string $source): Builder
    {
        if (empty($source)) {
            return $query;
        }

        return $query->whereHas('source', function (Builder $q) use ($source) {
            $q->where('slug', $source);
        });
    }

    public function scopeFilterByCategory(Builder $query, ?string $category): Builder
    {
        if (empty($category)) {
            return $query;
        }

        return $query->whereHas('category', function (Builder $q) use ($category) {
            $q->where('slug', $category);
        });
    }

    public function scopeFilterByAuthor(Builder $query, ?string $author): Builder
    {
        if (empty($author)) {
            return $query;
        }

        return $query->where('author', 'like', '%' . $author . '%');
    }

    public function scopeFilterByDateRange(Builder $query, ?string $dateFrom, ?string $dateTo): Builder
    {
        if ($dateFrom) {
            $query->where('published_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->where('published_at', '<=', $dateTo);
        }

        return $query;
    }
}

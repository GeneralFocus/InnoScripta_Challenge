<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\ArticleRepositoryInterface;
use App\Models\Article;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ArticleRepository implements ArticleRepositoryInterface
{
    public function getPaginated(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = Article::with(['source', 'category'])
            ->search($filters['search'] ?? null)
            ->filterBySource($filters['source'] ?? null)
            ->filterByCategory($filters['category'] ?? null)
            ->filterByAuthor($filters['author'] ?? null)
            ->filterByDateRange(
                $filters['date_from'] ?? null,
                $filters['date_to'] ?? null
            )
            ->orderBy('published_at', 'desc');

        return $query->paginate($perPage);
    }

    public function find(int $id): ?Article
    {
        return Article::with(['source', 'category'])->find($id);
    }

    public function upsert(array $data): Article
    {
        return Article::updateOrCreate(
            ['external_id' => $data['external_id']],
            $data
        );
    }

    public function existsByExternalId(string $externalId): bool
    {
        return Article::where('external_id', $externalId)->exists();
    }

    public function existsByUrl(string $url): bool
    {
        return Article::where('url', $url)->exists();
    }
}

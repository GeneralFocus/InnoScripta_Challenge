<?php

declare(strict_types=1);

namespace App\Contracts\Repositories;

use App\Models\Article;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ArticleRepositoryInterface
{
    public function getPaginated(array $filters, int $perPage = 15): LengthAwarePaginator;

    public function find(int $id): ?Article;

    public function upsert(array $data): Article;

    public function existsByExternalId(string $externalId): bool;
}

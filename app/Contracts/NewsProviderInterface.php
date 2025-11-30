<?php

declare(strict_types=1);

namespace App\Contracts;

use App\DTOs\NormalizedArticleDTO;

interface NewsProviderInterface
{
    public function fetchArticles(): array;

    public function getProviderName(): string;
}

<?php

declare(strict_types=1);

namespace App\Contracts;

interface NewsProviderInterface
{
    public function fetchArticles(): array;

    public function getProviderName(): string;
}

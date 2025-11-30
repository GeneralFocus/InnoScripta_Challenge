<?php

declare(strict_types=1);

namespace App\Services\NewsProviders;

use App\DTOs\NormalizedArticleDTO;
use Carbon\Carbon;

class NewsApiProvider extends AbstractNewsProvider
{
    protected function getApiKey(): string
    {
        return (string) config('news.providers.newsapi.api_key');
    }

    protected function getBaseUrl(): string
    {
        return (string) config('news.providers.newsapi.base_url');
    }

    public function getProviderName(): string
    {
        return 'newsapi';
    }

    public function fetchArticles(): array
    {
        // Respect "enabled" flag
        if (!config('news.providers.newsapi.enabled')) {
            return [];
        }

        $url = $this->getBaseUrl() . '/top-headlines';

        $data = $this->makeRequest($url, [
            'apiKey'   => $this->getApiKey(),
            'language' => 'en',
            'pageSize' => (int) config('news.fetch.max_articles_per_provider', 100),
        ]);

        if (!$data || !isset($data['articles']) || !is_array($data['articles'])) {
            return [];
        }

        $normalized = [];
        foreach ($data['articles'] as $article) {
            if (!is_array($article)) {
                continue;
            }

            $normalized[] = $this->normalize($article);
        }

        return $normalized;
    }

    private function normalize(array $article): NormalizedArticleDTO
    {
        $sourceName = $article['source']['name'] ?? 'NewsAPI Source';

        return new NormalizedArticleDTO(
            externalId: 'newsapi_' . sha1(($article['url'] ?? '') . ($article['publishedAt'] ?? uniqid())),
            title: $article['title'] ?? 'Untitled',
            description: $article['description'] ?? null,
            content: $article['content'] ?? $article['description'] ?? null,
            author: $article['author'] ?? null,
            url: $article['url'] ?? '',
            imageUrl: $article['urlToImage'] ?? null,
            publishedAt: $this->parseDate($article['publishedAt'] ?? null),
            sourceName: $sourceName,
            provider: $this->getProviderName(),
            categoryName: null,
        );
    }

    private function parseDate(?string $date): string
    {
        if (!$date) {
            return now()->toDateTimeString();
        }

        try {
            return Carbon::parse($date)->toDateTimeString();
        } catch (\Exception) {
            return now()->toDateTimeString();
        }
    }
}

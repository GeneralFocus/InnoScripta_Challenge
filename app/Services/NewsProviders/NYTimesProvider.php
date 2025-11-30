<?php

declare(strict_types=1);

namespace App\Services\NewsProviders;

use App\DTOs\NormalizedArticleDTO;
use Carbon\Carbon;

class NYTimesProvider extends AbstractNewsProvider
{
    protected function getApiKey(): string
    {
        return (string) config('news.providers.nytimes.api_key');
    }

    protected function getBaseUrl(): string
    {
        return (string) config('news.providers.nytimes.base_url');
    }

    public function getProviderName(): string
    {
        return 'nytimes';
    }

    public function fetchArticles(): array
    {
        if (!config('news.providers.nytimes.enabled')) {
            return [];
        }

        $url = $this->getBaseUrl() . '/topstories/v2/home.json';

        $data = $this->makeRequest($url, [
            'api-key' => $this->getApiKey(),
        ]);

        if (!$data || !isset($data['results']) || !is_array($data['results'])) {
            return [];
        }

        $normalized = [];
        foreach ($data['results'] as $article) {
            if (!is_array($article)) {
                continue;
            }

            $normalized[] = $this->normalize($article);
        }

        return $normalized;
    }

    private function normalize(array $article): NormalizedArticleDTO
    {
        $imageUrl = null;
        if (isset($article['multimedia'][0]['url'])) {
            $imageUrl = $article['multimedia'][0]['url'];
        }

        return new NormalizedArticleDTO(
            externalId: 'nytimes_' . ($article['uri'] ?? uniqid()),
            title: $article['title'] ?? 'Untitled',
            description: $article['abstract'] ?? null,
            content: $article['abstract'] ?? null,
            author: $article['byline'] ?? null,
            url: $article['url'] ?? '',
            imageUrl: $imageUrl,
            publishedAt: $this->parseDate($article['published_date'] ?? null),
            sourceName: 'The New York Times',
            provider: $this->getProviderName(),
            categoryName: $article['section'] ?? null,
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

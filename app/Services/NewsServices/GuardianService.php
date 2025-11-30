<?php

declare(strict_types=1);

namespace App\Services\NewsServices;

use App\DTOs\NormalizedArticleDTO;
use Carbon\Carbon;

class GuardianService extends AbstractNewsService
{
    protected function getApiKey(): string
    {
        return (string) config('news.providers.guardian.api_key');
    }

    protected function getBaseUrl(): string
    {
        return (string) config('news.providers.guardian.base_url');
    }

    public function getProviderName(): string
    {
        return 'guardian';
    }

    public function fetchArticles(): array
    {
        if (!config('news.providers.guardian.enabled')) {
            return [];
        }

        $url = $this->getBaseUrl() . '/search';

        $data = $this->makeRequest($url, [
            'api-key'     => $this->getApiKey(),
            'show-fields' => 'trailText,body,thumbnail,byline',
            'page-size'   => (int) config('news.fetch.max_articles_per_provider', 100),
            'order-by'    => 'newest',
        ]);

        if (
            !$data
            || !isset($data['response']['results'])
            || !is_array($data['response']['results'])
        ) {
            return [];
        }

        $normalized = [];
        foreach ($data['response']['results'] as $article) {
            if (!is_array($article)) {
                continue;
            }

            $normalized[] = $this->normalize($article);
        }

        return $normalized;
    }

    private function normalize(array $article): NormalizedArticleDTO
    {
        $fields = $article['fields'] ?? [];

        return new NormalizedArticleDTO(
            externalId: 'guardian_' . ($article['id'] ?? uniqid()),
            title: $article['webTitle'] ?? 'Untitled',
            description: $fields['trailText'] ?? null,
            content: $fields['body'] ?? $fields['trailText'] ?? null,
            author: $fields['byline'] ?? null,
            url: $article['webUrl'] ?? '',
            imageUrl: $fields['thumbnail'] ?? null,
            publishedAt: $this->parseDate($article['webPublicationDate'] ?? null),
            sourceName: 'The Guardian',
            provider: $this->getProviderName(),
            categoryName: $article['sectionName'] ?? null,
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

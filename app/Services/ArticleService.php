<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\NewsProviderInterface;
use App\Contracts\Repositories\ArticleRepositoryInterface;
use App\DTOs\NormalizedArticleDTO;
use App\Models\Category;
use App\Models\Source;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ArticleService
{
    public function __construct(
        private readonly ArticleRepositoryInterface $repository,
        private readonly array $providers = []
    ) {
    }

    public function getArticles(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getPaginated($filters, $perPage);
    }

    public function getArticle(int $id): ?array
    {
        $article = $this->repository->find($id);

        return $article?->load(['source', 'category'])->toArray();
    }

    public function fetchFromAllProviders(): array
    {
        $results = [
            'total_fetched' => 0,
            'total_saved' => 0,
            'total_skipped' => 0,
            'providers' => [],
        ];

        foreach ($this->providers as $provider) {
            $providerResult = $this->fetchFromProvider($provider);
            $results['providers'][$provider->getProviderName()] = $providerResult;
            $results['total_fetched'] += $providerResult['fetched'];
            $results['total_saved'] += $providerResult['saved'];
            $results['total_skipped'] += $providerResult['skipped'];
        }

        return $results;
    }

    private function fetchFromProvider(NewsProviderInterface $provider): array
    {
        $providerName = $provider->getProviderName();

        Log::info("Fetching articles from {$providerName}");

        try {
            $articles = $provider->fetchArticles();
            $fetched = count($articles);
            $saved = 0;
            $skipped = 0;

            foreach ($articles as $dto) {
                if ($this->repository->existsByExternalId($dto->externalId)) {
                    $skipped++;
                    continue;
                }

                $this->persistArticle($dto);
                $saved++;
            }

            Log::info("{$providerName}: Fetched {$fetched}, Saved {$saved}, Skipped {$skipped}");

            return [
                'fetched' => $fetched,
                'saved' => $saved,
                'skipped' => $skipped,
            ];
        } catch (\Exception $e) {
            Log::error("{$providerName} fetch failed: {$e->getMessage()}");

            return [
                'fetched' => 0,
                'saved' => 0,
                'skipped' => 0,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function persistArticle(NormalizedArticleDTO $dto): void
    {
        DB::transaction(function () use ($dto) {
            $source = $this->getOrCreateSource($dto->sourceName, $dto->provider);
            $category = $dto->categoryName
                ? $this->getOrCreateCategory($dto->categoryName)
                : null;

            $this->repository->upsert([
                'external_id' => $dto->externalId,
                'source_id' => $source->id,
                'category_id' => $category?->id,
                'title' => $dto->title,
                'description' => $dto->description,
                'content' => $dto->content,
                'author' => $dto->author,
                'url' => $dto->url,
                'image_url' => $dto->imageUrl,
                'published_at' => $dto->publishedAt,
            ]);
        });
    }

    private function getOrCreateSource(string $name, string $provider): Source
    {
        $slug = Str::slug($name);

        return Source::firstOrCreate(
            ['slug' => $slug],
            [
                'name' => $name,
                'provider' => $provider,
            ]
        );
    }

    private function getOrCreateCategory(string $name): Category
    {
        $slug = Str::slug($name);

        return Category::firstOrCreate(
            ['slug' => $slug],
            ['name' => $name]
        );
    }
}

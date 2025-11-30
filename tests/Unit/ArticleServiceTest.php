<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Contracts\NewsProviderInterface;
use App\Contracts\Repositories\ArticleRepositoryInterface;
use App\DTOs\NormalizedArticleDTO;
use App\Services\ArticleService;
use Mockery;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ArticleServiceTest extends TestCase
{
    #[Test]
    public function test_fetch_from_all_providers(): void
    {
        $provider = Mockery::mock(NewsProviderInterface::class);
        $provider->shouldReceive('getProviderName')
            ->andReturn('mockprovider');
        $provider->shouldReceive('fetchArticles')
            ->andReturn([
                new NormalizedArticleDTO(
                    externalId: 'demo',
                    title: 'Test Title',
                    description: 'Test Description',
                    content: 'Test Content',
                    author: 'Oyinkansola Olabode',
                    url: 'https://example.com',
                    imageUrl: null,
                    publishedAt: now()->toDateTimeString(),
                    sourceName: 'Demo Source',
                    provider: 'demoprovider',
                    categoryName: 'Tech'
                )
            ]);

        $repo = Mockery::mock(ArticleRepositoryInterface::class);
        $repo->shouldReceive('existsByExternalId')->andReturn(false);
        $repo->shouldReceive('upsert')->andReturn(true);

        $service = Mockery::mock(ArticleService::class, [
            $repo,
            [$provider],
        ])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();

        $service->shouldReceive('persistArticle')->andReturnNull();

        $result = $service->fetchFromAllProviders();

        $this->assertEquals(1, $result['total_fetched']);
        $this->assertEquals(1, $result['total_saved']);
        $this->assertEquals(0, $result['total_skipped']);
    }
}

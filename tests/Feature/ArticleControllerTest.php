<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use App\Models\Source;
use App\Services\ArticleService;
use Illuminate\Pagination\LengthAwarePaginator;
use Mockery;
use Tests\TestCase;

class ArticleControllerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_index_returns_paginated_articles(): void
    {
        $article = new Article();
        $article->forceFill([
            'id' => 1,
            'title' => 'Sample',
            'description' => 'Summary',
            'content' => 'Full content',
            'author' => 'Author',
            'url' => 'https://example.com/article',
            'image_url' => null,
            'published_at' => now(),
        ]);
        $article->setRelation('source', new Source(['name' => 'Demo Source']));
        $article->setRelation('category', new Category(['name' => 'Demo Category']));

        $paginator = new LengthAwarePaginator([$article], 1, 15);
        $paginator->setPath(url('/api/v1/articles'));

        $service = Mockery::mock(ArticleService::class);
        $service->shouldReceive('getArticles')
            ->once()
            ->andReturn($paginator);
        $this->app->instance(ArticleService::class, $service);

        $response = $this->getJson('/api/v1/articles');

        $response->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.data.0.title', 'Sample')
            ->assertJsonPath('data.links.first', url('/api/v1/articles?page=1'));
    }

    public function test_show_returns_not_found_when_article_missing(): void
    {
        $service = Mockery::mock(ArticleService::class);
        $service->shouldReceive('getArticle')
            ->once()
            ->with(1)
            ->andReturn(null);
        $this->app->instance(ArticleService::class, $service);

        $response = $this->getJson('/api/v1/articles/1');

        $response->assertNotFound()
            ->assertJsonPath('status', 'error');
    }

    public function test_show_rejects_non_numeric_id(): void
    {
        $service = Mockery::mock(ArticleService::class);
        $service->shouldNotReceive('getArticle');
        $this->app->instance(ArticleService::class, $service);

        $response = $this->getJson('/api/v1/articles/abc');

        $response->assertStatus(422)
            ->assertJsonPath('status', 'error')
            ->assertJsonPath('message', 'Invalid article id');
    }
}

<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\NewsServices\NewsApiService;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Tests\TestCase;

class NewsApiProviderTest extends TestCase
{
    public function test_newsapi_returns_normalized_articles(): void
    {
        config()->set('news.providers.newsapi.enabled', true);
        config()->set('news.providers.newsapi.base_url', 'https://newsapi.test');
        config()->set('news.providers.newsapi.api_key', 'demo-key');

        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'articles' => [
                    [
                        'title' => 'Sample News',
                        'description' => 'Description',
                        'content' => 'Content',
                        'author' => 'John Doe',
                        'url' => 'https://example.com',
                        'urlToImage' => null,
                        'publishedAt' => now()->toISOString(),
                        'source' => ['name' => 'CNN'],
                    ],
                ],
            ])),
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $provider = new NewsApiService($client);

        $articles = $provider->fetchArticles();

        $this->assertCount(1, $articles);
        $this->assertEquals('Sample News', $articles[0]->title);
        $this->assertEquals('newsapi', $articles[0]->provider);
    }
}

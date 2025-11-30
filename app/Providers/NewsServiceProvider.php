<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\Repositories\ArticleRepositoryInterface;
use App\Repositories\ArticleRepository;
use App\Services\ArticleService;
use App\Services\NewsProviders\GuardianProvider;
use App\Services\NewsProviders\NewsApiProvider;
use App\Services\NewsProviders\NYTimesProvider;
use Illuminate\Support\ServiceProvider;

class NewsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            ArticleRepositoryInterface::class,
            ArticleRepository::class
        );

        $this->app->tag([
            NewsApiProvider::class,
            GuardianProvider::class,
            NYTimesProvider::class,
        ], 'news.providers');

        $this->app->singleton(ArticleService::class, function ($app) {
            $providers = $app->tagged('news.providers');

            return new ArticleService(
                repository: $app->make(ArticleRepositoryInterface::class),
                providers: iterator_to_array($providers)
            );
        });
    }

    public function boot(): void
    {
        //
    }
}

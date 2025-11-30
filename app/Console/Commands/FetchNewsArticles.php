<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\ArticleService;
use Illuminate\Console\Command;

class FetchNewsArticles extends Command
{
    protected $signature = 'news:fetch';

    protected $description = 'Fetch articles from all news providers';

    public function __construct(private readonly ArticleService $service)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Starting to fetch articles from all providers...');

        $results = $this->service->fetchFromAllProviders();

        $this->newLine();
        $this->info('=== Fetch Summary ===');
        $this->line("Total Fetched: {$results['total_fetched']}");
        $this->line("Total Saved: {$results['total_saved']}");
        $this->line("Total Skipped: {$results['total_skipped']}");
        $this->newLine();
        $this->info('=== Provider Details ===');

        $failedProviders = [];
        foreach ($results['providers'] as $provider => $stats) {
            if (isset($stats['error'])) {
                $failedProviders[] = $provider;
                $this->error("{$provider}: Failed - {$stats['error']}");
            } else {
                $this->line("{$provider}: Fetched {$stats['fetched']}, Saved {$stats['saved']}, Skipped {$stats['skipped']}");
            }
        }

        $this->newLine();
        if (!empty($failedProviders)) {
            $this->warn('Article fetch completed with errors.');
            return self::FAILURE;
        }

        $this->info('Article fetch completed!');
        return self::SUCCESS;
    }
}

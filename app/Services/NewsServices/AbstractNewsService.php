<?php

declare(strict_types=1);

namespace App\Services\NewsServices;

use App\Contracts\NewsProviderInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

abstract class AbstractNewsService implements NewsProviderInterface
{
    protected Client $client;

    public function __construct(?Client $client = null)
    {
        $timeout = (float) config('news.fetch.timeout', 30);

        $this->client = $client ?? new Client([
            'timeout' => $timeout,
            'connect_timeout' => min($timeout, 10.0),
            'http_errors' => false,
        ]);
    }

    protected function makeRequest(string $url, array $params = []): ?array
    {
        try {
            $response = $this->client->get($url, [
                'query' => $params,
            ]);

            $statusCode = $response->getStatusCode();

            if ($statusCode !== 200) {
                Log::warning("{$this->getProviderName()} API returned status {$statusCode}");
                return null;
            }

            $body = json_decode((string) $response->getBody(), true);

            if (!is_array($body)) {
                Log::error("{$this->getProviderName()} API returned invalid JSON");
                return null;
            }

            return $body;
        } catch (GuzzleException $e) {
            Log::error("{$this->getProviderName()} API request failed: {$e->getMessage()}");
            return null;
        }
    }

    abstract protected function getApiKey(): string;

    abstract protected function getBaseUrl(): string;
}

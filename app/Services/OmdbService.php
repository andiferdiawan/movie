<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Thin wrapper around the OMDb API (http://www.omdbapi.com/).
 *
 * Responses are cached for a configurable amount of time so that repeated
 * searches / detail views do not needlessly burn through the free API quota.
 */
class OmdbService
{
    /** @var Client */
    protected $client;

    /** @var string */
    protected $apiKey;

    /** @var string */
    protected $baseUrl;

    /** @var int */
    protected $cacheMinutes;

    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->apiKey = config('services.omdb.api_key');
        $this->baseUrl = config('services.omdb.base_url');
        $this->cacheMinutes = (int) config('services.omdb.cache_minutes', 60);
    }

    /**
     * Search movies/series by keyword, with optional type & year filters.
     *
     * @param  string  $keyword
     * @param  int  $page
     * @param  string|null  $type    movie|series|episode
     * @param  string|null  $year
     * @return array
     */
    public function search(string $keyword, int $page = 1, ?string $type = null, ?string $year = null): array
    {
        $params = [
            's' => $keyword,
            'page' => $page,
        ];

        if (! empty($type)) {
            $params['type'] = $type;
        }

        if (! empty($year)) {
            $params['y'] = $year;
        }

        return $this->request($params);
    }

    /**
     * Fetch full details for a single title by its IMDb id.
     *
     * @param  string  $imdbId
     * @return array
     */
    public function find(string $imdbId): array
    {
        return $this->request([
            'i' => $imdbId,
            'plot' => 'full',
        ]);
    }

    /**
     * Perform the (cached) HTTP request against OMDb.
     *
     * @param  array  $params
     * @return array
     */
    protected function request(array $params): array
    {
        $params['apikey'] = $this->apiKey;

        $cacheKey = 'omdb:' . md5(json_encode($params));

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            $response = $this->client->get($this->baseUrl, [
                'query' => $params,
                'timeout' => 10,
            ]);

            $data = json_decode((string) $response->getBody(), true) ?? [];
        } catch (GuzzleException $e) {
            Log::error('OMDb API request failed: ' . $e->getMessage());

            // Network / API failures are not cached so the next request can retry.
            return [
                'Response' => 'False',
                'Error' => 'Unable to reach the OMDb API. Please try again later.',
            ];
        }

        Cache::put($cacheKey, $data, now()->addMinutes($this->cacheMinutes));

        return $data;
    }
}

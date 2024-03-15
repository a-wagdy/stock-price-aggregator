<?php

namespace App\Stock\API;

use Mockery\Exception;

class AlphaVantageAPI
{
    public const API_ENDPOINT = 'https://alphavantage.co/query';

    /**
     * Get the quote endpoint URL
     *
     * @param string $symbol
     * @return string
     */
    public function getQuoteEndpoint(string $symbol): string
    {
        $queryStrings = [
            'symbol' => $symbol,
            'function' => 'GLOBAL_QUOTE',
            'apikey' => env('ALPHA_VANTAGE_KEY'),
        ];

        return $this->buildApiUrl($queryStrings);
    }

    /**
     * Build the URL to respect the query strings
     *
     * @param array $queryStrings
     * @return string
     */
    public function buildApiUrl(array $queryStrings): string
    {
        $queryString = http_build_query($queryStrings);

        return static::API_ENDPOINT . '?' . $queryString;
    }

    /**
     * Get the content of the endpoint
     *
     * @param string $endpoint
     * @return mixed
     */
    public function getContents(string $endpoint): mixed
    {
        $content = file_get_contents($endpoint);

        if (!$content) {
            throw new Exception('Error getting the content');
        }

        return json_decode($content,true);
    }
}
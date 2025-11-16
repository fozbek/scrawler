<?php

namespace Scrawler\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

/**
 * HTTP client wrapper for fetching web pages.
 */
class HttpClient
{
    private Client $client;

    private const DEFAULT_USER_AGENT = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.36";

    public function __construct(?Client $client = null)
    {
        $this->client = $client ?? new Client();
    }

    /**
     * Fetch the content of a URL.
     *
     * @param string $url The URL to fetch
     * @param array<string, string> $headers Additional headers to send
     * @return string The HTML content
     * @throws GuzzleException If the request fails
     */
    public function fetch(string $url, array $headers = []): string
    {
        $defaultHeaders = [
            "User-Agent" => self::DEFAULT_USER_AGENT,
        ];

        $response = $this->client->request("GET", $url, [
            "headers" => array_merge($defaultHeaders, $headers),
        ]);

        return $response->getBody()->getContents();
    }

    /**
     * Get the underlying Guzzle client.
     *
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }
}

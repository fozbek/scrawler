<?php

namespace Scrawler;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class RequestHelper
{
    /**
     * @var Client
     */
    private Client $client;

    /**
     * RequestHelper constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $url
     * @return string
     * @throws GuzzleException
     * @throws Exception
     */
    public function GET(string $url): string
    {
        try {
            $response = $this->getClient()->request('GET', $url, [
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.36'
                ]
            ]);

            return $response->getBody()->getContents();
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * @return Client
     */
    private function getClient(): Client
    {
        return $this->client;
    }
}

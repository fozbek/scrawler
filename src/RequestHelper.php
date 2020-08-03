<?php

namespace Scrawler;

use GuzzleHttp\Client;

class RequestHelper
{
    /**
     * @var ?Client
     */
    private $client;
    /**
     * @var array<string, mixed>
     */
    private $options;

    /**
     * RequestHelper constructor.
     * @param array<string, mixed> $params
     * @param ?Client $client
     */
    public function __construct(array $params = [], ?Client $client = null)
    {
        $this->client = $client;
        $this->options = $params;
    }

    /**
     * @param string $url
     * @return false|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function GET(string $url)
    {
        try {
            $response = $this->getClient()->request('GET', $url, $this->options);

            return $response->getBody()->getContents();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @return Client
     */
    private function getClient(): Client
    {
        if ($this->client != null) {
            return $this->client;
        }

        $this->client = new Client();

        if (array_key_exists('client', $this->options)) {
            $this->client = $this->options['client'];
        }

        return $this->client;
    }
}

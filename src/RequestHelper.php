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
     * RequestHelper constructor.
     * @param ?Client $client
     */
    public function __construct(?Client $client = null)
    {
        $this->client = $client;
    }

    /**
     * @param string $url
     * @return false|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function GET(string $url)
    {
        try {
            $response = $this->getClient()->request('GET', $url);

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
        if (!empty($this->client)) {
            return $this->client;
        }

        $this->client = new Client();

        return $this->client;
    }
}

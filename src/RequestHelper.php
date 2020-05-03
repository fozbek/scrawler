<?php

namespace Scrawler;

use GuzzleHttp\Client;

class RequestHelper
{
    /**
     * @var Client
     */
    private $client;
    /**
     * @var array
     */
    private $options;

    public function __construct($params = [], Client $client = null)
    {
        $this->client = $client;
        $this->options = $params;
    }

    public function GET($url)
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

<?php

namespace Scrawler;

use GuzzleHttp\Client;

class RequestHelper
{
    private $client = null;
    private $options = [];

    public function __construct($params)
    {
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

    private function getClient()
    {
        if ($this->client == null) {
            $this->client = new Client();
        }

        if (array_key_exists('client', $this->options)) {
            $this->client = $this->options['client'];
        }

        return $this->client;
    }
}

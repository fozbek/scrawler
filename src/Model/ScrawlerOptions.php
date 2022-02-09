<?php

namespace Scrawler\Model;

use GuzzleHttp\Client;

class ScrawlerOptions
{
    /**
     * @var Client
     */
    private $guzzleClient;

    /**
     * ScrawlerOptions constructor.
     */
    public function __construct(Client $client = null)
    {
        if (null !== $client) {
            $this->setGuzzleClient($client);
        }
    }

    /**
     * @return Client
     */
    public function getGuzzleClient()
    {
        return $this->guzzleClient;
    }

    /**
     * @param Client $client
     * @return ScrawlerOptions
     */
    public function setGuzzleClient(Client $client): ScrawlerOptions
    {
        $this->guzzleClient = $client;

        return $this;
    }
}

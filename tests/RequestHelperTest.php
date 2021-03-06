<?php

namespace Scrawler;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class RequestHelperTest extends TestCase
{
    /**
     * @var RequestHelper
     */
    private RequestHelper $helper;
    /**
     * @var RequestHelper
     */
    private RequestHelper $helperWithoutCustomClient;

    protected function setUp(): void
    {
        $mock = new MockHandler([
            new Response(200, ['X-Foo' => 'Bar'], 'Hello, World'),
            new Response(202, ['Content-Length' => 0]),
            new RequestException('Error Communicating with Server', new Request('GET', 'test'))
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);
        $this->helper = new RequestHelper($client);

        $this->helperWithoutCustomClient = new RequestHelper(new Client());
    }

    /**
     * @throws GuzzleException
     */
    public function testGET(): void
    {
        $response = $this->helper->GET('/');
        self::assertEquals('Hello, World', $response);

        $response = $this->helper->GET('/');
        self::assertEmpty($response);

        $this->expectException(RequestException::class);
        $this->helper->GET('/');

        $response = $this->helperWithoutCustomClient->GET('https://google.com/');
        self::assertNotEmpty($response);

        $this->expectException(ConnectException::class);
        $this->helperWithoutCustomClient->GET('someWrongHostname');
    }
}

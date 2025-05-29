<?php

namespace Scrawler;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ConnectException;
use PHPUnit\Framework\TestCase;

class RequestHelperTest extends TestCase
{
    public function testGETReturnsBodyOn200()
    {
        $mock = new MockHandler([
            new Response(200, [], 'Hello'),
        ]);
        $client = new Client(['handler' => HandlerStack::create($mock)]);
        $helper = new RequestHelper($client);
        $this->assertEquals('Hello', $helper->GET('/'));
    }

    public function testGETReturnsEmptyOn202()
    {
        $mock = new MockHandler([
            new Response(202, []),
        ]);
        $client = new Client(['handler' => HandlerStack::create($mock)]);
        $helper = new RequestHelper($client);
        $this->assertEmpty($helper->GET('/'));
    }

    public function testGETThrowsOnError()
    {
        $mock = new MockHandler([
            new RequestException('Error', new Request('GET', '/')),
        ]);
        $client = new Client(['handler' => HandlerStack::create($mock)]);
        $helper = new RequestHelper($client);
        $this->expectException(RequestException::class);
        $helper->GET('/');
    }

    public function testGETRealRequest()
    {
        $mock = new MockHandler([
            new Response(200, [], '{"url": "https://httpbin.org/get"}'),
        ]);
        $client = new Client(['handler' => HandlerStack::create($mock)]);
        $helper = new RequestHelper($client);
        $response = $helper->GET('https://httpbin.org/get');
        $this->assertStringContainsString('httpbin', $response);
    }

    public function testGETThrowsOnInvalidHost()
    {
        $helper = new RequestHelper(new Client());
        $this->expectException(ConnectException::class);
        $helper->GET('http://nonexistent-host-12345');
    }
}

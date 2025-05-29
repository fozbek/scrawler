<?php

use PHPUnit\Framework\TestCase;
use Scrawler\Scrawler;

class ScrawlerTest extends TestCase
{
    public function testScrapeHtml()
    {
        $html = '<div><span>bar</span></div>';
        $scrawler = new Scrawler();
        $schema = ['foo' => 'span'];
        $result = $scrawler->scrape($html, $schema, true);
        $this->assertEquals(['foo' => 'bar'], $result);
    }

    public function testScrapeUrl()
    {
        $scrawler = new Scrawler();
        $schema = ['foo' => 'title'];
        $result = $scrawler->scrape('https://example.com', $schema, false);
        $this->assertArrayHasKey('foo', $result);
    }
}

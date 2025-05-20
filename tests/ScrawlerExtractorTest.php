<?php

use PHPUnit\Framework\TestCase;
use Scrawler\ScrawlerExtractor;
use DiDom\Document;

class ScrawlerExtractorTest extends TestCase
{
    public function testExtractTextAndAttribute()
    {
        $html = '<div><a href="/foo">Link</a><span>Text</span></div>';
        $doc = new Document($html);
        $schema = [
            'link' => 'a@href',
            'text' => 'span',
        ];
        $result = ScrawlerExtractor::extract($doc, $schema);
        $this->assertEquals('/foo', $result['link']);
        $this->assertEquals('Text', $result['text']);
    }

    public function testExtractList()
    {
        $html = '<ul><li>One</li><li>Two</li></ul>';
        $doc = new Document($html);
        $schema = [
            'items' => [
                'list-selector' => 'li',
                'content' => [ 'text' => null ] // Use null to get current element text
            ]
        ];
        $result = ScrawlerExtractor::extract($doc, $schema);
        $this->assertCount(2, $result['items']);
        $this->assertEquals('One', $result['items'][0]['text']);
        $this->assertEquals('Two', $result['items'][1]['text']);
    }

    public function testExtractMissingElement()
    {
        $html = '<div></div>';
        $doc = new Document($html);
        $schema = [ 'foo' => 'span' ];
        $result = ScrawlerExtractor::extract($doc, $schema);
        $this->assertNull($result['foo']);
    }
}

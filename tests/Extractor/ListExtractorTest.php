<?php

namespace Scrawler\Tests\Extractor;

use DiDom\Document;
use PHPUnit\Framework\TestCase;
use Scrawler\Extractor\ExtractorFactory;
use Scrawler\Extractor\ListExtractor;

final class ListExtractorTest extends TestCase
{
    private ListExtractor $extractor;

    protected function setUp(): void
    {
        $factory = new ExtractorFactory();
        $this->extractor = new ListExtractor($factory);
    }

    public function testCanHandleListSchema(): void
    {
        $schema = [
            'list-selector' => 'li',
            'content' => ['text' => null],
        ];

        $this->assertTrue($this->extractor->canHandle($schema));
    }

    public function testCannotHandleNonListSchema(): void
    {
        $this->assertFalse($this->extractor->canHandle('div'));
        $this->assertFalse($this->extractor->canHandle(null));
        $this->assertFalse($this->extractor->canHandle([]));
        $this->assertFalse($this->extractor->canHandle(['list-selector' => 'li']));
    }

    public function testExtractSimpleList(): void
    {
        $html = '<ul><li>One</li><li>Two</li><li>Three</li></ul>';
        $doc = new Document($html);

        $schema = [
            'list-selector' => 'li',
            'content' => ['text' => null],
        ];

        $result = $this->extractor->extract($doc, $schema);

        $this->assertCount(3, $result);
        $this->assertEquals('One', $result[0]['text']);
        $this->assertEquals('Two', $result[1]['text']);
        $this->assertEquals('Three', $result[2]['text']);
    }

    public function testExtractComplexList(): void
    {
        $html = '
            <div class="item"><h2>Item 1</h2><a href="/1">Link</a></div>
            <div class="item"><h2>Item 2</h2><a href="/2">Link</a></div>
        ';
        $doc = new Document($html);

        $schema = [
            'list-selector' => '.item',
            'content' => [
                'title' => 'h2',
                'url' => 'a@href',
            ],
        ];

        $result = $this->extractor->extract($doc, $schema);

        $this->assertCount(2, $result);
        $this->assertEquals('Item 1', $result[0]['title']);
        $this->assertEquals('/1', $result[0]['url']);
        $this->assertEquals('Item 2', $result[1]['title']);
        $this->assertEquals('/2', $result[1]['url']);
    }

    public function testExtractEmptyList(): void
    {
        $html = '<ul></ul>';
        $doc = new Document($html);

        $schema = [
            'list-selector' => 'li',
            'content' => ['text' => null],
        ];

        $result = $this->extractor->extract($doc, $schema);

        $this->assertEmpty($result);
    }
}

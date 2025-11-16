<?php

namespace Scrawler\Tests\Extractor;

use DiDom\Document;
use PHPUnit\Framework\TestCase;
use Scrawler\Extractor\TextExtractor;

final class TextExtractorTest extends TestCase
{
    private TextExtractor $extractor;

    protected function setUp(): void
    {
        $this->extractor = new TextExtractor();
    }

    public function testCanHandleString(): void
    {
        $this->assertTrue($this->extractor->canHandle('div'));
        $this->assertTrue($this->extractor->canHandle('a@href'));
    }

    public function testCannotHandleNonString(): void
    {
        $this->assertFalse($this->extractor->canHandle(null));
        $this->assertFalse($this->extractor->canHandle(123));
        $this->assertFalse($this->extractor->canHandle([]));
    }

    public function testExtractText(): void
    {
        $html = '<div><p>Hello World</p></div>';
        $doc = new Document($html);

        $result = $this->extractor->extract($doc, 'p');

        $this->assertEquals('Hello World', $result);
    }

    public function testExtractAttribute(): void
    {
        $html = '<a href="https://example.com">Link</a>';
        $doc = new Document($html);

        $result = $this->extractor->extract($doc, 'a@href');

        $this->assertEquals('https://example.com', $result);
    }

    public function testExtractMissingElement(): void
    {
        $html = '<div></div>';
        $doc = new Document($html);

        $result = $this->extractor->extract($doc, 'p');

        $this->assertNull($result);
    }

    public function testExtractMissingAttribute(): void
    {
        $html = '<a>No href</a>';
        $doc = new Document($html);

        $result = $this->extractor->extract($doc, 'a@href');

        $this->assertNull($result);
    }

    public function testExtractTrimsWhitespace(): void
    {
        $html = '<p>  Content with spaces  </p>';
        $doc = new Document($html);

        $result = $this->extractor->extract($doc, 'p');

        $this->assertEquals('Content with spaces', $result);
    }
}

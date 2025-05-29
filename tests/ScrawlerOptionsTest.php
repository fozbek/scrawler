<?php

use PHPUnit\Framework\TestCase;
use Scrawler\Model\ScrawlerOptions;

class ScrawlerOptionsTest extends TestCase
{
    public function testDefaultValues()
    {
        $options = new ScrawlerOptions();
        $this->assertFalse($options->isHtml);
        $this->assertEquals([], $options->schema);
        $this->assertEquals('', $options->urlOrHtml);
    }

    public function testSetValues()
    {
        $options = new ScrawlerOptions();
        $options->isHtml = true;
        $options->schema = ['foo' => 'bar'];
        $options->urlOrHtml = '<html></html>';
        $this->assertTrue($options->isHtml);
        $this->assertEquals(['foo' => 'bar'], $options->schema);
        $this->assertEquals('<html></html>', $options->urlOrHtml);
    }

    public function testPartialOptions()
    {
        $options = new ScrawlerOptions();
        $options->schema = ['foo' => 'bar'];
        $this->assertFalse($options->isHtml);
        $this->assertEquals(['foo' => 'bar'], $options->schema);
        $this->assertEquals('', $options->urlOrHtml);
    }
}

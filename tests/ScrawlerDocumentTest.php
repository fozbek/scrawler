<?php

namespace Scrawler\Model;

use PHPUnit\Framework\TestCase;

class ScrawlerDocumentTest extends TestCase
{
    public function test_first(): void
    {
        $options = new ScrawlerOptions();
        $options->isHtml = true;
        $options->schema = ['foo' => 'span'];
        $options->urlOrHtml = '<div><span>bar</span></div>';
        $document = new ScrawlerDocument($options);
        $result = $document->extract();
        self::assertEquals(['foo' => 'bar'], $result);
    }

    public function test_empty_schema(): void
    {
        $options = new ScrawlerOptions();
        $options->isHtml = true;
        $options->schema = [];
        $options->urlOrHtml = '<div><span>bar</span></div>';
        $document = new ScrawlerDocument($options);
        $result = $document->extract();
        self::assertEquals([], $result);
    }

    public function test_malformed_html(): void
    {
        $options = new ScrawlerOptions();
        $options->isHtml = true;
        $options->schema = ['foo' => 'span'];
        $options->urlOrHtml = '<div><span>bar'; // malformed
        $document = new ScrawlerDocument($options);
        $result = $document->extract();
        self::assertEquals(['foo' => 'bar'], $result);
    }

    public function test_null_value_schema(): void
    {
        $options = new ScrawlerOptions();
        $options->isHtml = true;
        $options->schema = ['foo' => null];
        $options->urlOrHtml = '<div>baz</div>';
        $document = new ScrawlerDocument($options);
        $result = $document->extract();
        self::assertEquals(['foo' => 'baz'], $result);
    }
}

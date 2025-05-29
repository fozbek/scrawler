<?php

namespace Scrawler\Model;

use DiDom\Document;

/**
 * Handles the extraction logic for Scrawler.
 */
class ScrawlerDocument
{
    /**
     * @var Document
     */
    private Document $document;

    /**
     * @var array
     */
    private array $schema;

    /**
     * ScrawlerDocument constructor.
     * @param ScrawlerOptions $options
     */
    public function __construct(ScrawlerOptions $options)
    {
        $this->schema = $options->schema;
        $this->document = $options->isHtml
            ? new Document($options->urlOrHtml)
            : new Document($options->urlOrHtml, true);
    }

    /**
     * Extract data based on the schema.
     * @return array
     */
    public function extract(): array
    {
        return \Scrawler\ScrawlerExtractor::extract($this->document, $this->schema);
    }
}
<?php

namespace Scrawler\Model;

/**
 * Options for Scrawler scraping process.
 */
class ScrawlerOptions
{
    /**
     * @var bool Whether the input is HTML (true) or a URL (false)
     */
    public bool $isHtml = false;

    /**
     * @var array The extraction schema
     */
    public array $schema = [];

    /**
     * @var string The URL or HTML string to scrape
     */
    public string $urlOrHtml = '';
}
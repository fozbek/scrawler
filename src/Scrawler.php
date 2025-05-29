<?php

namespace Scrawler;

use GuzzleHttp\Client;
use Scrawler\Model\ScrawlerDocument;
use Scrawler\Model\ScrawlerOptions;

/**
 * Main Scrawler class for web scraping.
 */
class Scrawler
{
    /**
     * Scrape a web page or HTML string using a schema.
     *
     * @param string $urlOrHtml URL or HTML string to scrape
     * @param array $schema Extraction schema
     * @param bool $isHtml If true, treat $urlOrHtml as HTML
     * @return array Extracted data
     */
    public function scrape(string $urlOrHtml, array $schema, bool $isHtml = false, ?Client $client = null): array
    {
        $options = new ScrawlerOptions();
        $options->schema = $schema;
        if ($isHtml) {
            $options->isHtml = true;
            $options->urlOrHtml = $urlOrHtml;
        } else {
            $requestHelper = new \Scrawler\RequestHelper($client);
            $options->isHtml = true;
            $options->urlOrHtml = $requestHelper->fetch($urlOrHtml);
        }

        return $this->loopSchema($options);
    }

    /**
     * Internal method to process the schema and extract data.
     *
     * @param ScrawlerOptions $options
     * @return array
     */
    public function loopSchema(ScrawlerOptions $options): array
    {
        $doc = new ScrawlerDocument($options);
        
        return $doc->extract();
    }
}

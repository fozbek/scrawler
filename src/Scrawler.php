<?php

namespace Scrawler;

use DiDom\Document;
use GuzzleHttp\Client;
use Scrawler\Extractor\ExtractorFactory;
use Scrawler\Http\HttpClient;

/**
 * Main entry point for web scraping with schema-based extraction.
 */
final class Scrawler
{
    private HttpClient $httpClient;
    private ExtractorFactory $extractorFactory;

    public function __construct(?Client $client = null)
    {
        $this->httpClient = new HttpClient($client);
        $this->extractorFactory = new ExtractorFactory();
    }

    /**
     * Scrape data from a URL or HTML string using a schema.
     *
     * @param string $urlOrHtml URL to fetch or HTML string to parse
     * @param array<string, mixed> $schema Extraction schema
     * @param bool $isHtml Whether to treat input as HTML (true) or URL (false)
     * @return array<string, mixed> Extracted data
     *
     * @example
     * Basic usage:
     * $schema = ['title' => 'h1', 'description' => 'p'];
     * $data = $scrawler->scrape('https://example.com', $schema);
     *
     * @example
     * Extract attributes:
     * $schema = ['link' => 'a@href', 'image' => 'img@src'];
     *
     * @example
     * Extract lists:
     * $schema = [
     *     'items' => [
     *         'list-selector' => 'li',
     *         'content' => ['text' => null]
     *     ]
     * ];
     */
    public function scrape(
        string $urlOrHtml,
        array $schema,
        bool $isHtml = false,
    ): array {
        $html = $isHtml ? $urlOrHtml : $this->httpClient->fetch($urlOrHtml);
        $document = new Document($html);

        return $this->extractData($document, $schema);
    }

    /**
     * Extract data from a document using the schema.
     *
     * @param Document|\DiDom\Element $context
     * @param array<string, mixed> $schema
     * @return array<string, mixed>
     */
    private function extractData($context, array $schema): array
    {
        $result = [];

        foreach ($schema as $key => $value) {
            $extractor = $this->extractorFactory->create($value);
            $result[$key] = $extractor->extract($context, $value);
        }

        return $result;
    }
}

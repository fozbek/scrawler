<?php

namespace Scrawler;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Scrawler\Model\ScrawlerDocument;
use Scrawler\Model\ScrawlerOptions;

class Scrawler
{
    private ?ScrawlerOptions $options;

    /**
     * Scrawler constructor.
     * @param ?ScrawlerOptions $options
     */
    public function __construct(?ScrawlerOptions $options = null)
    {
        if (!$options instanceof ScrawlerOptions) {
            $options = new ScrawlerOptions();
        }

        $this->options = $options;
    }

    /**
     * @param string $urlOrHtml
     * @param array<mixed> $schema
     * @param bool $isHtml
     * @return array<mixed>
     * @throws GuzzleException
     */
    public function scrape(string $urlOrHtml, array $schema, bool $isHtml = false): array
    {
        if ($isHtml === false) {
            $urlOrHtml = $this->getHtmlContentFromUrl($urlOrHtml);
        }

        return $this->loopSchema($urlOrHtml, $schema);
    }

    /**
     * @param string $htmlContent
     * @param array<mixed> $schema
     * @return array<string, mixed>
     * @throws Exception|GuzzleException
     */
    private function loopSchema(string $htmlContent, array $schema): array
    {
        $result = [];

        foreach ($schema as $key => $depth) {
            $depthModel = new ScrawlerDocument($htmlContent);
            $depth = ScrawlerDocument::normalizeDepth($depth);

            if ($depth['selector']) { // handle single
                if ($depth['content'] !== false) {
                    $element = $depthModel->first($depth['selector']);

                    $content = '<null>';
                    if ($element !== null) {
                        $content = $element->html();
                    }

                    $result[$key] = $this->loopSchema($content, $depth['content']);
                } else {
                    $result[$key] = $depthModel->handleSingleSelector($depth);
                }

            } elseif ($depth['request-selector']) {

                $url = $depthModel->handleSingleSelector($depth['request-selector']);

                if ($depth['base-url']) {
                    $url = $this->makeValidUrl($url, $depth['base-url']);
                }

                $result[$key] = $this->scrape($url, $depth['content']);

            } else {
                $elements = $depthModel->find($depth['list-selector']);

                if ($depth['content'] === false) {
                    $result[$key][] = array_map(static fn($element) => ScrawlerDocument::manipulateExistingDom($element, $depth), $elements);

                    continue;
                }

                foreach ($elements as $element) {
                    $result[$key][] = $this->loopSchema($element->html(), $depth['content']);
                }
            }
        }

        return $result;
    }

    /**
     * @param string $url
     * @return string
     * @throws Exception|GuzzleException
     */
    private function getHtmlContentFromUrl(string $url): string
    {
        return $this->getHttpRequester()->GET($url);
    }

    /**
     * @return RequestHelper
     */
    private function getHttpRequester(): RequestHelper
    {
        if (empty($this->requestHelper)) {
            if (!$this->options->getGuzzleClient()) {
                $this->options->setGuzzleClient(new Client());
            }

            $this->requestHelper = new RequestHelper($this->options->getGuzzleClient());
        }

        return $this->requestHelper;
    }

    /**
     * @param string $pathOrUrl
     * @param string $baseUrl
     * @return string
     */
    public function makeValidUrl(string $pathOrUrl, string $baseUrl): string
    {
        if ($baseUrl) {
            return sprintf("%s/%s", rtrim($baseUrl, '/'), ltrim($pathOrUrl, '/')); // make a valid url
        }

        return $pathOrUrl;
    }
}
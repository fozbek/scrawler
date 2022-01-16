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
        if (!is_a($options, ScrawlerOptions::class)) {
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
            $model = new ScrawlerDocument($htmlContent, $depth);

            if ($model->isSingleSelector()) {
                if ($model->hasContent()) {
                    $result[$key] = $this->loopSchema($model->getHtml(), $model->getContent());
                } else {
                    $result[$key] = $model->handleSingleSelector();
                }

            } elseif ($model->isRequestSelector()) {
                $result[$key] = $this->scrape($model->getUrl(), $model->getContent());
            } else {
                $elements = $model->getListContents();

                if (false === $model->hasContent()) {
                    $result[$key][] = array_map(static function ($element) use ($depth) {
                        return (new ScrawlerDocument($element, $depth))->handleSingleSelector();
                    }, $elements);

                    continue;
                }

                foreach ($elements as $element) {
                    $result[$key][] = $this->loopSchema($element->html(), $model->getContent());
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
}
<?php

namespace Scrawler;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class Scrawler
{
    /**
     * @var array
     */
    private $options = [];
    /**
     * @var string|null
     */
    private $baseUrl = null;
    /**
     * @var RequestHelper
     */
    private $requestHelper = null;

    /**
     * Scrawler constructor.
     * @param array $options
     * @param Client|null $client
     */
    public function __construct(?array $options = [], ?Client $client = null)
    {
        $options['guzzle_client'] = $client;
        $this->options = array_merge_recursive($this->options, $options);
    }

    /**
     * @param string $urlOrHtml
     * @param array|string $template
     * @return array
     * @throws \Exception
     */
    public function scrape(string $urlOrHtml, $template): array
    {
        if (array_key_exists('pagination', $template)) {
            $pagination = $template['pagination'];
            unset($template['pagination']);

            return $this->handlePagination($urlOrHtml, $template, $pagination);
        }

        $htmlContent = $this->getHtmlContent($urlOrHtml);

        return $this->loopTemplate($htmlContent, $template);
    }

    /**
     * @param $urlOrHtml
     * @return string
     * @throws \Exception
     */
    private function getHtmlContent($urlOrHtml): string
    {
        if (!filter_var($urlOrHtml, FILTER_VALIDATE_URL)) {
            return $urlOrHtml;
        }

        $this->loadBaseUrl($urlOrHtml);
        $this->loadHttpRequester();
        $htmlContent = $this->requestHelper->GET($urlOrHtml);

        if ($htmlContent === false) {
            throw new \Exception('HTTP request Failed.');
        }

        return $htmlContent;
    }

    /**
     * @param string $pathOrUrl
     * @return void
     * @throws \Exception
     */
    public function makeValidUrl(string &$pathOrUrl): void
    {
        if (filter_var($pathOrUrl, FILTER_VALIDATE_URL)) {
            return;
        }

        if (empty($this->baseUrl)) {
            throw new \Exception('Base url is empty');
        }

        $pathOrUrl = $this->baseUrl . '/' . ltrim($pathOrUrl, '/'); // make a valid url
    }

    /**
     * @param string $url
     * @return void
     */
    public function loadBaseUrl(string $url): void
    {
        $urlParts = parse_url($url);
        $host = $urlParts['host'];
        $scheme = $urlParts['scheme'];

        $url = sprintf('%s://%s', $scheme, $host);
        $this->baseUrl = rtrim($url, '/');
    }

    private function handleSelector(string $html, string $selector, bool $isSingle = true)
    {
        $crawler = new Crawler($html);
        [$selector, $attributeName] = $this->normalizeSelector($selector);
        $domObject = $crawler->filter($selector);

        if ($domObject->count() < 1) {
            return $isSingle ? null : [];
        }

        if (!empty($attributeName)) {
            $attributeValue = $domObject->first()->attr($attributeName);

            if (in_array($attributeName, ['href', 'src'])) {
                $this->makeValidUrl($attributeValue);
            }

            return $attributeValue;
        }

        if ($isSingle) {
            return $domObject->first()->text();
        }

        return $domObject;
    }

    private function normalizeSelector(string $selector)
    {
        if (strpos($selector, '@') !== false) {
            return explode('@', $selector);
        }

        return [$selector, null];
    }

    // todo this method should be simplified
    private function loopTemplate(string $htmlContent, array $template): array
    {
        $result = [];

        foreach ($template as $key => $depth) {

            if (is_string($depth)) {
                $result[$key] = $this->handleSelector($htmlContent, $depth);
            } else {

                $urlOrList = $this->handleSelector($htmlContent, $depth['selector'], false);

                if (is_string($urlOrList)) {
                    $result[$key] = $this->scrape($urlOrList, $depth['content']);
                } else { // list selector
                    if (empty($urlOrList)) {
                        $result[$key] = [];
                    } else {
                        foreach ($urlOrList as $listSelectorKey => $item) {
                            $itemContent = $item->ownerDocument->saveHTML($item);
                            $result[$key][$listSelectorKey] = $this->scrape($itemContent, $depth['content']);
                        }
                    }
                }

            }
        }

        return $result;
    }

    /**
     * @return void
     */
    private function loadHttpRequester(): void
    {
        if (!empty($this->requestHelper))
            return;

        $options = [];
        $options ??= $this->options['guzzle_options'];

        $client = null;
        $client ??= $this->options['guzzle_client'];

        $this->requestHelper = new RequestHelper($options, $client);
    }

    /**
     * @param string $urlOrHtml
     * @param array $template
     * @param array $pagination
     * @return array
     * @throws \Exception
     */
    private function handlePagination(string $urlOrHtml, array $template, array $pagination): array
    {
        $currentPage = 1;
        $selector = $pagination['selector'];
        $maxPage = $pagination['limit'];
        $pages = [];

        // todo this method should be simplified

        do {
            $urlKey = 'very_secret_page_url_key';

            $template[$urlKey] = $selector;
            $response = $this->scrape($urlOrHtml, $template);

            $urlOrHtml = $response[$urlKey];
            unset($response[$urlKey]);
            $pages[$currentPage] = $response;

        } while (++$currentPage <= $maxPage && !empty($urlOrHtml));

        return $pages;
    }
}
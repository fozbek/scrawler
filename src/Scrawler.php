<?php

namespace Scrawler;

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
     * Xray constructor.
     * @param array $options
     */
    public function __construct($options = [])
    {
        $this->options = array_merge_recursive($this->options, $options);
    }

    /**
     * @param string $urlOrHtml
     * @param $template
     * @return array
     * @throws \Exception
     */
    public function scrape($urlOrHtml, $template)
    {
        if (array_key_exists('pagination', $template)) {
            $pagination = $template['pagination'];
            unset($template['pagination']);

            return $this->handlePagination($urlOrHtml, $template, $pagination);
        }

        $html = $this->getHtmlContent($urlOrHtml);

        return $this->loopTemplate($html, $template);
    }

    /**
     * @param $urlOrHtml
     * @return string
     * @throws \Exception
     */
    private function getHtmlContent($urlOrHtml): string
    {
        if (filter_var($urlOrHtml, FILTER_VALIDATE_URL)) {
            $this->loadBaseUrl($urlOrHtml);
            $this->loadHttpRequester();
            $html = $this->requestHelper->GET($urlOrHtml);

            if ($html === false) {
                throw new \Exception('HTTP request Failed.');
            }

            return $html;
        }

        return $urlOrHtml;
    }

    public function makeValidUrl(string &$pathOrUrl)
    {
        if (filter_var($pathOrUrl, FILTER_VALIDATE_URL)) {
            return;
        }

        if (empty($this->baseUrl)) {
            throw new \Exception('Base url is empty');
        }

        $pathOrUrl = $this->baseUrl . ltrim($pathOrUrl, '/'); // make a valid url
    }

    public function loadBaseUrl($url)
    {
        $urlParts = parse_url($url);
        $host = $urlParts['host'];
        $scheme = $urlParts['scheme'];

        $url = sprintf('%s://%s', $scheme, $host);
        $this->baseUrl = rtrim($url, '/') . '/';
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
    private function loopTemplate($urlOrHtml, $template)
    {
        $result = [];

        foreach ($template as $key => $depth) {

            if (is_string($depth)) {
                $result[$key] = $this->handleSelector($urlOrHtml, $depth);
            } else {

                $urlOrList = $this->handleSelector($urlOrHtml, $depth['selector'], false);

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

    private function loadHttpRequester()
    {
        if (!empty($this->requestHelper))
            return;

        $options = [];
        if (!empty($this->options['guzzle_options'])) {
            $options = $this->options['guzzle_options'];
        }

        $this->requestHelper = new RequestHelper($options);
    }

    private function handlePagination(string $urlOrHtml, $template, $pagination)
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
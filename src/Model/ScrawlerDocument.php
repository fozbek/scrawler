<?php

namespace Scrawler\Model;

use DiDom\Document;
use DiDom\Element;
use DiDom\Exceptions\InvalidSelectorException;
use DOMElement;

class ScrawlerDocument
{
    public const defaultParameters = [
        'selector' => false,
        'list-selector' => false,
        'request-selector' => false,
        'base-url' => false,
        'content' => false,
        'how' => false,
        'attr' => false,
        'trim' => true,
    ];

    /**
     * @var Document
     */
    private Document $document;
    private array $depth;

    /**
     * ScrawlerDocument constructor.
     * @param string $content
     * @param $depth
     */
    public function __construct(string $content, $depth)
    {
        $this->document = new Document($content);
        $this->depth = $this->normalizeDepth($depth);
    }

    public function isSingleSelector(): bool
    {
        return !empty($this->depth['selector']);
    }

    public function isRequestSelector(): bool
    {
        return !empty($this->depth['request-selector']);
    }

    public function isListSelector(): bool
    {
        return !empty($this->depth['list-selector']);
    }

    public function hasContent(): bool
    {
        return !empty($this->depth['content']);
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->depth['content'];
    }

    /**
     * @return string
     * @throws InvalidSelectorException
     */
    public function getHtml(): string
    {
        $element = $this->first();

        $content = '<null>';
        if ($element !== null) {
            $content = $element->html();
        }

        return $content;
    }

    /**
     * @return string
     * @throws InvalidSelectorException
     */
    public function getUrl(): ?string
    {
        if ($this->depth['base-url']) {
            return $this->buildUrl($this->first(), $this->depth['base-url']);
        }

        return $this->first();
    }

    public function getListSelector()
    {
        return $this->depth['list-selector'];
    }

    private function getDocument(): Document
    {
        return $this->document;
    }

    /**
     * @throws InvalidSelectorException
     */
    public function getCount(string $expression): int
    {
        return $this->getDocument()->count($expression);
    }

    /**
     * @return Element|DOMElement|null
     * @throws InvalidSelectorException
     *
     */
    public function first()
    {
        $selector = 'list-selector';

        if ($this->isSingleSelector()) {
            $selector = 'selector';
        }

        if ($this->isRequestSelector()) {
            $selector = 'request-selector';
        }

        return $this->getDocument()->first($this->depth[$selector]);
    }

    /**
     * @return Element[]
     * @throws InvalidSelectorException
     */
    public function getListContents(): array
    {
        return $this->getDocument()->find($this->getListSelector());
    }

    /**
     * @throws InvalidSelectorException
     */
    public function handleSingleSelector(): ?string
    {
        $element = $this->first();

        if ($element === null) {
            return null;
        }

        return $this->manipulateExistingDom($element, $this->depth);
    }

    public function manipulateExistingDom($element, $depth)
    {
        if ($depth['attr'] !== false) {
            return $element->attr($depth['attr']);
        }

        if ($depth['how'] === 'html') {
            return $element->html();
        }

        if ($depth['trim'] !== false) {
            return trim($element->text());
        }

        return $element->text();
    }

    private function getOptions($depth): array
    {
        return array_merge(self::defaultParameters, $depth);
    }

    private function normalizeDepth($depth): array
    {
        if (is_string($depth)) {
            if (str_contains($depth, '@')) {
                [$selector, $attr] = explode('@', $depth);

                return $this->getOptions([
                    'selector' => $selector,
                    'attr' => $attr,
                ]);
            }

            return $this->getOptions([
                'selector' => $depth,
            ]);
        }

        return $this->getOptions($depth);
    }

    /**
     * @param string $pathOrUrl
     * @param string $baseUrl
     * @return string
     */
    private function buildUrl(string $pathOrUrl, string $baseUrl): string
    {
        if ($baseUrl) {
            return sprintf("%s/%s", rtrim($baseUrl, '/'), ltrim($pathOrUrl, '/')); // make a valid url
        }

        return $pathOrUrl;
    }
}

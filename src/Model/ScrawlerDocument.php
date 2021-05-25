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

    /**
     * ScrawlerDocument constructor.
     * @param string $content
     */
    public function __construct(string $content)
    {
        $this->document = new Document($content);
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
    public function first(string $expression)
    {
        return $this->getDocument()->first($expression);
    }

    /**
     * @return Element[]
     * @throws InvalidSelectorException
     */
    public function find(string $expression): array
    {
        return $this->getDocument()->find($expression);
    }

    public function handleSingleSelector($depth): ?string
    {
        $depth = self::normalizeDepth($depth);

        $element = $this->first($depth['selector']);

        if ($element === null) {
            return null;
        }

        return self::manipulateExistingDom($element, $depth);
    }

    public static function manipulateExistingDom($element, $depth)
    {
        if ($depth['attr'] !== false) {
            return $element->attr($depth['attr']);
        }

        if ($depth['how'] !== false) {
            if ($depth['how'] === 'html') {
                return $element->html();
            }
        }

        if ($depth['trim'] !== false) {
            return trim($element->text());
        }

        return $element->text();
    }

    private static function _normalizeDepth($depth): array
    {
        return array_merge(self::defaultParameters, $depth);
    }

    public static function normalizeDepth($depth): array
    {
        if (is_string($depth)) {
            if (strpos($depth, '@') !== false) {
                [$selector, $attr] = explode('@', $depth);

                return self::_normalizeDepth([
                    'selector' => $selector,
                    'attr' => $attr,
                ]);
            }

            return self::_normalizeDepth([
                'selector' => $depth,
            ]);
        }

        return self::_normalizeDepth($depth);
    }
}
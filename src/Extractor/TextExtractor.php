<?php

namespace Scrawler\Extractor;

use DiDom\Element;
use Scrawler\Selector\Selector;
use Scrawler\Transformer\TransformerRegistry;

/**
 * Extracts text content or attributes from elements.
 *
 * Supports transformers for value manipulation.
 */
final class TextExtractor implements ExtractorInterface
{
    private TransformerRegistry $transformerRegistry;

    public function __construct(
        ?TransformerRegistry $transformerRegistry = null,
    ) {
        $this->transformerRegistry =
            $transformerRegistry ?? new TransformerRegistry();
    }

    /**
     * @param mixed $context
     * @param mixed $value
     * @return mixed
     */
    public function extract($context, $value)
    {
        // Handle array format: [selector, transformers]
        if (is_array($value) && array_key_exists(0, $value)) {
            $selector = $value[0];
            $transformers = $value[1] ?? null;

            if (!is_string($selector)) {
                return null;
            }

            $result = $this->extractFromSelector($context, $selector);

            if ($transformers && is_string($transformers)) {
                $result = $this->transformerRegistry->applyChain(
                    $result,
                    $transformers,
                );
            }

            return $result;
        }

        // Handle simple string format
        if (is_string($value)) {
            return $this->extractFromSelector($context, $value);
        }

        return null;
    }

    public function canHandle($value): bool
    {
        // Handle string selectors
        if (is_string($value)) {
            return true;
        }

        // Handle array format: [selector, transformers]
        if (
            is_array($value) &&
            array_key_exists(0, $value) &&
            is_string($value[0])
        ) {
            return true;
        }

        return false;
    }

    /**
     * Extract value from a selector string.
     *
     * @param \DiDom\Document|\DiDom\Element $context
     */
    private function extractFromSelector(
        $context,
        string $selectorString,
    ): ?string {
        $selector = Selector::fromString($selectorString);

        if ($selector->hasAttribute()) {
            return $this->extractAttribute($context, $selector);
        }

        return $this->extractText($context, $selector);
    }

    /**
     * @param \DiDom\Document|\DiDom\Element $context
     */
    private function extractText($context, Selector $selector): ?string
    {
        $elements = $context->find($selector->getCssSelector());

        if (empty($elements)) {
            return null;
        }

        $element = $elements[0];

        if ($element instanceof Element) {
            return trim($element->text());
        }

        return null;
    }

    /**
     * @param \DiDom\Document|\DiDom\Element $context
     */
    private function extractAttribute($context, Selector $selector): ?string
    {
        $cssSelector = $selector->getCssSelector();
        $attribute = $selector->getAttribute();

        // If selector is empty, extract attribute from current context element
        if ($cssSelector === "" && $context instanceof Element) {
            return $context->attr($attribute);
        }

        // Otherwise find elements with the selector
        $elements = $context->find($cssSelector);

        if (empty($elements)) {
            return null;
        }

        $element = $elements[0];

        if ($element instanceof Element && $attribute !== null) {
            return $element->attr($attribute);
        }

        return null;
    }
}

<?php

namespace Scrawler\Extractor;

use DiDom\Element;
use Scrawler\Transformer\TransformerRegistry;

/**
 * Extracts text from the current context element.
 */
final class CurrentTextExtractor implements ExtractorInterface
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
        if (!($context instanceof Element)) {
            return null;
        }

        $text = trim($context->text());

        // Handle array format: [null, transformers]
        if (
            is_array($value) &&
            array_key_exists(0, $value) &&
            $value[0] === null
        ) {
            $transformers = $value[1] ?? null;

            if ($transformers && is_string($transformers)) {
                return $this->transformerRegistry->applyChain(
                    $text,
                    $transformers,
                );
            }
        }

        return $text;
    }

    public function canHandle($value): bool
    {
        // Handle simple null
        if ($value === null) {
            return true;
        }

        // Handle array format: [null, transformers]
        // Note: use array_key_exists because isset() returns false for null values
        if (
            is_array($value) &&
            array_key_exists(0, $value) &&
            $value[0] === null
        ) {
            return true;
        }

        return false;
    }
}

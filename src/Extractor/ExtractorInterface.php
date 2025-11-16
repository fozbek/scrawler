<?php

namespace Scrawler\Extractor;

use DiDom\Document;
use DiDom\Element;

/**
 * Interface for all extractors.
 */
interface ExtractorInterface
{
    /**
     * Extract data from the given context.
     *
     * @param Document|Element $context The document or element to extract from
     * @param mixed $value The schema value that defines the extraction
     * @return mixed The extracted data
     */
    public function extract($context, $value);

    /**
     * Check if this extractor can handle the given value.
     *
     * @param mixed $value The schema value to check
     * @return bool True if this extractor can handle the value
     */
    public function canHandle($value): bool;
}

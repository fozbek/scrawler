<?php

namespace Scrawler\Extractor;

/**
 * Fallback extractor that returns null for unhandled values.
 */
final class NullExtractor implements ExtractorInterface
{
    public function extract($context, $value): null
    {
        return null;
    }

    public function canHandle($value): bool
    {
        return true;
    }
}

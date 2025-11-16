<?php

namespace Scrawler\Extractor;

use Scrawler\Transformer\TransformerRegistry;

/**
 * Factory for creating extractors based on schema values.
 *
 * Uses the Chain of Responsibility pattern to find the appropriate extractor.
 */
final class ExtractorFactory
{
    /**
     * @var array<ExtractorInterface>
     */
    private array $extractors;

    private TransformerRegistry $transformerRegistry;

    public function __construct(
        ?TransformerRegistry $transformerRegistry = null,
    ) {
        $this->transformerRegistry =
            $transformerRegistry ?? new TransformerRegistry();

        // Order matters: most specific first
        $this->extractors = [
            new ListExtractor($this),
            new CurrentTextExtractor($this->transformerRegistry),
            new TextExtractor($this->transformerRegistry),
            new NullExtractor(),
        ];
    }

    /**
     * Create an extractor for the given value.
     *
     * @param mixed $value The schema value
     * @return ExtractorInterface The appropriate extractor
     */
    public function create($value): ExtractorInterface
    {
        foreach ($this->extractors as $extractor) {
            if ($extractor->canHandle($value)) {
                return $extractor;
            }
        }

        // This should never happen since NullExtractor handles everything
        return new NullExtractor();
    }
}

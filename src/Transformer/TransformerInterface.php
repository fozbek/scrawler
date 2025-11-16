<?php

namespace Scrawler\Transformer;

/**
 * Interface for value transformers.
 */
interface TransformerInterface
{
    /**
     * Transform a value.
     *
     * @param mixed $value The value to transform
     * @return mixed The transformed value
     */
    public function transform($value);

    /**
     * Get the transformer name.
     *
     * @return string
     */
    public function getName(): string;
}

<?php

namespace Scrawler\Transformer\Transformers;

use Scrawler\Transformer\TransformerInterface;

final class UpperTransformer implements TransformerInterface
{
    public function transform($value)
    {
        return is_string($value) ? strtoupper($value) : $value;
    }

    public function getName(): string
    {
        return 'upper';
    }
}

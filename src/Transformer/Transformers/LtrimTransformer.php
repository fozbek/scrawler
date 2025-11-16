<?php

namespace Scrawler\Transformer\Transformers;

use Scrawler\Transformer\TransformerInterface;

final class LtrimTransformer implements TransformerInterface
{
    public function transform($value)
    {
        return is_string($value) ? ltrim($value) : $value;
    }

    public function getName(): string
    {
        return 'ltrim';
    }
}

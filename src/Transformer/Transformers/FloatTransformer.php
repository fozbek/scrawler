<?php

namespace Scrawler\Transformer\Transformers;

use Scrawler\Transformer\TransformerInterface;

final class FloatTransformer implements TransformerInterface
{
    public function transform($value)
    {
        return (float) $value;
    }

    public function getName(): string
    {
        return 'float';
    }
}

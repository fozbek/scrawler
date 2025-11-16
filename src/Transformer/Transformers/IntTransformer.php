<?php

namespace Scrawler\Transformer\Transformers;

use Scrawler\Transformer\TransformerInterface;

final class IntTransformer implements TransformerInterface
{
    public function transform($value)
    {
        return (int) $value;
    }

    public function getName(): string
    {
        return 'int';
    }
}

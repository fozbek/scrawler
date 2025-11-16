<?php

namespace Scrawler\Transformer\Transformers;

use Scrawler\Transformer\TransformerInterface;

final class BoolTransformer implements TransformerInterface
{
    public function transform($value)
    {
        return (bool) $value;
    }

    public function getName(): string
    {
        return 'bool';
    }
}

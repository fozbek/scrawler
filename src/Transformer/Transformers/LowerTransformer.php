<?php

namespace Scrawler\Transformer\Transformers;

use Scrawler\Transformer\TransformerInterface;

final class LowerTransformer implements TransformerInterface
{
    public function transform($value)
    {
        return is_string($value) ? strtolower($value) : $value;
    }

    public function getName(): string
    {
        return 'lower';
    }
}

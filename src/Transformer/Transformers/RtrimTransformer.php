<?php

namespace Scrawler\Transformer\Transformers;

use Scrawler\Transformer\TransformerInterface;

final class RtrimTransformer implements TransformerInterface
{
    public function transform($value)
    {
        return is_string($value) ? rtrim($value) : $value;
    }

    public function getName(): string
    {
        return 'rtrim';
    }
}

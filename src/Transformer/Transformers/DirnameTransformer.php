<?php

namespace Scrawler\Transformer\Transformers;

use Scrawler\Transformer\TransformerInterface;

final class DirnameTransformer implements TransformerInterface
{
    public function transform($value)
    {
        return is_string($value) ? dirname($value) : $value;
    }

    public function getName(): string
    {
        return 'dirname';
    }
}

<?php

namespace Scrawler\Transformer\Transformers;

use Scrawler\Transformer\TransformerInterface;

final class UrldecodeTransformer implements TransformerInterface
{
    public function transform($value)
    {
        return is_string($value) ? urldecode($value) : $value;
    }

    public function getName(): string
    {
        return 'urldecode';
    }
}

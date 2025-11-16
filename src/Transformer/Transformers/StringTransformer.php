<?php

namespace Scrawler\Transformer\Transformers;

use Scrawler\Transformer\TransformerInterface;

final class StringTransformer implements TransformerInterface
{
    public function transform($value)
    {
        return (string) $value;
    }

    public function getName(): string
    {
        return 'string';
    }
}

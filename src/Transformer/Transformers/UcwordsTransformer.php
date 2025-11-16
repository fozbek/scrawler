<?php

namespace Scrawler\Transformer\Transformers;

use Scrawler\Transformer\TransformerInterface;

final class UcwordsTransformer implements TransformerInterface
{
    public function transform($value)
    {
        return is_string($value) ? ucwords($value) : $value;
    }

    public function getName(): string
    {
        return 'ucwords';
    }
}

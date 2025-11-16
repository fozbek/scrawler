<?php

namespace Scrawler\Transformer\Transformers;

use Scrawler\Transformer\TransformerInterface;

final class UcfirstTransformer implements TransformerInterface
{
    public function transform($value)
    {
        return is_string($value) ? ucfirst($value) : $value;
    }

    public function getName(): string
    {
        return 'ucfirst';
    }
}

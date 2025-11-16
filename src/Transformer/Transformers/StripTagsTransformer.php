<?php

namespace Scrawler\Transformer\Transformers;

use Scrawler\Transformer\TransformerInterface;

final class StripTagsTransformer implements TransformerInterface
{
    public function transform($value)
    {
        return is_string($value) ? strip_tags($value) : $value;
    }

    public function getName(): string
    {
        return 'strip_tags';
    }
}

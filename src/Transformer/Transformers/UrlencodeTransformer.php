<?php

namespace Scrawler\Transformer\Transformers;

use Scrawler\Transformer\TransformerInterface;

final class UrlencodeTransformer implements TransformerInterface
{
    public function transform($value)
    {
        return is_string($value) ? urlencode($value) : $value;
    }

    public function getName(): string
    {
        return 'urlencode';
    }
}

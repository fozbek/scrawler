<?php

namespace Scrawler\Transformer\Transformers;

use Scrawler\Transformer\TransformerInterface;

final class TrimTransformer implements TransformerInterface
{
    public function transform($value)
    {
        return is_string($value) ? trim($value) : $value;
    }

    public function getName(): string
    {
        return 'trim';
    }
}

<?php

namespace Scrawler\Transformer\Transformers;

use Scrawler\Transformer\TransformerInterface;

final class JsonTransformer implements TransformerInterface
{
    public function transform($value)
    {
        if (!is_string($value)) {
            return $value;
        }

        $decoded = json_decode($value, true);
        return $decoded !== null ? $decoded : $value;
    }

    public function getName(): string
    {
        return 'json';
    }
}

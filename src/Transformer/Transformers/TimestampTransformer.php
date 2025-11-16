<?php

namespace Scrawler\Transformer\Transformers;

use Scrawler\Transformer\TransformerInterface;

final class TimestampTransformer implements TransformerInterface
{
    public function transform($value)
    {
        if (!is_string($value)) {
            return $value;
        }

        $timestamp = strtotime($value);
        return $timestamp !== false ? $timestamp : $value;
    }

    public function getName(): string
    {
        return 'timestamp';
    }
}

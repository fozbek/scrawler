<?php

namespace Scrawler\Transformer\Transformers;

use Scrawler\Transformer\TransformerInterface;

final class AbsTransformer implements TransformerInterface
{
    public function transform($value)
    {
        return is_numeric($value) ? abs($value) : $value;
    }

    public function getName(): string
    {
        return 'abs';
    }
}

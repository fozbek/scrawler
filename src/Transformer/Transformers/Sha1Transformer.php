<?php

namespace Scrawler\Transformer\Transformers;

use Scrawler\Transformer\TransformerInterface;

final class Sha1Transformer implements TransformerInterface
{
    public function transform($value)
    {
        return is_string($value) ? sha1($value) : $value;
    }

    public function getName(): string
    {
        return 'sha1';
    }
}

<?php

namespace Scrawler\Transformer\Transformers;

use Scrawler\Transformer\TransformerInterface;

final class Md5Transformer implements TransformerInterface
{
    public function transform($value)
    {
        return is_string($value) ? md5($value) : $value;
    }

    public function getName(): string
    {
        return 'md5';
    }
}

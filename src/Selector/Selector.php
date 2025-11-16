<?php

namespace Scrawler\Selector;

/**
 * Represents a CSS selector with optional attribute extraction.
 */
final class Selector
{
    private string $cssSelector;
    private ?string $attribute;

    private function __construct(string $cssSelector, ?string $attribute = null)
    {
        $this->cssSelector = $cssSelector;
        $this->attribute = $attribute;
    }

    public static function fromString(string $value): self
    {
        if (str_contains($value, '@')) {
            [$selector, $attribute] = explode('@', $value, 2);
            return new self($selector, $attribute);
        }

        return new self($value);
    }

    public function getCssSelector(): string
    {
        return $this->cssSelector;
    }

    public function getAttribute(): ?string
    {
        return $this->attribute;
    }

    public function hasAttribute(): bool
    {
        return $this->attribute !== null;
    }
}

<?php

namespace Scrawler\Tests\Selector;

use PHPUnit\Framework\TestCase;
use Scrawler\Selector\Selector;

final class SelectorTest extends TestCase
{
    public function testCreateSimpleSelector(): void
    {
        $selector = Selector::fromString('div.class');

        $this->assertEquals('div.class', $selector->getCssSelector());
        $this->assertNull($selector->getAttribute());
        $this->assertFalse($selector->hasAttribute());
    }

    public function testCreateSelectorWithAttribute(): void
    {
        $selector = Selector::fromString('a@href');

        $this->assertEquals('a', $selector->getCssSelector());
        $this->assertEquals('href', $selector->getAttribute());
        $this->assertTrue($selector->hasAttribute());
    }

    public function testCreateSelectorWithDataAttribute(): void
    {
        $selector = Selector::fromString('div@data-id');

        $this->assertEquals('div', $selector->getCssSelector());
        $this->assertEquals('data-id', $selector->getAttribute());
        $this->assertTrue($selector->hasAttribute());
    }

    public function testCreateSelectorWithComplexCss(): void
    {
        $selector = Selector::fromString('div.container > a.link@href');

        $this->assertEquals('div.container > a.link', $selector->getCssSelector());
        $this->assertEquals('href', $selector->getAttribute());
        $this->assertTrue($selector->hasAttribute());
    }
}

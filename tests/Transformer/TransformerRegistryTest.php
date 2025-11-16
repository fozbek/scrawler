<?php

namespace Scrawler\Tests\Transformer;

use PHPUnit\Framework\TestCase;
use Scrawler\Transformer\TransformerRegistry;

final class TransformerRegistryTest extends TestCase
{
    private TransformerRegistry $registry;

    protected function setUp(): void
    {
        $this->registry = new TransformerRegistry();
    }

    public function testHasBuiltInTransformers(): void
    {
        $this->assertTrue($this->registry->has("trim"));
        $this->assertTrue($this->registry->has("float"));
        $this->assertTrue($this->registry->has("int"));
        $this->assertTrue($this->registry->has("upper"));
        $this->assertTrue($this->registry->has("lower"));
    }

    public function testGetTransformer(): void
    {
        $transformer = $this->registry->get("trim");

        $this->assertNotNull($transformer);

        if ($transformer !== null) {
            $this->assertEquals("trim", $transformer->getName());
        }
    }

    public function testGetNonExistentTransformer(): void
    {
        $transformer = $this->registry->get("nonexistent");

        $this->assertNull($transformer);
    }

    public function testApplyChainSingleTransformer(): void
    {
        $result = $this->registry->applyChain("  hello  ", "trim");

        $this->assertEquals("hello", $result);
    }

    public function testApplyChainMultipleTransformers(): void
    {
        $result = $this->registry->applyChain("  hello  ", "trim|upper");

        $this->assertEquals("HELLO", $result);
    }

    public function testApplyChainWithSpaces(): void
    {
        $result = $this->registry->applyChain("  test  ", " trim | upper ");

        $this->assertEquals("TEST", $result);
    }

    public function testApplyChainTypeConversions(): void
    {
        $result = $this->registry->applyChain("  123.45  ", "trim|float");

        $this->assertSame(123.45, $result);
    }

    public function testApplyChainIgnoresUnknownTransformers(): void
    {
        $result = $this->registry->applyChain("test", "trim|unknown|upper");

        $this->assertEquals("TEST", $result);
    }
}

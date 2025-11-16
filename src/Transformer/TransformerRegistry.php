<?php

namespace Scrawler\Transformer;

use Scrawler\Transformer\Transformers\AbsTransformer;
use Scrawler\Transformer\Transformers\BasenameTransformer;
use Scrawler\Transformer\Transformers\BoolTransformer;
use Scrawler\Transformer\Transformers\DirnameTransformer;
use Scrawler\Transformer\Transformers\FloatTransformer;
use Scrawler\Transformer\Transformers\IntTransformer;
use Scrawler\Transformer\Transformers\JsonTransformer;
use Scrawler\Transformer\Transformers\LowerTransformer;
use Scrawler\Transformer\Transformers\LtrimTransformer;
use Scrawler\Transformer\Transformers\Md5Transformer;
use Scrawler\Transformer\Transformers\RtrimTransformer;
use Scrawler\Transformer\Transformers\Sha1Transformer;
use Scrawler\Transformer\Transformers\StringTransformer;
use Scrawler\Transformer\Transformers\StripTagsTransformer;
use Scrawler\Transformer\Transformers\TimestampTransformer;
use Scrawler\Transformer\Transformers\TrimTransformer;
use Scrawler\Transformer\Transformers\UcfirstTransformer;
use Scrawler\Transformer\Transformers\UcwordsTransformer;
use Scrawler\Transformer\Transformers\UpperTransformer;
use Scrawler\Transformer\Transformers\UrldecodeTransformer;
use Scrawler\Transformer\Transformers\UrlencodeTransformer;

/**
 * Registry of built-in transformers.
 */
final class TransformerRegistry
{
    /**
     * @var array<string, TransformerInterface>
     */
    private array $transformers = [];

    public function __construct()
    {
        $this->registerBuiltInTransformers();
    }

    /**
     * Register a transformer.
     */
    public function register(TransformerInterface $transformer): void
    {
        $this->transformers[$transformer->getName()] = $transformer;
    }

    /**
     * Get a transformer by name.
     *
     * @param string $name
     * @return TransformerInterface|null
     */
    public function get(string $name): ?TransformerInterface
    {
        return $this->transformers[$name] ?? null;
    }

    /**
     * Check if a transformer exists.
     */
    public function has(string $name): bool
    {
        return isset($this->transformers[$name]);
    }

    /**
     * Apply a chain of transformers to a value.
     *
     * @param mixed $value
     * @param string $transformerChain Pipe-separated transformer names (e.g., "trim|float")
     * @return mixed
     */
    public function applyChain($value, string $transformerChain)
    {
        $transformerNames = explode('|', $transformerChain);

        foreach ($transformerNames as $name) {
            $name = trim($name);
            if (empty($name)) {
                continue;
            }

            $transformer = $this->get($name);
            if ($transformer) {
                $value = $transformer->transform($value);
            }
        }

        return $value;
    }

    /**
     * Register all built-in transformers.
     */
    private function registerBuiltInTransformers(): void
    {
        // Type conversions
        $this->register(new IntTransformer());
        $this->register(new FloatTransformer());
        $this->register(new BoolTransformer());
        $this->register(new StringTransformer());

        // String operations
        $this->register(new TrimTransformer());
        $this->register(new LtrimTransformer());
        $this->register(new RtrimTransformer());
        $this->register(new UpperTransformer());
        $this->register(new LowerTransformer());
        $this->register(new UcfirstTransformer());
        $this->register(new UcwordsTransformer());
        $this->register(new StripTagsTransformer());

        // URL/Path
        $this->register(new BasenameTransformer());
        $this->register(new DirnameTransformer());
        $this->register(new UrlencodeTransformer());
        $this->register(new UrldecodeTransformer());

        // Parsing
        $this->register(new JsonTransformer());
        $this->register(new TimestampTransformer());

        // Utility
        $this->register(new AbsTransformer());
        $this->register(new Md5Transformer());
        $this->register(new Sha1Transformer());
    }
}

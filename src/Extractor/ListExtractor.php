<?php

namespace Scrawler\Extractor;

/**
 * Extracts repeated elements as a list.
 *
 * Supports both old and new syntax formats.
 * Old: ['list-selector' => '...', 'content' => [...]]
 * New: ['.selector' => [...], 'limit' => 10, 'offset' => 0]
 */
final class ListExtractor implements ExtractorInterface
{
    private ExtractorFactory $factory;

    public function __construct(ExtractorFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param mixed $context
     * @param mixed $value
     * @return array<int, array<int|string, mixed>>
     */
    public function extract($context, $value): array
    {
        if (!$this->canHandle($value)) {
            return [];
        }

        // Detect format and normalize
        $normalized = $this->normalizeSchema($value);

        $items = [];
        $elements = $context->find($normalized["selector"]);

        // Apply offset
        if ($normalized["offset"] > 0) {
            $elements = array_slice($elements, $normalized["offset"]);
        }

        // Apply limit
        if ($normalized["limit"] !== null) {
            $elements = array_slice($elements, 0, $normalized["limit"]);
        }

        foreach ($elements as $element) {
            $item = [];
            foreach ($normalized["content"] as $key => $contentValue) {
                $extractor = $this->factory->create($contentValue);
                $item[$key] = $extractor->extract($element, $contentValue);
            }
            $items[] = $item;
        }

        return $items;
    }

    public function canHandle($value): bool
    {
        if (!is_array($value)) {
            return false;
        }

        // Old format: has 'list-selector' key
        if (isset($value["list-selector"]) && isset($value["content"])) {
            return is_array($value["content"]);
        }

        // New format: search for a CSS selector key (not 'limit' or 'offset')
        foreach ($value as $key => $val) {
            if (is_string($key) && $key !== "limit" && $key !== "offset") {
                if ($this->isCssSelector($key) && is_array($val)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Normalize schema to consistent format.
     *
     * @param array<string, mixed> $value
     * @return array{selector: string, content: array<string, mixed>, limit: int|null, offset: int}
     */
    private function normalizeSchema(array $value): array
    {
        // Old format
        if (isset($value["list-selector"])) {
            return [
                "selector" => $value["list-selector"],
                "content" => $value["content"],
                "limit" => $value["limit"] ?? null,
                "offset" => $value["offset"] ?? 0,
            ];
        }

        // New format - find the selector key
        $selector = null;
        $content = [];
        $limit = null;
        $offset = 0;

        foreach ($value as $key => $val) {
            if (is_string($key)) {
                if ($key === "limit" && is_int($val)) {
                    $limit = $val;
                } elseif ($key === "offset" && is_int($val)) {
                    $offset = $val;
                } elseif ($this->isCssSelector($key) && is_array($val)) {
                    $selector = $key;
                    $content = $val;
                }
            }
        }

        return [
            "selector" => $selector ?? "",
            "content" => $content,
            "limit" => $limit,
            "offset" => $offset,
        ];
    }

    /**
     * Check if a string looks like a CSS selector.
     */
    private function isCssSelector(string $str): bool
    {
        // Simple heuristic: starts with . # [ or contains common CSS selector chars
        return str_starts_with($str, ".") ||
                str_starts_with($str, "#") ||
                str_starts_with($str, "[") ||
                preg_match('/^[a-z][a-z0-9]*$/i', $str) || // tag name
                str_contains($str, " ") || // descendant selector
                str_contains($str, ">") || // child selector
                str_contains($str, "@"); // attribute selector
    }
}

<?php

namespace Scrawler;

use DiDom\Document;
use DiDom\Element;

/**
 * Handles the extraction logic for Scrawler.
 */
class ScrawlerExtractor
{
    /**
     * Recursively extract data from the document using the schema.
     * @param Document|Element $context
     * @param array $schema
     * @return array
     */
    public static function extract($context, array $schema): array
    {
        $result = [];
        foreach ($schema as $key => $value) {
            if (self::isList($value)) {
                $result[$key] = self::extractList($context, $value);
            } elseif (self::isAttributeSelector($value)) {
                $result[$key] = self::extractAttribute($context, $value);
            } elseif (self::isTextSelector($value)) {
                $result[$key] = self::extractText($context, $value);
            } elseif ($value === null) {
                $result[$key] = self::extractCurrentText($context);
            } else {
                $result[$key] = null;
            }
        }
        return $result;
    }

    private static function isList($value): bool
    {
        return is_array($value) && isset($value['list-selector'], $value['content']);
    }

    private static function extractList($context, $value): array
    {
        $items = [];
        foreach ($context->find($value['list-selector']) as $el) {
            $items[] = self::extract($el, $value['content']);
        }
        return $items;
    }

    private static function isAttributeSelector($value): bool
    {
        return is_string($value) && strpos($value, '@') !== false;
    }

    private static function extractAttribute($context, $value)
    {
        [$selector, $attr] = explode('@', $value, 2);
        $el = $context->find($selector)[0] ?? null;
        return $el ? $el->attr($attr) : null;
    }

    private static function isTextSelector($value): bool
    {
        return is_string($value);
    }

    private static function extractText($context, $value)
    {
        $el = $context->find($value)[0] ?? null;
        return $el ? trim($el->text()) : null;
    }

    private static function extractCurrentText($context)
    {
        return method_exists($context, 'text') ? trim($context->text()) : null;
    }
}

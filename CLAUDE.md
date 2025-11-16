# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Scrawler is a modern, schema-based web scraping library for PHP with powerful transformer support. It features a clean, intuitive syntax that works great for both manual use and JSON APIs. Built with clean architecture principles and SOLID design patterns.

### PHP 8.4 Compatibility

The library includes a `Bootstrap` class that handles PHP 8.4 deprecation warnings from vendor libraries (DiDom, Guzzle) which haven't been updated yet for the new implicitly nullable parameter requirements. The Bootstrap class uses a custom error handler that only suppresses E_DEPRECATED warnings from vendor code, keeping your own code's warnings intact.

## Development Commands

### Testing
```bash
# Run all tests (using Herd PHP on Windows)
/c/Users/fozbek/.config/herd/bin/php.bat vendor/bin/phpunit tests

# Or using composer
composer test

# Specific test
./vendor/bin/phpunit tests/ScrawlerNewSyntaxTest.php

# With coverage
composer coverage
```

### Static Analysis
```bash
composer analyse
```

## Architecture

### New Features (Latest)

**Transformer System:**
- 20+ built-in transformers for data manipulation
- Pipe-separated chains: `'trim|float'`
- JSON-friendly (no closures needed)
- Extensible via TransformerInterface

**New List Syntax:**
- Old: `['list-selector' => '...', 'content' => [...]]`
- New: `['.selector' => [...], 'limit' => 10, 'offset' => 0]`
- Both syntaxes supported for backward compatibility

**Array Value Format:**
- Old: `'field' => 'selector'`
- New: `'field' => ['selector', 'transformers']`
- Example: `'price' => ['span.price', 'trim|float']`

### Core Components

**Scrawler** (`src/Scrawler.php`):
- Final class, main entry point
- Constructor accepts optional Guzzle Client
- `scrape()` method coordinates HTTP + extraction

**HttpClient** (`src/Http/HttpClient.php`):
- Wraps Guzzle with sensible defaults
- Default User-Agent provided
- Accepts custom headers

**Transformer System** (`src/Transformer/`):
- **TransformerInterface**: Simple `transform()` + `getName()`
- **TransformerRegistry**: Manages built-in transformers, applies chains
- **Transformers/**: 20+ transformer implementations

**Selector** (`src/Selector/Selector.php`):
- Value object for CSS selector parsing
- Handles `@attribute` syntax
- Immutable, created via `fromString()` factory

**Extractor System** (`src/Extractor/`):

- **ExtractorInterface**: `extract()` + `canHandle()`
- **ExtractorFactory**: Chain of Responsibility pattern
- **TextExtractor**: Handles strings and `[selector, transformers]` arrays
- **CurrentTextExtractor**: Handles `null` and `[null, transformers]` arrays
- **ListExtractor**: Handles both old and new list syntax
- **NullExtractor**: Safe fallback

### Data Flow

1. `Scrawler::scrape($urlOrHtml, $schema, $isHtml)`
2. If URL: `HttpClient::fetch()` retrieves HTML
3. Create DiDom Document
4. For each schema key-value:
   - `ExtractorFactory::create()` finds matching extractor
   - Extractor processes value (with transformers if applicable)
5. Return result array

### Schema Processing

**Extractor Selection Order** (Chain of Responsibility):
1. **ListExtractor**: Arrays with selector key or `list-selector` key
2. **CurrentTextExtractor**: `null` or `[null, transformers]`
3. **TextExtractor**: Strings or `[string, transformers]`
4. **NullExtractor**: Everything else

**Key Point:** Use `array_key_exists()` not `isset()` when checking for null values in arrays!

### Transformer System

**Built-in Transformers:**
- Type: int, float, bool, string
- String: trim, ltrim, rtrim, upper, lower, ucfirst, ucwords, strip_tags
- URL: basename, dirname, urlencode, urldecode
- Parse: json, timestamp
- Utility: abs, md5, sha1

**Applying Transformers:**
```php
$registry->applyChain($value, 'trim|float'); // Applies trim, then float
```

**Adding New Transformer:**
1. Create class in `src/Transformer/Transformers/`
2. Implement `TransformerInterface`
3. Add to `TransformerRegistry::registerBuiltInTransformers()`
4. Write tests
5. Update README

### List Syntax Detection

**Old Format:**
```php
['list-selector' => 'li', 'content' => [...]]
```

**New Format:**
```php
['.selector' => [...], 'limit' => 10, 'offset' => 0]
```

Detection logic in `ListExtractor::canHandle()`:
- Check for `list-selector` key (old format)
- Or find CSS selector key that's not `limit`/`offset` (new format)

### Important Implementation Notes

1. **isset() vs array_key_exists()**
   - `isset($arr[0])` returns `false` if `$arr[0] === null`
   - Always use `array_key_exists(0, $arr)` when checking for null values

2. **Transformer Registry**
   - Transformers created once in constructor (not per-call)
   - Registry injected into extractors via ExtractorFactory
   - Unknown transformers silently ignored in chain

3. **CSS Selector Detection**
   - Simple heuristic in `ListExtractor::isCssSelector()`
   - Checks for `.`, `#`, `[`, tag names, spaces, `>`, `@`
   - Could be improved with more sophisticated parsing if needed

4. **Attribute Extraction from Current Element**
   - Use `@attribute` syntax to extract attribute from current context element
   - Example: In a list, `'id' => '@id'` extracts the id attribute from each list item
   - TextExtractor checks if CSS selector is empty and context is Element
   - If so, extracts attribute directly from context instead of calling find()

5. **Backward Compatibility**
   - Both old and new list syntax supported
   - Simple string selectors still work
   - No breaking changes

## Testing Strategy

### Test Organization
- `tests/ScrawlerTest.php`: Original integration tests
- `tests/ScrawlerNewSyntaxTest.php`: New syntax features
- `tests/Transformer/TransformerRegistryTest.php`: Transformer tests
- `tests/Extractor/`: Unit tests for extractors
- `tests/Selector/`: Selector value object tests

### Test Patterns

**Testing Transformers:**
```php
$result = $this->registry->applyChain('  hello  ', 'trim|upper');
$this->assertEquals('HELLO', $result);
```

**Testing New List Syntax:**
```php
$schema = [
    'items' => [
        'li' => ['text' => null],
        'limit' => 3
    ]
];
```

**Testing Array Format with Transformers:**
```php
$schema = [
    'price' => ['span.price', 'trim|float']
];
```

## Common Tasks

### Adding a New Transformer

1. Create `src/Transformer/Transformers/MyTransformer.php`:
```php
final class MyTransformer implements TransformerInterface
{
    public function transform($value) {
        // Your logic
        return $transformed;
    }

    public function getName(): string {
        return 'my_transformer';
    }
}
```

2. Register in `TransformerRegistry::registerBuiltInTransformers()`:
```php
$this->register(new MyTransformer());
```

3. Add tests in `tests/Transformer/`

4. Update README with transformer documentation

### Debugging Schema Issues

1. Check which extractor is selected:
   ```php
   $factory = new ExtractorFactory();
   $extractor = $factory->create($schemaValue);
   var_dump(get_class($extractor));
   ```

2. Verify extractor order in `ExtractorFactory::__construct()`

3. Check `canHandle()` logic for each extractor

4. For list issues, check `ListExtractor::normalizeSchema()`

## JSON API Considerations

**Works seamlessly:**
```json
{
  "price": ["span.price", "trim|float"],
  "items": {
    "li": {"text": [null, "trim|upper"]},
    "limit": 10
  }
}
```

**Not supported in JSON:**
- PHP closures/callbacks
- Complex filtering logic

**Solution:** API consumers should filter/process data after extraction.

## Performance Notes

- Transformers created once (not per extraction)
- Selector parsing not cached (but very fast)
- No reflection or dynamic method calls
- All type checks via `instanceof` and `is_*` functions

## Code Style

- PSR-12 coding standard
- Final classes by default
- Strict types in all files
- Early returns for guard clauses
- Meaningful names, no abbreviations
- PHPDoc with generic types

## Dependencies

- PHP 8.1+
- DiDom 2.0+
- Guzzle 6.0|7.0+
- PHPUnit 9
- PHPStan 0.12+ (max level)

## Stats

- **Files**: 30+ source files
- **Tests**: 47 tests, 107 assertions
- **Transformers**: 20+ built-in
- **PHPStan**: Max level, 0 errors
- **Test Coverage**: Comprehensive

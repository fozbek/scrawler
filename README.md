# Scrawler

A modern, schema-based web scraping library for PHP with powerful transformers and a clean, intuitive syntax. Perfect for both manual use and API integration.

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![PHP Version](https://img.shields.io/badge/php-%5E8.1-blue.svg)](https://php.net)

## Features

- **Intuitive Schema Syntax**: Easy to write by hand and by AI
- **Built-in Transformers**: 20+ transformers for data manipulation (trim, float, int, upper, lower, etc.)
- **Flexible Lists**: Support for limit and offset
- **JSON-Friendly**: Perfect for API usage
- **Type-Safe**: Full PHPStan max level compliance
- **Clean Architecture**: SOLID principles, no anti-patterns
- **Well-Tested**: 47 tests, 107 assertions

## Installation

```bash
composer require fozbek/scrawler
```

## Quick Start

```php
use Scrawler\Bootstrap;
use Scrawler\Scrawler;

// Handle PHP 8.4 deprecation warnings from vendor libraries (optional)
Bootstrap::init();

$scrawler = new Scrawler();

$schema = [
    'title' => 'h1',
    'price' => ['span.price', 'trim|float'],
    'items' => [
        'li' => [
            'text' => [null, 'trim|upper']
        ],
        'limit' => 5
    ]
];

$data = $scrawler->scrape('https://example.com', $schema);
```

### PHP 8.4 Compatibility

If you're running PHP 8.4+, you may see deprecation warnings from vendor libraries (DiDom, Guzzle) related to implicitly nullable parameters. These are harmless but can clutter output. Use `Bootstrap::init()` to suppress these vendor-specific warnings:

```php
use Scrawler\Bootstrap;

Bootstrap::init(); // Call once at the start of your script
```

This only suppresses deprecation warnings from vendor code, keeping your own code's warnings intact.

## Schema Syntax

### Simple Text Extraction

```php
$schema = [
    'title' => 'h1',
    'description' => '.content p'
];
```

### Attribute Extraction

```php
$schema = [
    'image' => 'img@src',
    'link' => 'a@href',
    'dataId' => 'div@data-id'
];
```

**Extracting attributes from the current element** (useful in lists):

```php
$schema = [
    'items' => [
        '.product' => [
            'id' => '@id',              // Get id attribute from .product element
            'data' => '@data-value',    // Get data-value attribute
            'name' => '.title'          // Get text from nested .title
        ]
    ]
];
```

### Transformers

Apply transformations using pipe-separated transformer names:

```php
$schema = [
    'price' => ['span.price', 'trim|float'],
    'name' => ['.product-name', 'trim|upper'],
    'url' => ['a@href', 'urldecode']
];
```

**Available Transformers:**

**Type Conversions:**
- `int`, `float`, `bool`, `string`

**String Operations:**
- `trim`, `ltrim`, `rtrim`
- `upper`, `lower`, `ucfirst`, `ucwords`
- `strip_tags`

**URL/Path:**
- `basename`, `dirname`
- `urlencode`, `urldecode`

**Parsing:**
- `json` - decode JSON strings
- `timestamp` - convert dates to Unix timestamp

**Utility:**
- `abs` - absolute value
- `md5`, `sha1` - hashing

### Lists (New Syntax)

**Simple list:**
```php
$schema = [
    'items' => [
        'li' => [
            'text' => null  // Current element text
        ]
    ]
];
```

**List with transformers:**
```php
$schema = [
    'products' => [
        '.product' => [
            'name' => ['.name', 'trim|ucwords'],
            'price' => ['.price', 'trim|float']
        ]
    ]
];
```

**List with limit and offset:**
```php
$schema = [
    'items' => [
        'li' => ['text' => null],
        'limit' => 10,    // Take only first 10
        'offset' => 5     // Skip first 5
    ]
];
```

**Old syntax still supported:**
```php
$schema = [
    'items' => [
        'list-selector' => 'li',
        'content' => [
            'text' => null
        ]
    ]
];
```

### Nested Lists

```php
$schema = [
    'categories' => [
        '.category' => [
            'name' => '.category-name',
            'products' => [
                '.product' => [
                    'name' => ['.name', 'trim'],
                    'price' => ['.price', 'trim|float']
                ],
                'limit' => 5
            ]
        ]
    ]
];
```

## Examples

### Scraping with Transformers

```php
$html = '
    <div class="product">
        <h2>  wireless headphones  </h2>
        <span class="price">  $59.99  </span>
        <a href="/products/item%20123">Details</a>
    </div>
';

$schema = [
    'name' => ['h2', 'trim|ucwords'],
    'price' => ['.price', 'trim|float'],
    'url' => ['a@href', 'urldecode']
];

$result = $scrawler->scrape($html, $schema, true);

// Output:
// [
//     'name' => 'Wireless Headphones',
//     'price' => 59.99,
//     'url' => '/products/item 123'
// ]
```

### Scraping Lists with Limits

```php
$html = '<li>1</li><li>2</li><li>3</li><li>4</li><li>5</li>';

$schema = [
    'items' => [
        'li' => ['text' => null],
        'offset' => 1,
        'limit' => 3
    ]
];

$result = $scrawler->scrape($html, $schema, true);

// Output: ['items' => [['text' => '2'], ['text' => '3'], ['text' => '4']]]
```

### Complex Real-World Example

```php
$schema = [
    'title' => ['h1', 'trim|upper'],
    'author' => '.meta .author',
    'publishedAt' => ['.meta .date', 'timestamp'],
    'content' => ['.content', 'trim|strip_tags'],
    'tags' => [
        '.tag' => [
            'name' => [null, 'trim|lower'],
            'url' => ['a@href', 'urldecode']
        ],
        'limit' => 10
    ]
];
```

## JSON API Usage

The schema syntax is designed to work seamlessly with JSON:

```json
{
  "title": ["h1", "trim|upper"],
  "price": ["span.price", "trim|float"],
  "products": {
    ".product": {
      "name": [".name", "trim"],
      "price": [".price", "trim|float"]
    },
    "limit": 10,
    "offset": 0
  }
}
```

**Note:** Callbacks and filtering should be handled by the API consumer after receiving the data.

### Custom HTTP Client

```php
use GuzzleHttp\Client;
use Scrawler\Scrawler;

$client = new Client([
    'timeout' => 30,
    'headers' => ['User-Agent' => 'My Bot/1.0'],
    'proxy' => 'http://proxy.example.com:8080'
]);

$scrawler = new Scrawler($client);
```

## Testing

```bash
# Run all tests
composer test

# Run specific test
./vendor/bin/phpunit tests/ScrawlerNewSyntaxTest.php

# With coverage
composer coverage
```

## Static Analysis

```bash
composer analyse
```

**PHPStan Level:** Max (strictest)

## Requirements

- PHP 8.1 or higher
- ext-dom
- Guzzle 6.0 or 7.0+
- DiDom 2.0+

## License

MIT License - see [LICENSE](LICENSE)

## Contributing

Contributions welcome! Please ensure:
- All tests pass
- PHPStan analysis passes
- Follow PSR-12

## Author

Fatih Özbek - [mail@fatih.dev](mailto:mail@fatih.dev)

## Credits

- [Guzzle](https://github.com/guzzle/guzzle) - HTTP client
- [DiDom](https://github.com/Imangazaliev/DiDom) - DOM parsing

<?php

namespace Scrawler\Tests;

use PHPUnit\Framework\TestCase;
use Scrawler\Scrawler;

final class ScrawlerNewSyntaxTest extends TestCase
{
    private Scrawler $scrawler;

    protected function setUp(): void
    {
        $this->scrawler = new Scrawler();
    }

    public function testArraySyntaxWithTransformers(): void
    {
        $html = "<div><span>  123.45  </span></div>";
        $schema = [
            "price" => ["span", "trim|float"],
        ];

        $result = $this->scrawler->scrape($html, $schema, true);

        $this->assertSame(123.45, $result["price"]);
    }

    public function testArraySyntaxMultipleTransformers(): void
    {
        $html = "<div><p>  hello world  </p></div>";
        $schema = [
            "text" => ["p", "trim|upper"],
        ];

        $result = $this->scrawler->scrape($html, $schema, true);

        $this->assertEquals("HELLO WORLD", $result["text"]);
    }

    public function testArraySyntaxWithAttribute(): void
    {
        $html = '<a href="/path/to/page">Link</a>';
        $schema = [
            "file" => ["a@href", "basename"],
        ];

        $result = $this->scrawler->scrape($html, $schema, true);

        $this->assertEquals("page", $result["file"]);
    }

    public function testNewListSyntax(): void
    {
        $html = '
            <div class="item">Item 1</div>
            <div class="item">Item 2</div>
            <div class="item">Item 3</div>
        ';

        $schema = [
            "items" => [
                ".item" => [
                    "text" => null,
                ],
            ],
        ];

        $result = $this->scrawler->scrape($html, $schema, true);

        $this->assertCount(3, $result["items"]);
        $this->assertEquals("Item 1", $result["items"][0]["text"]);
        $this->assertEquals("Item 2", $result["items"][1]["text"]);
    }

    public function testNewListSyntaxWithLimit(): void
    {
        $html = '
            <li>1</li><li>2</li><li>3</li><li>4</li><li>5</li>
        ';

        $schema = [
            "items" => [
                "li" => ["text" => null],
                "limit" => 3,
            ],
        ];

        $result = $this->scrawler->scrape($html, $schema, true);

        $this->assertCount(3, $result["items"]);
        $this->assertEquals("1", $result["items"][0]["text"]);
        $this->assertEquals("3", $result["items"][2]["text"]);
    }

    public function testNewListSyntaxWithOffset(): void
    {
        $html = '
            <li>1</li><li>2</li><li>3</li><li>4</li><li>5</li>
        ';

        $schema = [
            "items" => [
                "li" => ["text" => null],
                "offset" => 2,
            ],
        ];

        $result = $this->scrawler->scrape($html, $schema, true);

        $this->assertCount(3, $result["items"]);
        $this->assertEquals("3", $result["items"][0]["text"]);
        $this->assertEquals("5", $result["items"][2]["text"]);
    }

    public function testNewListSyntaxWithLimitAndOffset(): void
    {
        $html = '
            <li>1</li><li>2</li><li>3</li><li>4</li><li>5</li>
        ';

        $schema = [
            "items" => [
                "li" => ["text" => null],
                "offset" => 1,
                "limit" => 2,
            ],
        ];

        $result = $this->scrawler->scrape($html, $schema, true);

        $this->assertCount(2, $result["items"]);
        $this->assertEquals("2", $result["items"][0]["text"]);
        $this->assertEquals("3", $result["items"][1]["text"]);
    }

    public function testNewListSyntaxWithTransformers(): void
    {
        $html = '
            <div class="product">
                <span class="price">  $10.99  </span>
            </div>
            <div class="product">
                <span class="price">  $20.50  </span>
            </div>
        ';

        $schema = [
            "products" => [
                ".product" => [
                    "price" => [".price", "trim|strip_tags"],
                ],
            ],
        ];

        $result = $this->scrawler->scrape($html, $schema, true);

        $this->assertCount(2, $result["products"]);
        $this->assertEquals('$10.99', $result["products"][0]["price"]);
        $this->assertEquals('$20.50', $result["products"][1]["price"]);
    }

    public function testBackwardCompatibilityOldListSyntax(): void
    {
        $html = "<li>A</li><li>B</li>";

        $schema = [
            "items" => [
                "list-selector" => "li",
                "content" => [
                    "text" => null,
                ],
            ],
        ];

        $result = $this->scrawler->scrape($html, $schema, true);

        $this->assertCount(2, $result["items"]);
        $this->assertEquals("A", $result["items"][0]["text"]);
        $this->assertEquals("B", $result["items"][1]["text"]);
    }

    public function testComplexSchemaNewSyntax(): void
    {
        $html = '
            <article>
                <h1>  Article Title  </h1>
                <div class="meta">
                    <span class="author">John Doe</span>
                    <span class="date">2024-01-15</span>
                </div>
                <div class="tag">php</div>
                <div class="tag">web</div>
            </article>
        ';

        $schema = [
            "title" => ["h1", "trim|upper"],
            "author" => ".meta .author",
            "timestamp" => [".meta .date", "timestamp"],
            "tags" => [
                ".tag" => [
                    "name" => [null, "trim|lower"],
                ],
                "limit" => 10,
            ],
        ];

        $result = $this->scrawler->scrape($html, $schema, true);

        $this->assertEquals("ARTICLE TITLE", $result["title"]);
        $this->assertEquals("John Doe", $result["author"]);
        $this->assertIsInt($result["timestamp"]);
        $this->assertCount(2, $result["tags"]);
        $this->assertEquals("php", $result["tags"][0]["name"]);
        $this->assertEquals("web", $result["tags"][1]["name"]);
    }

    public function testAttributeFromCurrentElement(): void
    {
        $html = '
            <div class="item" id="item1" data-value="100">First</div>
            <div class="item" id="item2" data-value="200">Second</div>
        ';

        $schema = [
            "items" => [
                ".item" => [
                    "id" => "@id",
                    "value" => "@data-value",
                    "text" => null,
                ],
            ],
        ];

        $result = $this->scrawler->scrape($html, $schema, true);

        $this->assertCount(2, $result["items"]);
        $this->assertEquals("item1", $result["items"][0]["id"]);
        $this->assertEquals("100", $result["items"][0]["value"]);
        $this->assertEquals("First", $result["items"][0]["text"]);
        $this->assertEquals("item2", $result["items"][1]["id"]);
        $this->assertEquals("200", $result["items"][1]["value"]);
        $this->assertEquals("Second", $result["items"][1]["text"]);
    }
}

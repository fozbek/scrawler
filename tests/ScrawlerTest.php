<?php

namespace Scrawler\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Scrawler\Scrawler;

final class ScrawlerTest extends TestCase
{
    public function testScrapeTextFromHtml(): void
    {
        $html = "<div><h1>Title</h1><p>Description</p></div>";
        $schema = [
            "title" => "h1",
            "description" => "p",
        ];

        $scrawler = new Scrawler();
        $result = $scrawler->scrape($html, $schema, true);

        $this->assertEquals("Title", $result["title"]);
        $this->assertEquals("Description", $result["description"]);
    }

    public function testScrapeAttributeFromHtml(): void
    {
        $html = '<a href="https://example.com">Link</a>';
        $schema = ["url" => "a@href"];

        $scrawler = new Scrawler();
        $result = $scrawler->scrape($html, $schema, true);

        $this->assertEquals("https://example.com", $result["url"]);
    }

    public function testScrapeMultipleAttributes(): void
    {
        $html = '<img src="/image.jpg" alt="Test Image" data-id="123">';
        $schema = [
            "src" => "img@src",
            "alt" => "img@alt",
            "id" => "img@data-id",
        ];

        $scrawler = new Scrawler();
        $result = $scrawler->scrape($html, $schema, true);

        $this->assertEquals("/image.jpg", $result["src"]);
        $this->assertEquals("Test Image", $result["alt"]);
        $this->assertEquals("123", $result["id"]);
    }

    public function testScrapeList(): void
    {
        $html = "<ul><li>First</li><li>Second</li><li>Third</li></ul>";
        $schema = [
            "items" => [
                "list-selector" => "li",
                "content" => [
                    "text" => null,
                ],
            ],
        ];

        $scrawler = new Scrawler();
        $result = $scrawler->scrape($html, $schema, true);

        $this->assertCount(3, $result["items"]);
        $this->assertEquals("First", $result["items"][0]["text"]);
        $this->assertEquals("Second", $result["items"][1]["text"]);
        $this->assertEquals("Third", $result["items"][2]["text"]);
    }

    public function testScrapeNestedList(): void
    {
        $html = '
            <div class="product">
                <h2>Product 1</h2>
                <span class="price">$10</span>
            </div>
            <div class="product">
                <h2>Product 2</h2>
                <span class="price">$20</span>
            </div>
        ';

        $schema = [
            "products" => [
                "list-selector" => ".product",
                "content" => [
                    "name" => "h2",
                    "price" => ".price",
                ],
            ],
        ];

        $scrawler = new Scrawler();
        $result = $scrawler->scrape($html, $schema, true);

        $this->assertCount(2, $result["products"]);
        $this->assertEquals("Product 1", $result["products"][0]["name"]);
        $this->assertEquals('$10', $result["products"][0]["price"]);
        $this->assertEquals("Product 2", $result["products"][1]["name"]);
        $this->assertEquals('$20', $result["products"][1]["price"]);
    }

    public function testScrapeMissingElement(): void
    {
        $html = "<div>Content</div>";
        $schema = ["title" => "h1"];

        $scrawler = new Scrawler();
        $result = $scrawler->scrape($html, $schema, true);

        $this->assertNull($result["title"]);
    }

    public function testScrapeMissingAttribute(): void
    {
        $html = "<a>Link without href</a>";
        $schema = ["url" => "a@href"];

        $scrawler = new Scrawler();
        $result = $scrawler->scrape($html, $schema, true);

        $this->assertNull($result["url"]);
    }

    public function testScrapeEmptyList(): void
    {
        $html = "<ul></ul>";
        $schema = [
            "items" => [
                "list-selector" => "li",
                "content" => ["text" => null],
            ],
        ];

        $scrawler = new Scrawler();
        $result = $scrawler->scrape($html, $schema, true);

        $this->assertEmpty($result["items"]);
    }

    public function testScrapeComplexSchema(): void
    {
        $html = '
            <article>
                <h1>Article Title</h1>
                <p class="meta">By John Doe</p>
                <div class="content">
                    <p>Paragraph 1</p>
                    <p>Paragraph 2</p>
                </div>
                <ul class="tags">
                    <li><a href="/tag/php">PHP</a></li>
                    <li><a href="/tag/web">Web</a></li>
                </ul>
            </article>
        ';

        $schema = [
            "title" => "h1",
            "author" => ".meta",
            "tags" => [
                "list-selector" => ".tags li",
                "content" => [
                    "name" => "a",
                    "url" => "a@href",
                ],
            ],
        ];

        $scrawler = new Scrawler();
        $result = $scrawler->scrape($html, $schema, true);

        $this->assertEquals("Article Title", $result["title"]);
        $this->assertEquals("By John Doe", $result["author"]);
        $this->assertCount(2, $result["tags"]);
        $this->assertEquals("PHP", $result["tags"][0]["name"]);
        $this->assertEquals("/tag/php", $result["tags"][0]["url"]);
        $this->assertEquals("Web", $result["tags"][1]["name"]);
        $this->assertEquals("/tag/web", $result["tags"][1]["url"]);
    }

    public function testScrapeFromUrl(): void
    {
        $mockHtml =
            "<html><head><title>Test Page</title></head><body></body></html>";

        $mock = new MockHandler([new Response(200, [], $mockHtml)]);

        $client = new Client(["handler" => HandlerStack::create($mock)]);
        $scrawler = new Scrawler($client);

        $schema = ["title" => "title"];
        $result = $scrawler->scrape("https://example.com", $schema);

        $this->assertEquals("Test Page", $result["title"]);
    }

    public function testScrapeWithCustomClient(): void
    {
        $mockHtml = "<div><h1>Custom Client</h1></div>";

        $mock = new MockHandler([new Response(200, [], $mockHtml)]);

        $client = new Client([
            "handler" => HandlerStack::create($mock),
            "timeout" => 10,
        ]);

        $scrawler = new Scrawler($client);
        $schema = ["title" => "h1"];
        $result = $scrawler->scrape("https://example.com", $schema);

        $this->assertEquals("Custom Client", $result["title"]);
    }

    public function testScrapeTrimsWhitespace(): void
    {
        $html = "<p>  Text with spaces  </p>";
        $schema = ["text" => "p"];

        $scrawler = new Scrawler();
        $result = $scrawler->scrape($html, $schema, true);

        $this->assertEquals("Text with spaces", $result["text"]);
    }

    public function testScrapeInvalidSchemaValue(): void
    {
        $html = "<div>Content</div>";
        $schema = [
            "invalid" => 123,
            "object" => new \stdClass(),
        ];

        $scrawler = new Scrawler();
        $result = $scrawler->scrape($html, $schema, true);

        $this->assertNull($result["invalid"]);
        $this->assertNull($result["object"]);
    }
}

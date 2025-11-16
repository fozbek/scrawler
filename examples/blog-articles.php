<?php

/**
 * Example: Scraping Blog Articles with Nested Data
 *
 * Demonstrates:
 * - Nested lists (articles with tags)
 * - Timestamp transformation
 * - URL decoding
 * - Complex nested structures
 * - Combining multiple transformers
 */

use Scrawler\Bootstrap;
use Scrawler\Scrawler;

require_once dirname(__DIR__) . "/vendor/autoload.php";

// Handle PHP 8.4 deprecation warnings from vendor libraries
Bootstrap::init();

$scrawler = new Scrawler();

// Sample blog HTML
$html = '
<div class="blog">
    <article class="post">
        <h1 class="title">  Getting Started with PHP 8.1  </h1>
        <div class="meta">
            <span class="author">John Doe</span>
            <time class="date">2024-11-15 10:30:00</time>
            <span class="read-time">5 min read</span>
        </div>
        <div class="content">
            This is an introduction to PHP 8.1 features including enums,
            readonly properties, and more...
        </div>
        <div class="tags">
            <a href="/tag/php" class="tag">PHP</a>
            <a href="/tag/programming" class="tag">programming</a>
            <a href="/tag/web-dev" class="tag">web development</a>
        </div>
    </article>

    <article class="post">
        <h1 class="title">  Modern JavaScript Patterns  </h1>
        <div class="meta">
            <span class="author">Jane Smith</span>
            <time class="date">2024-11-14 15:45:00</time>
            <span class="read-time">8 min read</span>
        </div>
        <div class="content">
            Exploring modern JavaScript design patterns and best practices
            for building scalable applications...
        </div>
        <div class="tags">
            <a href="/tag/javascript" class="tag">JavaScript</a>
            <a href="/tag/patterns" class="tag">design patterns</a>
        </div>
    </article>
</div>
';

$schema = [
    "articles" => [
        "article.post" => [
            "title" => ["h1.title", "trim"],
            "author" => ".meta .author",
            "publishedAt" => [".meta .date", "timestamp"], // Convert to Unix timestamp
            "readTime" => [".meta .read-time", "trim"],
            "excerpt" => [".content", "trim|strip_tags"],
            "tags" => [
                ".tags .tag" => [
                    "name" => [null, "trim|lower"],
                    "slug" => ["@href", "basename"], // Extract just the tag name from URL
                ],
                "limit" => 5, // Max 5 tags per article
            ],
        ],
        "limit" => 20, // Max 20 articles
    ],
    "blog_title" => ["h1", "trim|upper"],
];

$result = $scrawler->scrape($html, $schema, true);

header("Content-Type: application/json");
echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

/* Output:
{
    "articles": [
        {
            "title": "Getting Started with PHP 8.1",
            "author": "John Doe",
            "publishedAt": 1731667800,
            "readTime": "5 min read",
            "excerpt": "This is an introduction to PHP 8.1 features including enums...",
            "tags": [
                {
                    "name": "php",
                    "slug": "php"
                },
                {
                    "name": "programming",
                    "slug": "programming"
                },
                {
                    "name": "web development",
                    "slug": "web-dev"
                }
            ]
        },
        {
            "title": "Modern JavaScript Patterns",
            "author": "Jane Smith",
            "publishedAt": 1731602700,
            "readTime": "8 min read",
            "excerpt": "Exploring modern JavaScript design patterns and best practices...",
            "tags": [
                {
                    "name": "javascript",
                    "slug": "javascript"
                },
                {
                    "name": "design patterns",
                    "slug": "patterns"
                }
            ]
        }
    ]
}
*/

// You can also use this for real blog scraping:
// $url = 'https://example-blog.com/articles';
// $result = $scrawler->scrape($url, $schema);

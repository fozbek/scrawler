<?php

/**
 * Example: Scraping Hacker News with new syntax
 *
 * Demonstrates:
 * - New list syntax with cleaner format
 * - Using transformers for data cleaning
 * - Limit to control number of items
 */

use Scrawler\Bootstrap;
use Scrawler\Scrawler;

require_once dirname(__DIR__) . "/vendor/autoload.php";

// Handle PHP 8.4 deprecation warnings from vendor libraries
Bootstrap::init();

$scrawler = new Scrawler();
$url = "https://news.ycombinator.com/";

// Hacker News structure: each story is a tr.athing element
$schema = [
    "threads" => [
        ".athing" => [
            "id" => "@id",
            "rank" => ["td span.rank", "trim"],
            "title" => ["td.title span.titleline a", "trim"],
            "url" => "td.title span.titleline a@href",
            "site" => ["td.title span.sitestr", "trim"],
        ],
        "limit" => 30, // Only get first 30 stories
    ],
];

$response = $scrawler->scrape($url, $schema);

// Pretty print JSON output
header("Content-Type: application/json");
echo json_encode(
    $response,
    JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
);

/* Output format:
{
  "threads": [
    {
      "id": "45947810",
      "rank": "1.",
      "title": "Open-source Zig book",
      "url": "https://www.zigbook.net",
      "site": "zigbook.net"
    },
    {
      "id": "45947770",
      "rank": "2.",
      "title": "Tracking users with favicons, even in incognito mode",
      "url": "https://github.com/jonasstrehle/supercookie",
      "site": "github.com/jonasstrehle"
    },
    ...
  ]
}
*/

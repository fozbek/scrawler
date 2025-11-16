<?php

/**
 * Example: Scraping Wikipedia main page headlines
 *
 * Demonstrates:
 * - Simple list extraction
 * - Using null to get current element text
 * - Transformer chains (trim|ucfirst)
 */

use Scrawler\Bootstrap;
use Scrawler\Scrawler;

require_once dirname(__DIR__) . "/vendor/autoload.php";

// Handle PHP 8.4 deprecation warnings from vendor libraries
Bootstrap::init();

$scrawler = new Scrawler();
$url = "https://en.wikipedia.org/wiki/Main_Page";

$schema = [
    "headlines" => [
        "#mp-upper .mp-h2" => [
            "headline" => [null, "trim|ucfirst"],
        ],
    ],
];

$result = $scrawler->scrape($url, $schema);

header("Content-Type: application/json");
echo json_encode(
    $result,
    JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
);

/* Output format:
{
  "headlines": [
    {
      "headline": "From today's featured article"
    },
    {
      "headline": "Did you know..."
    },
    ...
  ]
}
*/

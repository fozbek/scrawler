<?php

use Scrawler\Bootstrap;
use Scrawler\Scrawler;

require_once __DIR__ . "/vendor/autoload.php";

Bootstrap::init();

$html = '<tr class="athing submission" id="45947810"><td align="right" valign="top" class="title"><span class="rank">1.</span></td><td valign="top" class="votelinks"><center><a id="up_45947810"><div class="votearrow" title="upvote"></div></a></center></td><td class="title"><span class="titleline"><a href="https://www.zigbook.net">Open-source Zig book</a><span class="sitebit comhead"> (<a href="from?site=zigbook.net"><span class="sitestr">zigbook.net</span></a>)</span></span></td></tr>';

$scrawler = new Scrawler();

// Test 1: Simple selector
echo "Test 1: Simple tr.athing selector\n";
$schema1 = [
    "threads" => [
        "tr.athing" => [
            "title" => "span.titleline a",
        ],
    ],
];
$result1 = $scrawler->scrape($html, $schema1, true);
var_dump($result1);

// Test 2: With all fields
echo "\n\nTest 2: Full schema\n";
$schema2 = [
    "threads" => [
        "tr.athing" => [
            "id" => "@id",
            "rank" => "span.rank",
            "title" => "span.titleline a",
            "url" => "span.titleline a@href",
            "site" => "span.sitestr",
        ],
    ],
];
$result2 = $scrawler->scrape($html, $schema2, true);
var_dump($result2);

// Test 3: Try with table wrapper
echo "\n\nTest 3: Try direct element\n";
$schema3 = [
    "title" => "span.titleline a",
    "url" => "span.titleline a@href",
];
$result3 = $scrawler->scrape($html, $schema3, true);
var_dump($result3);

<?php

use Scrawler\Scrawler;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$scrawler = new Scrawler();

$url = 'https://news.ycombinator.com/';

$schema = [
    'threads' => [
        'list-selector' => 'tr.athing',
        'content' => [
            'title' => 'span.titleline > a',
            'link' => 'span.titleline > a@href'
        ]
    ]
];

$response = $scrawler->scrape($url, $schema);

// Pretty print JSON output
header('Content-Type: application/json');
echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);


/* $response is formatted

{
  "threads": [
    {
      "title": "Ask HN: Who is hiring? (August 2020)",
      "link": "https://news.ycombinator.com/item?id=24038520"
    },
    {
      "title": "The Art of Not Thinking",
      "link": "http://tiffanymatthe.com/not-thinking"
    },
    {
      "title": "Ask HN: Who wants to be hired? (August 2020)",
      "link": "https://news.ycombinator.com/item?id=24038518"
    },
...


 */
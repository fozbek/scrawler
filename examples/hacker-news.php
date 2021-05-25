<?php

use Scrawler\Scrawler;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$scrawler = new Scrawler();

$url = 'https://news.ycombinator.com/';

$schema = [
    'threads' => [
        'list-selector' => 'tr.athing',
        'content' => [
            'title' => '.storylink',
            'link' => '.storylink@href',
            'source' => '.sitebit.comhead > a'
        ]
    ]
];

$response = $scrawler->scrape($url, $schema);

echo json_encode($response);


/* $response is formatted

{
  "threads": [
    {
      "title": "Ask HN: Who is hiring? (August 2020)",
      "link": "https://news.ycombinator.com/item?id=24038520",
      "source": null
    },
    {
      "title": "The Art of Not Thinking",
      "link": "http://tiffanymatthe.com/not-thinking",
      "source": "tiffanymatthe.com"
    },
    {
      "title": "Ask HN: Who wants to be hired? (August 2020)",
      "link": "https://news.ycombinator.com/item?id=24038518",
      "source": null
    },
...


 */
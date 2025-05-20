<?php
// Example: Scrape Wikipedia main page headlines

use Scrawler\Scrawler;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$scrawler = new Scrawler();
$url = 'https://en.wikipedia.org/wiki/Main_Page';
$schema = [
    'headlines' => [
        'list-selector' => '#mp-upper .mp-h2',
        'content' => [
            'headline' => null
        ]
    ]
];
$result = $scrawler->scrape($url, $schema);
header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

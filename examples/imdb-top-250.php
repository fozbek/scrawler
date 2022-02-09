<?php

use Scrawler\Scrawler;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$scrawler = new Scrawler();

$url = 'https://www.imdb.com/chart/top/';

$schema = [
    'movies' => [
        'list-selector' => 'tbody.lister-list tr',
        'content' => [
            'name' => '.titleColumn a',
            'link' => '.titleColumn a@href',
            'rating' => '.ratingColumn strong'
        ]
    ]
];

$response = $scrawler->scrape($url, $schema);

echo json_encode($response, JSON_PRETTY_PRINT);


/* $response is formatted

{
  "movies": [
    {
      "name": "The Shawshank Redemption",
      "link": "https://www.imdb.com/title/tt0111161/?pf_rd_m=A2FGELUUNOQJNL&pf_rd_p=e31d89dd-322d-4646-8962-327b42fe94b1&pf_rd_r=C69B6F74KN9CSM0N5YA3&pf_rd_s=center-1&pf_rd_t=15506&pf_rd_i=top&ref_=chttp_tt_1",
      "rating": "9.2"
    },
    {
      "name": "The Godfather",
      "link": "https://www.imdb.com/title/tt0068646/?pf_rd_m=A2FGELUUNOQJNL&pf_rd_p=e31d89dd-322d-4646-8962-327b42fe94b1&pf_rd_r=C69B6F74KN9CSM0N5YA3&pf_rd_s=center-1&pf_rd_t=15506&pf_rd_i=top&ref_=chttp_tt_2",
      "rating": "9.1"
    },
    ...
  ]
}

 */
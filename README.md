# Scrawler

### Description
Simple, schema base scraping tool

### Installation
    composer require fozbek/scrawler

## Usage

#### Simple usage
Google Example
```php
$url = 'https://google.com';

$schema = [
    'title' => 'title',
    'a-tags' => [
        'selector' => 'a',
        'content' => [
            'text' => 'a',
            'url' => 'a@href',
        ],
    ],
];
$scrawler = new \Scrawler\Scrawler();
$response = $scrawler->scrape($url, $schema);

echo json_encode($response);
```
    
Response (Formatted)
    
    {
        "title": "Google",
        "a-tags": [
            {
                "text": "Grseller",
                "url": "https://www.google.com.tr/imghp?hl=tr&tab=wi"
            },
            {
                "text": "Haritalar",
                "url": "https://maps.google.com.tr/maps?hl=tr&tab=wl"
            }
            ...
        ]
    } 
    
#### Examples as Yaml
>You can test all of these in any xenforo forum. Example url: https://xenforo.com/community/forums/announcements/

- Scrape single selector
```php
$schema = [
    'forum-title' => '.p-body-header .p-title-value' 
];
``` 

- Loop selector
```php
$schema = [
    'threads' => [
        'selector' => '.structItem--thread',
        'content' => [
            'thread-title' => '.structItem-title',
            'thread-url' => '.structItem-title a@href',
            'last-update-date' => '.structItem-latestDate',
        ]
    ]
];
``` 

- Pagination
```php
$schema = [
    'title' => 'title',
    'pagination' => [
        'limit' => 3,
        'selector' => '.pageNav-jump--next@href',
    ],
];
```

- New Request
```php
$schema = [
    'login-page' => [
        'selector' => 'a.p-navgroup-link--logIn@href',
        'content' => [
            'title' => 'title',
        ],
    ],
];
```

- You can combine them :)
```php
$schema = [
    'title' => 'title',
    'threads' => [
        'selector' => '.structItem--thread',
        'content' => [
            'thread-detail' => [
                'selector' => '.structItem-title a@href',
                'content' => [
                    'thread-content' => '.message-body .bbWrapper',
                ],
            ],
        ],
    ],
    'pagination' => [
        'limit' => 3,
        'selector' => '.pageNav-jump--next@href',
    ],
];
```
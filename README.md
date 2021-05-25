# Scrawler

### Description
Simple, schema based scraping tool

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
        'list-selector' => 'a',
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
    
#### Examples
>You can test all of these in any site that uses xenforo. Example url: https://xenforo.com/community/forums/announcements/

- Single selector
```php
$schema = [
    'forum-title' => '.p-body-header .p-title-value' 
];
``` 

- Loop selector
```php
$schema = [
    'threads' => [
        'list-selector' => '.structItem--thread',
        'content' => [
            'thread-title' => '.structItem-title',
            'thread-url' => '.structItem-title a@href',
            'last-update-date' => '.structItem-latestDate',
        ]
    ]
];
``` 

- New Request
```php
$schema = [
    'login-page' => [
        'request-selector' => 'a.p-navgroup-link--logIn@href',
        'base-url' => 'https://xenforo.com',
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
        'list-selector' => '.structItem--thread',
        'content' => [
            'thread-detail' => [
                'request-selector' => '.structItem-title a@href',
                'base-url' => 'https://xenforo.com',
                'content' => [
                    'thread-content' => '.message-body .bbWrapper',
                ],
            ],
        ],
    ]
];
```

# Scrawler
This tool is developed with Yaml in mind. Using yaml is highly recommended but of course you can store the template as json in your DB or filesystem, you can use array as well.

Selectors almost the same with css selectors. There is only one different for now. Attributes can taken using @{attrName} format. Example: a@href, img@src

## Examples

### Simple usage
Google Example
```php
$url = 'https://google.com';

$template = '
title: title
a-tags:
  selector: a
  content:
    text: a
    url: a@href
';

$scrawler = new \Scrawler\Scrawler();
$response = $scrawler->scrape($url, $template);

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
    
### Examples as Yaml
>You can test all of these in any xenforo forum. Example url: https://xenforo.com/community/forums/announcements/

- Scrape single selector
```yaml
forum-title: .p-body-header .p-title-value 
``` 

- Loop selector
```yaml
threads:
  selector: .structItem--thread
  content:
    thread-title: .structItem-title
    thread-url: .structItem-title a@href
    last-update-date: .structItem-latestDate
``` 

- Pagination
```yaml
title: title
pagination:
  limit: 3
  selector: .pageNav-jump--next@href 
```



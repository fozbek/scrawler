<?php

namespace Scrawler\Model;

use PHPUnit\Framework\TestCase;

class ScrawlerDocumentTest extends TestCase
{
    public function test_first(): void
    {
        $document = new ScrawlerDocument(self::getHtmlContent());
        self::assertEquals($document->first(' ul li.item')->text(), 'li 1');

        self::assertIsArray($document->find('ul li.item'));
        self::assertCount(3, $document->find('ul li.item'));
    }

    private static function getHtmlContent(): string
    {
        return <<<EOT
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
</head>
<body>
    <ul>
        <li class="item">li 1</li>
        <li class="item">li 2</li>
        <li class="item">li 3</li>
    </ul>
</body>
</html>        
EOT;
    }
}

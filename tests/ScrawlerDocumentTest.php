<?php

namespace Scrawler\Model;

use PHPUnit\Framework\TestCase;

class ScrawlerDocumentTest extends TestCase
{
    public function test_first(): void
    {
        $document = new ScrawlerDocument(self::getHtmlContent(), 'ul li.item');
        self::assertEquals($document->first()->text(), 'li 1');
    }

    public function test_options(): void
    {
        $document = new ScrawlerDocument(self::getHtmlContent(), []);
        self::assertFalse($document->isListSelector());
        self::assertFalse($document->getListSelector());
        self::assertFalse($document->hasContent());
        self::assertFalse($document->getContent());

        $document = new ScrawlerDocument(self::getHtmlContent(), [
            'list-selector' => true,
            'content' => true,
        ]);
        self::assertTrue($document->isListSelector());
    }

    public function test_content(): void
    {
        $document = new ScrawlerDocument(self::getHtmlContent(), 'li.item');
        self::assertEquals('li 1', $document->first()->text());
    }

    public function test_html(): void
    {
        $document = new ScrawlerDocument(self::getHtmlContent(), 'li.item');
        self::assertEquals('li 1', $document->first()->text());
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

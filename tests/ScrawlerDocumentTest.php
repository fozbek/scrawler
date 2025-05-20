<?php

namespace Scrawler\Model;

use PHPUnit\Framework\TestCase;

class ScrawlerDocumentTest extends TestCase
{
    public function test_first(): void
    {
        $options = new ScrawlerOptions();
        $options->isHtml = true;
        $options->schema = ['foo' => 'span'];
        $options->urlOrHtml = '<div><span>bar</span></div>';
        $document = new ScrawlerDocument($options);
        $result = $document->extract();
        self::assertEquals(['foo' => 'bar'], $result);
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

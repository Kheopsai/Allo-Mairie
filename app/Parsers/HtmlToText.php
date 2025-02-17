<?php

namespace App\Parsers;

use InvalidArgumentException;
use Symfony\Component\DomCrawler\Crawler;

class HtmlToText
{
    protected array $defaultRemoveSelectors = [
        'title', 'nav', 'header', 'footer', 'script', 'style',
        '.navbar', '.header', '.footer', '.site-header',
        '.site-footer', '.ad-container', '[role="navigation"]',
    ];

    public function __construct(
        protected $html
    ) {}

    public function formatContent(array $customSelectors = []): string
    {
        if (! $this->html) {
            return '';
        }

        return $this->cleanHtml($this->html, $customSelectors);
    }

    public function cleanHtml(string $html, array $customSelectors = []): string
    {
        $selectors = array_merge($this->defaultRemoveSelectors, $customSelectors);
        $crawler = new Crawler($html);

        foreach ($selectors as $selector) {
            try {
                $crawler->filter($selector)->each(function (Crawler $node) {
                    $node->getNode(0)?->parentNode?->removeChild($node->getNode(0));
                });
            } catch (InvalidArgumentException $e) {
                continue;
            }
        }

        try {
            $mainContent = $crawler->filter('main')->html();

            return $this->wrapContent($mainContent);
        } catch (InvalidArgumentException $e) {
            return $this->wrapContent($crawler->html());
        }
    }

    protected function wrapContent(string $content): string
    {
        return <<<HTML
        <!DOCTYPE html>
        <html lang="">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title></title>
        </head>
        <body>
            $content
        </body>
        </html>
        HTML;
    }

    // Add content conversion methods
    public function toPlainText(string $html): string
    {
        $crawler = new Crawler($html);

        return $crawler->text();
    }

    public function getStructuredData(string $html): array
    {
        $crawler = new Crawler($html);

        return [
            'headings' => $this->extractHeadings($crawler),
            'links' => $this->extractLinks($crawler),
            'images' => $this->extractImages($crawler),
        ];
    }

    private function extractHeadings(Crawler $crawler): array
    {
        return $crawler->filter('h1, h2, h3, h4, h5, h6')->each(function (Crawler $node) {
            return [
                'level' => (int) substr($node->nodeName(), 1),
                'text' => $node->text(),
            ];
        });
    }

    private function extractLinks(Crawler $crawler): array
    {
        return $crawler->filter('a')->each(function (Crawler $node) {
            return [
                'text' => $node->text(),
                'href' => $node->attr('href'),
            ];
        });
    }

    private function extractImages(Crawler $crawler): array
    {
        return $crawler->filter('img')->each(function (Crawler $node) {
            return [
                'src' => $node->attr('src'),
                'alt' => $node->attr('alt'),
            ];
        });
    }
}

<?php

namespace App\Services\Content;

use Illuminate\Support\Str;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\GithubFlavoredMarkdownConverter;

class MarkdownService
{
    protected GithubFlavoredMarkdownConverter $converter;

    public function __construct()
    {
        $environment = new Environment([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
            'heading_permalink' => [
                'symbol' => '#',
                'insert' => 'after',
                'aria_hidden' => true,
            ],
        ]);

        $environment->addExtension(new CommonMarkCoreExtension);
        $environment->addExtension(new GithubFlavoredMarkdownExtension);
        $environment->addExtension(new HeadingPermalinkExtension);

        $this->converter = new GithubFlavoredMarkdownConverter([], $environment);
    }

    public function renderToHtml(string $markdown): string
    {
        return (string) $this->converter->convert($markdown);
    }

    public function excerpt(string $markdown, int $maxLength = 280): string
    {
        $text = $this->toPlainText($markdown);

        return Str::limit($text, $maxLength, '…');
    }

    public function readingTimeMinutes(string $markdown, int $wordsPerMinute = 200): int
    {
        $text = $this->toPlainText($markdown);
        $wordCount = str_word_count($text);

        return max(1, (int) ceil($wordCount / max(1, $wordsPerMinute)));
    }

    public function toPlainText(string $markdown): string
    {
        $html = $this->renderToHtml($markdown);

        return trim(html_entity_decode(strip_tags($html)));
    }
}

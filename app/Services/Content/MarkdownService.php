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
        $html = (string) $this->converter->convert($this->preprocessShortcodes($markdown));

        return $this->postprocessCallouts($html);
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

    protected function preprocessShortcodes(string $markdown): string
    {
        $kinds = ['note', 'tip', 'warning', 'success'];

        // Multiline form:
        // :::warning
        // line 1
        // line 2
        // :::
        $markdown = (string) preg_replace_callback(
            '/:::([a-zA-Z]+)\R(.*?)\R:::/s',
            function (array $matches) use ($kinds): string {
                $kind = strtolower((string) ($matches[1] ?? 'note'));
                $content = trim((string) ($matches[2] ?? ''));
                if (! in_array($kind, $kinds, true)) {
                    $kind = 'note';
                }

                return $this->toCalloutMarkdown($kind, $content);
            },
            $markdown,
        );

        // Single-line form:
        // :::warning something something :::
        $markdown = (string) preg_replace_callback(
            '/:::([a-zA-Z]+)\s+(.+?)\s+:::/m',
            function (array $matches) use ($kinds): string {
                $kind = strtolower((string) ($matches[1] ?? 'note'));
                $content = trim((string) ($matches[2] ?? ''));
                if (! in_array($kind, $kinds, true)) {
                    $kind = 'note';
                }

                return $this->toCalloutMarkdown($kind, $content);
            },
            $markdown,
        );

        return $markdown;
    }

    protected function toCalloutMarkdown(string $kind, string $content): string
    {
        $marker = strtoupper($kind);
        $lines = preg_split('/\R/', trim($content)) ?: [];
        $quoted = collect($lines)
            ->map(fn (string $line) => '> '.$line)
            ->implode("\n");

        return "> [!{$marker}]\n>\n{$quoted}\n";
    }

    protected function postprocessCallouts(string $html): string
    {
        return (string) preg_replace_callback(
            '/<blockquote>\s*<p>\[!(NOTE|TIP|WARNING|SUCCESS)\]<\/p>(.*?)<\/blockquote>/si',
            function (array $matches): string {
                $kind = strtolower((string) $matches[1]);
                $body = (string) ($matches[2] ?? '');
                $label = ucfirst($kind);

                return sprintf(
                    '<blockquote class="callout callout-%s"><p class="callout-title">%s</p>%s</blockquote>',
                    e($kind),
                    e($label),
                    $body,
                );
            },
            $html,
        );
    }
}

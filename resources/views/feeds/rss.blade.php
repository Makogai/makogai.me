<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0">
    <channel>
        <title>{{ config('app.name') }} Blog</title>
        <link>{{ $siteUrl }}</link>
        <description>Latest blog posts from {{ config('app.name') }}</description>
        <language>{{ str_replace('_', '-', app()->getLocale()) }}</language>
        <lastBuildDate>{{ now()->toRssString() }}</lastBuildDate>
        <atom:link xmlns:atom="http://www.w3.org/2005/Atom" href="{{ $feedUrl }}" rel="self" type="application/rss+xml" />

        @foreach($posts as $post)
            <item>
                <title><![CDATA[{{ $post->title }}]]></title>
                <link>{{ url('/blog/'.$post->slug) }}</link>
                <guid isPermaLink="true">{{ url('/blog/'.$post->slug) }}</guid>
                @if($post->excerpt)
                    <description><![CDATA[{{ $post->excerpt }}]]></description>
                @endif
                @if($post->published_at)
                    <pubDate>{{ $post->published_at->toRssString() }}</pubDate>
                @endif
            </item>
        @endforeach
    </channel>
</rss>

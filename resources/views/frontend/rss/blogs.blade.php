{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<rss version="2.0">
    <channel>
        <title>Suyagya Blog</title>
        <link>{{ url('/blogs') }}</link>
        <description>Latest spiritual blogs from Suyagya</description>
        <language>en-IN</language>

        @foreach($blogs as $blog)
            <item>
                <title><![CDATA[{{ $blog->title }}]]></title>
                <link>{{ url('/blogs/' . $blog->slug) }}</link>
                <guid>{{ url('/blogs/' . $blog->slug) }}</guid>
                <pubDate>{{ $blog->created_at->toRssString() }}</pubDate>

                <description><![CDATA[
                    {!! \Illuminate\Support\Str::limit(strip_tags($blog->content), 200) !!}
                ]]></description>

                @if($blog->og_image || $blog->main_image)
                    <enclosure
                        url="{{ asset($blog->og_image ?? $blog->main_image) }}"
                        type="image/jpeg" />
                @endif
            </item>
        @endforeach

    </channel>
</rss>

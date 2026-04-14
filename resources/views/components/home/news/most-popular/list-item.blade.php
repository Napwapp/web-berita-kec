{{-- Usage: <x-news.popular.list-item :news="$news" :rank="$loop->iteration" /> --}}
@props(['news', 'rank' => null])

@php
    $content = $news->currentVersion;
    $publishedAt = $content?->published_at ?? $news->created_at;
@endphp

<a href="{{ route('news.show', $news) }}" class="popular-list-item flex items-start gap-3 py-3 px-1 group
        border-b border-gray-100 last:border-b-0
    hover:bg-gray-50 transition-colors duration-150">

    <!-- Top 3 -->
    @if($rank)
        <span class="popular-list-item__rank shrink-0 w-6 pt-0.5 text-xl font-bold
                {{ $rank <= 3 ? 'color-primary' : 'text-gray-300' }}">
            {{ $rank }}
        </span>
    @endif

    <!-- Thumbnail -->
    <div class="popular-list-item__thumbnail shrink-0 w-16 h-16 rounded overflow-hidden bg-gray-100">
        @if($content?->thumbnail)
            <img src="{{ $content->thumbnail }}" alt="{{ $content->title }}"
                class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" />
        @else
            <div class="w-full h-full bg-gradient-to-br from-gray-200 to-gray-300"></div>
        @endif
    </div>

    <!-- Meta news-->
    <x-home.news.meta 
        :title="$content?->title ?? 'Untitled News'" 
        :publishedAt="$publishedAt" 
        titleTag="h4"
        titleClass="group-hover:text-green-600 text-sm font-semibold transition-colors duration-150"
        timestampClass="text-[0.65rem] mt-1" 
        wrapperClass="flex flex-col-reverse gap-0 min-w-0" 
    />
</a>
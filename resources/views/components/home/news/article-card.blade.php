@props(['news', 'showExcerpt' => false, 'showCategory' => true])

@php
    $content = $news->currentVersion;
    $thumbnail = $content?->thumbnail;
    $title = $content?->title ?? 'Untitled';
    $publishedAt = $news->created_at;
@endphp

<article class="bg-white border border-gray-100 overflow-hidden hover:shadow-sm group">
    <a href="{{ route('news.show', $news->slug) }}">
        <div class="aspect-video w-full overflow-hidden bg-gray-100">
            <img src="{{ $thumbnail }}" alt="{{ $title }}" class="w-full h-full object-cover" loading="lazy">
        </div>
    </a>
    <div class="p-3">
        @if($showCategory && $news->categories->isNotEmpty())
            <span class="inline-block text-xs font-medium px-2 py-0.5 rounded bg-green-50 text-green-700 mb-1.5">
                {{ $news->categories->first()->name }}
            </span>
        @endif

        <a href="{{ route('news.show', $news->slug) }}" class="block">
            <x-home.news.meta
                :title="$title"
                :publishedAt="$publishedAt"
                :excerpt="$showExcerpt ? $content?->excerpt : null"
                titleTag="h3"
                titleClass="text-sm group-hover:text-green-600"
                timestampClass="text-sm text-gray-400"
                wrapperClass="flex flex-col gap-1"
            />
        </a>
    </div>
</article>
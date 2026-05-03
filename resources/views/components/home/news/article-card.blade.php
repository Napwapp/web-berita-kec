@props([
    'news', 
    'showExcerpt' => false, 
    'showCategory' => true, 
    'highlight' => null
])

@php
    $content     = $news->currentVersion;
    $thumbnail   = $content?->thumbnail;
    $title       = $content?->title ?? 'Untitled';
    $publishedAt = $news->created_at;

    // Highlight keyword di title dan excerpt
    $highlightKeyword = function (?string $text, ?string $keyword): string {
        if (!$text || !$keyword) return e($text ?? '');
        $escaped = e($text);
        $pattern = '/(' . preg_quote(e($keyword), '/') . ')/iu';
        return preg_replace($pattern, '<mark class="bg-yellow-100 text-yellow-800 rounded px-0.5">$1</mark>', $escaped);
    };

    $highlightedTitle   = $highlightKeyword($title, $highlight);
    $highlightedExcerpt = $highlightKeyword($content?->excerpt, $highlight);

@endphp

<article class="bg-white border border-gray-100 overflow-hidden hover:shadow-sm group">
    <a href="{{ route('news.show', $news->slug) }}">
        <div class="aspect-video w-full overflow-hidden bg-gray-100">
            <img src="{{ $thumbnail }}" alt="{{ $title }}" class="w-full h-full object-cover" loading="lazy">
        </div>
    </a>
    <div class="p-3">
        @if($showCategory && $news->categories->isNotEmpty())
            @foreach($news->categories->take(2) as $category)
                <span class="inline-block text-xs font-medium px-2 py-0.5 rounded bg-green-50 text-green-700 mb-1.5 mr-1">
                    {{ $category->name }}
                </span>
            @endforeach
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

            {{-- Views & Likes: hanya tampil jika showExcerpt aktif --}}
            @if($showExcerpt)
                <div class="flex items-center gap-3 mt-1">
                    <span class="flex items-center gap-1.5 text-xs text-gray-400">
                        <i class="fas fa-eye"></i>
                        {{ number_format($news->views ?? 0) }}
                    </span>
                    <span class="flex items-center gap-1.5 text-xs text-gray-400">
                        <i class="fas fa-heart"></i>
                        {{ number_format($news->likes ?? 0) }}
                    </span>
                </div>
            @endif

            {{-- Slot title dengan highlight --}}
            <x-slot name="titleSlot">{!! $highlightedTitle !!}</x-slot>
            <x-slot name="excerptSlot">{!! $highlightedExcerpt !!}</x-slot>
        </a>
    </div>
</article>
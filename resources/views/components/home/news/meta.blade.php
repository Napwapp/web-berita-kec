@props([
    'title' => 'Untitled News',
    'publishedAt' => '-',
    'titleTag' => 'h2',
    'titleClass' => '',
    'timestampClass' => '',
    'excerptClass' => '',        // ← tambahan
    'excerpt' => null,           // ← tambahan, default null = tidak tampil
    'wrapperClass' => 'flex flex-col gap-2',
])

@php
    $defaultTitleClasses = ['text-gray-900', 'font-bold', 'text-base', 'leading-snug', 'line-clamp-2'];
    $customTitleClasses = $titleClass ? explode(' ', $titleClass) : [];
    $titleClass = implode(' ', array_unique(array_merge($defaultTitleClasses, $customTitleClasses)));

    $defaultTimestampClasses = ['text-xs', 'font-medium', 'text-gray-400', 'tracking-wide'];
    $customTimestampClasses = $timestampClass ? explode(' ', $timestampClass) : [];
    $timestampClass = implode(' ', array_unique(array_merge($defaultTimestampClasses, $customTimestampClasses)));

    $defaultExcerptClasses = ['text-sm', 'text-gray-500', 'leading-relaxed', 'line-clamp-3'];
    $customExcerptClasses = $excerptClass ? explode(' ', $excerptClass) : [];
    $excerptClass = implode(' ', array_unique(array_merge($defaultExcerptClasses, $customExcerptClasses)));
@endphp

<div class="{{ $wrapperClass }}">
    <span class="news-meta__timestamp {{ $timestampClass }}">
        {{ $publishedAt?->diffForHumans() ?? '-' }}
    </span>

    <{{ $titleTag }} class="news-meta__title {{ $titleClass }} m-0 line-clamp-2">
        {{ $title }}
    </{{ $titleTag }}>

    {{-- Hanya render jika excerpt diberikan --}}
    @if($excerpt)
        <p class="news-meta__excerpt {{ $excerptClass }}">
            {{ $excerpt }}
        </p>
    @endif
</div>
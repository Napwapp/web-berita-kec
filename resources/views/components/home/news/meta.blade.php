@props([
    'title' => 'Untitled News',
    'publishedAt' => '-',
    'titleTag' => 'h2', // Default tag untuk title
    'titleClass' => '',  // Class custom
    'timestampClass' => '', // Class custom
    'wrapperClass' => 'flex flex-col gap-1', // Default wrapper class
])

<!-- Merge Default and Custom Classes -->
@php
    $defaultTitleClasses = ['text-gray-900', 'font-bold', 'text-base', 'leading-snug', 'line-clamp-2'];
    $customTitleClasses = $titleClass ? explode(' ', $titleClass) : [];
    $titleClass = implode(' ', array_unique(array_merge($defaultTitleClasses, $customTitleClasses)));

    $defaultTimestampClasses = ['text-xs', 'font-medium', 'text-gray-400', 'tracking-wide'];
    $customTimestampClasses = $timestampClass ? explode(' ', $timestampClass) : [];
    $timestampClass = implode(' ', array_unique(array_merge($defaultTimestampClasses, $customTimestampClasses)));
@endphp

<div class="{{ $wrapperClass }}">
    <span class="news-meta__timestamp {{ $timestampClass }}">
        {{ $publishedAt?->diffForHumans() ?? '-' }}
    </span>

    <{{ $titleTag }} class="news-meta__title {{ $titleClass }} m-0">
        {{ $title }}
    </{{ $titleTag }}>
</div>
@props(['active', 'tag' => 'a'])
@php
    $default = 'shrink-0 px-4 py-2 text-center block whitespace-nowrap font-semibold inline-flex items-center justify-center hover:text-green-600 hover:border-b-2 hover:border-green-600';
    $activeClass = ($active ?? false) ? 'active border-b-2 border-green-600 text-green-600' : '';
    $extra = $attributes->get('class') ? $attributes->get('class') : '';
    $classes = trim("{$extra} {$activeClass} {$default}");
@endphp

@if($tag === 'button')
    <button {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@else
    <a {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@endif
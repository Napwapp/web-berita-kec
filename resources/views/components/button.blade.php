@props([
    'href' => null,
    'variant' => 'primary', // 'primary' atau 'secondary'
])

@php
    $base = 'px-5 py-2 rounded-md font-medium disabled:opacity-50 disabled:cursor-not-allowed inline-block box-border';
    
    $variants = [
        'primary'   => 'bg-primary text-white hover:opacity-90',
        'secondary' => 'bg-gray-50 text-gray-800 border border-gray-300 hover:bg-gray-100 hover:text-gray-900',
    ];

    $class = $base . ' ' . ($variants[$variant] ?? $variants['primary']);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $class]) }}>
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['type' => 'submit', 'class' => $class]) }}>
        {{ $slot }}
    </button>
@endif
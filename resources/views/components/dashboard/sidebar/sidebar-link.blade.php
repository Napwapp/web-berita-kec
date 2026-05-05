@props([
    'href',
    'active' => false,
    'icon',
    'count' => null,
    'variant' => 'default', // default | green | yellow | blue | red
    'title' => null,
])

@php
    $base = 'group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150';

    $activeClass = 'bg-green-600 text-white shadow-sm shadow-green-200';
    $inactiveClass = 'text-gray-600 hover:bg-green-50 hover:text-green-700';

    $iconBase = 'w-4 text-center text-sm';

    $iconVariants = [
        'default' => 'text-gray-400 group-hover:text-green-600',
        'green' => 'text-green-500 group-hover:text-green-600',
        'yellow' => 'text-yellow-500 group-hover:text-yellow-600',
        'blue' => 'text-blue-500 group-hover:text-blue-600',
        'red' => 'text-red-500 group-hover:text-red-600',
    ];

    $countVariants = [
        'default' => 'bg-gray-100 text-gray-500',
        'green' => 'bg-green-100 text-green-700',
        'yellow' => 'bg-yellow-100 text-yellow-700',
        'blue' => 'bg-blue-100 text-blue-700',
        'red' => 'bg-red-100 text-red-700',
    ];
@endphp

<a href="{{ $href }}"
    class="{{ $base }} {{ $active ? $activeClass : $inactiveClass }}"
    @if($title) title="{{ $title }}" @endif
>
    <i class="{{ $icon }} {{ $iconBase }} {{ $active ? 'text-white' : $iconVariants[$variant] }}"></i>
    <span class="flex-1">
    {{ $slot }}
</span>

    @if(!is_null($count) && $count > 0)
        <span class="text-xs font-semibold px-2 py-0.5 rounded-full
            {{ $active ? 'bg-white/20 text-white' : $countVariants[$variant] }}">
            {{ $count > 9 ? '9+' : $count }}
        </span>
    @endif
</a>
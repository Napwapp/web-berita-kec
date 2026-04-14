@props([
    'items'     => [],
    'separator' => null,
    'class'     => '',
]) 

@php
    $defaultSeparator = '<svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>';
    $sep = $separator ?? $defaultSeparator; 
    $homeIcon = '<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9.5L12 3l9 6.5V20a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V9.5z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>';
@endphp

@if(count($items) > 0)
<nav
    aria-label="Breadcrumb"
    class="w-full mb-4 {{ $class }}"
    itemscope
    itemtype="https://schema.org/BreadcrumbList"
>
    <ol class="flex flex-wrap items-center gap-x-1 gap-y-1 text-base font-medium"> 
        @foreach($items as $index => $item)
            @php
                $isFirst  = $index === 0;
                $isLast   = $index === count($items) - 1;
                $isActive = empty($item['url']);
                $label    = $item['label'] ?? '';
                $url      = $item['url'] ?? '#';
                $icon     = $item['icon'] ?? null;
            @endphp

            <li
                class="flex items-center gap-x-1"
                itemprop="itemListElement"
                itemscope
                itemtype="https://schema.org/ListItem"
            >
                {{-- Separator (kecuali item pertama) --}}
                @unless($isFirst)
                    <span class="text-gray-400 flex items-center select-none" aria-hidden="true">
                        {!! $sep !!}
                    </span>
                @endunless

                {{-- Item Aktif (halaman saat ini) --}}
                @if($isActive)
                    <span
                        class="active flex items-center gap-1.5 text-green-600 font-semibold max-w-[200px] sm:max-w-xs truncate cursor-default"
                        aria-current="page"
                        itemprop="name"
                        title="{{ $label }}"
                    >
                        @if($icon)
                            <span class="flex-shrink-0">{!! $icon !!}</span>
                        @endif
                        {{ $label }}
                    </span>

                {{-- Item Link --}}
                @else
                    <a
                        href="{{ $url }}"
                        class="flex items-center gap-1.5 text-gray-500 hover:text-green-600 transition-colors duration-150 max-w-[160px] sm:max-w-xs truncate hover:underline underline-offset-2 focus:outline-none focus:ring-2 focus:ring-green-400 rounded"
                        itemprop="item"
                        title="{{ $label }}"
                    >
                        {{-- Ikon home otomatis di item pertama --}}
                        @if($isFirst && !$icon)
                            {!! $homeIcon !!}
                        @endif 
                        @if($icon)
                            <span class="flex-shrink-0">{!! $icon !!}</span>
                        @endif

                        <span itemprop="name">{{ $label }}</span>
                    </a>
                @endif 
                <meta itemprop="position" content="{{ $index + 1 }}" />
            </li>
        @endforeach 
    </ol>
</nav>
@endif

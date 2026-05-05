@props([
    'tabs',        // array of ['key', 'label', 'count'] — dikirim dari controller
    'status',      // string|null — status yang sedang aktif
    'perPageRaw',  // int|string — nilai asli per_page (bisa 9,18,27,50,'all')
])

<div class="flex items-center gap-1 overflow-x-auto pb-1 -mx-1 px-1 scrollbar-hide">
    @foreach($tabs as $tab)
        @php
            $isActive = $tab['key'] === 'all'
                ? is_null($status)
                : $status === $tab['key'];

            $routeParams = $perPageRaw !== 9 ? ['per_page' => $perPageRaw] : [];

            $tabRoute = $tab['key'] === 'all'
                ? route('user.news', $routeParams)
                : route('user.news', array_merge(['status' => $tab['key']], $routeParams));
        @endphp

        <a href="{{ $tabRoute }}"
            @class([
                'shrink-0 flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-sm font-medium transition-all duration-150',
                'bg-green-600 text-white shadow-sm shadow-green-200' => $isActive,
                'text-gray-500 hover:bg-gray-100 hover:text-gray-700' => !$isActive,
            ])>
            {{ $tab['label'] }}
            @if($tab['count'] > 0)
                <span @class([
                    'text-xs font-bold px-1.5 py-0.5 rounded-full leading-none',
                    'bg-white/20 text-white'    => $isActive,
                    'bg-gray-200 text-gray-600' => !$isActive,
                ])>{{ $tab['count'] }}</span>
            @endif
        </a>
    @endforeach
</div>
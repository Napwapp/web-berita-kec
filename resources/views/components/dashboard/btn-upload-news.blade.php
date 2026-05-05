@php
    $href = Auth::user()->role === 'admin'
        ? '/admin/news/create'
        : route('news.create');
@endphp

<a href="{{ $href }}"
    class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-600 text-white text-sm font-semibold rounded-xl hover:bg-green-700 active:scale-95 transition-all shadow-sm shadow-green-200">
    <i class="fa-solid fa-plus text-xs"></i>
    {{ $slot }}
</a>
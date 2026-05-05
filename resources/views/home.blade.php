@php
    $currentType = request('type');
@endphp

<x-app title="Beranda">
    <div class="max-w-7xl mx-auto mb-4 px-2">
        <div class="inline-flex rounded-xl bg-gray-100 p-1">
            {{-- Semua --}}
            <a href="{{ url()->current() }}" class="px-4 py-2 text-sm font-medium rounded-lg transition
            {{ !$currentType
                ? 'bg-green-600 text-white shadow-sm'
                : 'text-gray-600 hover:text-green-600' }}">
                Semua
            </a>

            {{-- Masyarakat --}}
            <a href="{{ url()->current() }}?type=masyarakat" class="px-4 py-2 text-sm font-medium rounded-lg transition
            {{ $currentType === 'masyarakat'
                ? 'bg-green-600 text-white shadow-sm'
                : 'text-gray-600 hover:text-green-600' }}">
                Masyarakat
            </a>

            {{-- Pemerintahan --}}
            <a href="{{ url()->current() }}?type=pemerintahan" class="px-4 py-2 text-sm font-medium rounded-lg transition
            {{ $currentType === 'pemerintahan'
                ? 'bg-green-600 text-white shadow-sm'
                : 'text-gray-600 hover:text-green-600' }}">
                Pemerintahan
            </a>
        </div>
    </div>

    <!-- Pin dan berita populer -->
    <div class="flex flex-col lg:flex-row items-start gap-4 max-w-7xl mx-auto mb-2">
        <div class="w-full lg:w-3/5 overflow-hidden">
            <x-home.news.pinned-news :pinnedNews="$pinnedNews" :latestNews="$latestNews" />
        </div>

        <div class="w-full lg:w-2/5 sticky top-4">
            <x-home.news.most-popular :popularNews="$popularNews" />
        </div>
    </div>

    <!-- Berita terbaru -->
    <x-home.news.latest-news :latestNews="$moreLatestNews" />

    <!-- Berita pada Kategori Pilihan -->
    <x-home.news.featured-categories :featuredCategories="$featuredCategories" />
</x-app>
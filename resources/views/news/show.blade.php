<!-- Halaman Detail berita -->
@php([
    $content = $news->currentVersion,
    $publishedAt = $content?->published_at ?? $news->created_at
])

@section('meta')
    <meta property="og:type" content="article" />
    <meta property="og:url" content="{{ route('news.show', $news->slug) }}" />
    <meta property="og:title" content="{{ $content->title }}" />
    <meta property="og:description" content="{{ $content->excerpt }}" />
    <meta property="og:image" content="{{ $content->thumbnail }}" />
    <meta property="og:image:width" content="1200" />
    <meta property="og:image:height" content="630" />
    <meta property="og:locale" content="id_ID" />

    {{-- Twitter/X Card --}}
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="{{ $content->title }}" />
    <meta name="twitter:description" content="{{ $content->excerpt }}" />
    <meta name="twitter:image" content="{{ $content->thumbnail }}" />
@endsection

<x-app title="{{ $content->title }} - Berita">
    <!-- Nanti buat agar mengambil first category dan title dari berita yang dibuka -->
    <x-breadcumb :items="[
        ['label' => 'Beranda', 'url' => '/'],
        ['label' => 'Berita', 'url' => route('news.index')],
        ['label' => $content->title]
    ]">
    </x-breadcumb>

    <div class="flex flex-col lg:flex-row gap-6">
        <!-- Left Column: Berita -->
        <div class="lg:w-2/3">
            <article class="bg-white shadow-sm overflow-hidden">
                <!-- Meta (thumbnail & title) -->
                <x-home.news.detail-berita.meta :news="$news" />

                <!-- Content Berita -->
                <x-home.news.detail-berita.content :news="$news" />
            </article>

        </div>

        <!-- Right Column: Sidebar -->
        <x-home.news.detail-berita.sidebar 
            :trending-news="$trendingNews" 
            :related-news="$relatedNews"
            :most-used-categories="$mostUsedCategories" 
        />
    </div>

    <!-- More related news -->
    <x-home.news.grid-relatedNews.related-news :allRelatedNews="$allRelatedNews" />
</x-app>
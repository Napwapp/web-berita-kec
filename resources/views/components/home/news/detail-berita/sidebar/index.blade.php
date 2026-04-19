<aside class="lg:w-1/3 flex flex-col gap-6">
    {{-- Trending News --}}
    <x-home.news.detail-berita.sidebar.trending-news :trending-news="$trendingNews" />

    {{-- Most Used Categories --}}
    <x-home.news.detail-berita.sidebar.most-used-categories :most-used-categories="$mostUsedCategories" />
    
    {{-- Similar News --}}
    <x-home.news.detail-berita.sidebar.similar-news :related-news="$relatedNews" />

</aside>
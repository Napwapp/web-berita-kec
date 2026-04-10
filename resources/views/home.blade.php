<x-app title="Beranda">
    <!-- Pin dan berita populer -->
    <div class="flex flex-col lg:flex-row items-start gap-4 max-w-7xl mx-auto mb-2">
        <div class="w-full lg:w-3/5 overflow-hidden">
            <x-home.news.pinned-news :pinnedNews="$pinnedNews" :latestNews="$latestNews" />
        </div>

        <div class="w-full lg:w-2/5 sticky top-0">
            <x-home.news.most-popular :popularNews="$popularNews" />
        </div>
    </div>

    <!-- Berita terbaru -->
    <section class="latest-news mt-2" id="latest-news">
        <x-home.news.latest-news :latestNews="$moreLatestNews" />
    </section>
</x-app>
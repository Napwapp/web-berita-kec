<x-app title="Beranda">
    <div class="flex flex-col lg:flex-row items-start gap-4 max-w-7xl mx-auto">
        <div class="w-full lg:w-3/5 overflow-hidden">
            <x-home.news.pinned-news :pinnedNews="$pinnedNews" :latestNews="$latestNews" />
        </div>

        <div class="w-full lg:w-2/5 sticky top-0">
            <x-home.news.most-popular :popularNews="$popularNews" />
        </div>
    </div>
</x-app>
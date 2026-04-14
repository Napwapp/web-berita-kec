@props(['popularNews' => collect()])

<section id="popular-news" class="popular-news flex flex-col gap-1 max-h-[480px]">
    <h3 class="border-l-4 border-green-600 px-3 text-base font-semibold text-gray-900 mb-1">
        Terpopuler Minggu Ini
    </h3>

    @if($popularNews->isNotEmpty())
        <div class="popular__list overflow-y-auto">
            @foreach($popularNews as $news)
                <x-home.news.most-popular.list-item :news="$news" :rank="$loop->iteration" />
            @endforeach
        </div>
    @else
        <div class="flex flex-col items-center justify-center py-8 text-center">
            <i class="fas fa-chart-simple text-2xl text-gray-300 mb-2"></i>
            <p class="text-sm font-medium text-gray-500">Belum ada berita populer</p>
            <p class="text-xs text-gray-400 mt-0.5">Minggu ini belum ada data yang tersedia</p>
        </div>
    @endif
</section>
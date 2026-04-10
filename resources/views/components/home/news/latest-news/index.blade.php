@if ($latestNews->isNotEmpty())

    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-gray-800">Berita Terbaru</h2>
        <a href="{{ route('news.index') }}" class="text-sm text-green-600 hover:underline">
            Lihat Semuanya
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach ($latestNews as $news)
            <x-home.news.article-card :news="$news" />
        @endforeach
    </div>

    <x-divider />
@endif
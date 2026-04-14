@props(['news'])

@php([
    $content = $news->currentVersion,
    $shareUrl = urlencode(route('news.show', $news->slug)),
    $shareTitle = urlencode($content->title),
])

<section id="article-meta" class="article-meta">
    <!-- Thumbnail berita -->
    <div class="aspect-w-16 aspect-h-9 mb-6 rounded-md overflow-hidden">
        <img alt="{{ $content->title }}" class="w-full h-[400px] object-cover bg-gray-200"
            src="{{ $content->thumbnail }}" />
    </div>

    <!-- Title Berita -->
    <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 leading-tight mb-4">
        {{ $content->title }}
    </h1>

    <!-- Author meta -->
    <x-home.news.detail-berita.author :news="$news" />

    <!-- Categories -->
    <div class="flex flex-wrap gap-2 my-4">
        @foreach($news->categories as $category)
            <a href="{{ route('kategori.show', $category->slug) }}"
                class="px-3 py-1 text-sm border rounded bg-green-50 text-green-700 border-green-600 hover:bg-green-100">
                {{ $category->name }}
            </a>
        @endforeach
    </div>

    <!-- Aksi -->
    <x-home.news.detail-berita.actions :shareTitle="$shareTitle" :shareUrl="$shareUrl" :news="$news" />
</section>
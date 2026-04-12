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

    <!-- Share Buttons -->
    <div class="flex flex-row items-center space-x-3 mb-4">
        <div>
            <span class="mr-4 text-gray-700 text-md">Bagikan ke: </span>

            <!-- Share to whatsapp -->
            <a aria-label="Share ke WhatsApp" class="transition-opacity hover:opacity-80 text-xl text-[#25D366]"
                href="https://wa.me/?text={{ $shareTitle }}%20{{ $shareUrl }}" target="_blank"
                rel="noopener noreferrer">
                <i class="fab fa-whatsapp" title="Share ke Whatsapp"></i>
            </a>

            <!-- Share to Facebook -->
            <a aria-label="Share ke Facebook" class="transition-opacity hover:opacity-80 text-xl text-[#1877F2]"
                href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}" target="_blank"
                rel="noopener noreferrer">
                <i class="fab fa-facebook" title="Share ke Facebook"></i>
            </a>

            <!-- Share to X -->
            <a aria-label="Share ke X (Twitter)" class="transition-opacity hover:opacity-80 text-xl text-black"
                href="https://twitter.com/intent/tweet?text={{ $shareTitle }}&url={{ $shareUrl }}" target="_blank"
                rel="noopener noreferrer">
                <i class="fab fa-x-twitter" title="Share ke X (Twitter)"></i>
            </a>

            <!-- Copy Link -->
            <a aria-label="Salin tautan" class="relative transition-opacity hover:opacity-80 text-xl text-black"
                href="#" x-data="{ copied: false }" x-on:click.prevent="
                    navigator.clipboard.writeText('{{ route('news.show', $news->slug) }}');
                    copied = true;
                    setTimeout(() => copied = false, 2000)
                ">
                <i class="fas fa-link text-sm" title="Salin Tautan"></i>

                <span x-show="copied"
                    class="absolute -top-8 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded whitespace-nowrap pointer-events-none">
                    Tautan disalin!
                </span>
            </a>
        </div>
    </div>
</section>
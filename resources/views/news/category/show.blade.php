<x-app :title="$category->name">
    <div class="min-h-screen bg-gray-50">
        <!-- Header -->
        <div class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <x-breadcumb :items="[
                    ['label' => 'Beranda', 'url' => '/'],
                    ['label' => 'Berita',  'url' => route('news.index')],
                    ['label' => $category->name]
                ]" />
                <div class="flex items-start justify-between mt-4">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="w-1 h-6 bg-green-600 rounded-full inline-block"></span>
                            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">{{ $category->name }}</h1>
                        </div>
                        @if($category->description)
                            <p class="text-gray-500 text-sm mt-1 ml-3">{{ $category->description }}</p>
                        @endif
                        <p class="text-xs text-gray-400 mt-1 ml-3">
                            {{ $news->total() + ($featuredNews ? 1 : 0) }} berita dalam kategori ini</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

            <!-- Featured news -->
            @if($featuredNews)
                @php $fv = $featuredNews->currentVersion @endphp
                <section>
                    <div class="flex items-center gap-2 mb-4">
                        <span class="w-1 h-5 bg-green-600 rounded-full inline-block"></span>
                        <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Terpopuler di Kategori Ini</h2>
                    </div>

                    <a href="{{ route('news.show', $featuredNews->slug) }}"
                        class="group flex flex-col lg:flex-row bg-white rounded-lg border border-gray-200 overflow-hidden duration-200">

                        {{-- Thumbnail --}}
                        <div class="lg:w-3/5 aspect-video lg:aspect-auto overflow-hidden bg-gray-100">
                            <img
                                src="{{ $fv?->thumbnail }}"
                                alt="{{ $fv?->title }}"
                                class="w-full h-full object-cover group-hover:scale-[1.02] transition-transform duration-300"
                            />
                        </div>

                        {{-- Content --}}
                        <div class="flex-1 p-6 lg:p-8 flex flex-col justify-between">
                            <div class="space-y-3">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                    <i class="fas fa-fire text-[10px]"></i>
                                    {{ $category->name }}
                                </span>

                                <h2 class="text-xl lg:text-2xl font-bold text-gray-800 leading-snug group-hover:text-green-600 transition-colors line-clamp-3">
                                    {{ $fv?->title }}
                                </h2>

                                @if($fv?->excerpt)
                                    <p class="text-gray-500 text-sm leading-relaxed line-clamp-3">
                                        {{ $fv->excerpt }}
                                    </p>
                                @endif
                            </div>

                            <!-- Meta Information -->
                            <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-100">
                                {{-- KIRI --}}
                                <div class="flex items-center gap-3 flex-1 min-w-0">
                                    @if($featuredNews->author)
                                        <img
                                            src="{{ $featuredNews->author->profile_photo }}"
                                            alt="{{ $featuredNews->author->name }}"
                                            class="w-8 h-8 rounded-full object-cover flex-shrink-0"
                                        />
                                    @endif

                                    {{-- author name + tanggal --}}
                                    <div class="flex flex-col leading-tight min-w-0">
                                        <span class="text-sm text-gray-700 truncate">
                                            {{ $featuredNews->author->name }}
                                        </span>
                                        <span class="text-xs text-gray-400">
                                            {{ $featuredNews->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>

                                {{-- KANAN --}}
                                <div class="flex flex-col items-end gap-2 text-xs text-gray-400 ml-4 flex-shrink-0">
                                    <span class="flex items-center gap-1">
                                        <i class="fas fa-eye"></i>
                                        {{ number_format($featuredNews->views) }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <i class="fas fa-heart"></i>
                                        {{ number_format($featuredNews->likes) }}
                                    </span>
                                </div>

                            </div>
                        </div>
                    </a>
                </section>
            @endif

            <!-- Filter / Navigasi Kategori Lain -->
            <section>
                <div class="flex flex-wrap items-center gap-2">

                    <a href="{{ route('news.index') }}"
                        class="px-4 py-1.5 rounded-full text-sm font-medium border transition-colors duration-200
                            bg-white text-gray-600 border-gray-200 hover:border-green-400 hover:text-green-600">
                        Semua
                    </a>

                    @foreach($categories as $cat)
                        <a href="{{ route('kategori.show', $cat->slug) }}"
                            class="px-4 py-1.5 rounded-full text-sm font-medium border transition-colors duration-200
                                {{ $cat->slug === $category->slug
                                    ? 'bg-green-600 text-white border-green-600'
                                    : 'bg-white text-gray-600 border-gray-200 hover:border-green-400 hover:text-green-600' }}">
                            
                            {{ $cat->name }}
                            
                            <span class="text-xs ml-0.5
                                {{ $cat->slug === $category->slug ? 'text-white opacity-90' : 'opacity-60' }}">
                                ({{ $cat->news_count }})
                            </span>

                        </a>
                    @endforeach

                    {{-- Divider --}}
                    <x-divider />

                    {{-- Sort --}}
                    <a href="{{ route('kategori.show', ['slug' => $category->slug, 'sort' => 'latest']) }}"
                        class="px-4 py-1.5 rounded-full text-sm font-medium border transition-colors duration-200
                            {{ $sort === 'latest' || !$sort ? 'bg-gray-800 text-white border-gray-800' : 'bg-white text-gray-600 border-gray-200 hover:border-gray-400' }}">
                        Terbaru
                    </a>
                    <a href="{{ route('kategori.show', ['slug' => $category->slug, 'sort' => 'popular']) }}"
                        class="px-4 py-1.5 rounded-full text-sm font-medium border transition-colors duration-200
                            {{ $sort === 'popular' ? 'bg-gray-800 text-white border-gray-800' : 'bg-white text-gray-600 border-gray-200 hover:border-gray-400' }}">
                        Terpopuler
                    </a>

                </div>
            </section>

            <!-- List berita -->
            @if($news->isEmpty())
                <div class="flex flex-col items-center justify-center py-20 text-center bg-white rounded-2xl border border-gray-200">
                    <div class="w-14 h-14 rounded-2xl bg-gray-100 flex items-center justify-center mb-3">
                        <i class="fas fa-newspaper text-2xl text-gray-400"></i>
                    </div>
                    <p class="text-gray-700 font-medium mb-1">Belum ada berita lain</p>
                    <p class="text-gray-400 text-sm">Tidak ada berita lain di kategori {{ $category->name }}.</p>
                </div>
            @else
                <section class="space-y-5">
                    <div class="flex items-center gap-2">
                        <span class="w-1 h-5 bg-green-600 rounded-full inline-block"></span>
                        <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">
                            Semua Berita {{ $category->name }}
                        </h2>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                        @foreach($news as $item)
                            <x-home.news.article-card
                                :news="$item"
                                :showExcerpt="true"
                                :showCategory="false"
                            />
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    @if($news->hasPages())
                        <div class="flex justify-center pt-2">
                            {{ $news->links() }}
                        </div>
                    @endif
                </section>
            @endif

        </div>
    </div>
</x-app>
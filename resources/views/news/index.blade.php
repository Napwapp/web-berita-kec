<x-app title="Berita">
    <div class="min-h-screen bg-gray-50">
        <!-- Header -->
        <div class="bg-white border-b border-gray-200">
            <x-breadcumb :items="[
                ['label' => 'Beranda', 'url' => '/'],
                ['label' => 'Berita']
            ]">
            </x-breadcumb>

            <div class="max-w-7xl mx-auto mb-6">  
                <div class="mt-4">
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Berita</h1>
                    <p class="text-gray-500 text-sm">Informasi terkini seputar Kecamatan Binong</p>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-10">
            <!-- Featured / Pinned news -->
            @if($pinnedNews)
                @php $pv = $pinnedNews->currentVersion @endphp
                <section>
                    <div class="flex items-center gap-2 mb-4">
                        <span class="w-1 h-5 bg-green-600 rounded-full inline-block"></span>
                        <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Berita Utama</h2>
                    </div>

                    <a href="{{ route('news.show', $pinnedNews->slug) }}"
                        class="group relative flex flex-col lg:flex-row bg-white rounded-2xl border border-gray-200 overflow-hidden hover:shadow-sm">

                        {{-- Thumbnail --}}
                        <div class="lg:w-3/5 aspect-video lg:aspect-auto overflow-hidden bg-gray-100">
                            <img src="{{ $pv?->thumbnail }}" alt="{{ $pv?->title }}"
                                class="w-full h-full object-cover" />
                        </div>

                        {{-- Content --}}
                        <div class="flex-1 p-6 lg:p-8 flex flex-col justify-between">
                            <div class="space-y-3">
                                <div class="flex items-center gap-2">
                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                        Berita Unggulan
                                    </span>
                                    @if($pinnedNews->categories->isNotEmpty())
                                        <span
                                            class="inline-block px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                            {{ $pinnedNews->categories->first()->name }}
                                        </span>
                                    @endif
                                </div>

                                <h2
                                    class="text-xl lg:text-2xl font-bold text-gray-800 leading-snug group-hover:text-green-600 line-clamp-3">
                                    {{ $pv?->title }}
                                </h2>

                                @if($pv?->excerpt)
                                    <p class="text-gray-500 text-sm leading-relaxed line-clamp-3">
                                        {{ $pv->excerpt }}
                                    </p>
                                @endif
                            </div>

                            <!-- Meta information -->
                            <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-100">
                                {{-- KIRI --}}
                                <div class="flex items-center gap-3 flex-1 min-w-0">
                                    @if($pinnedNews->author)
                                        <img
                                            src="{{ $pinnedNews->author->profile_photo }}"
                                            alt="{{ $pinnedNews->author->name }}"
                                            class="w-8 h-8 rounded-full object-cover flex-shrink-0"
                                        />
                                    @endif

                                    {{-- author name + tanggal --}}
                                    <div class="flex flex-col leading-tight min-w-0">
                                        <span class="text-sm text-gray-700 truncate">
                                            {{ $pinnedNews->author->name }}
                                        </span>
                                        <span class="text-xs text-gray-400">
                                            {{ $pinnedNews->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>

                                {{-- KANAN --}}
                                <div class="flex flex-col items-end gap-2 text-xs text-gray-400 ml-4 flex-shrink-0">
                                    <span class="flex items-center gap-1">
                                        <i class="fas fa-eye"></i>
                                        {{ number_format($pinnedNews->views) }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <i class="fas fa-heart"></i>
                                        {{ number_format($pinnedNews->likes) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                </section>
            @endif

            <!-- Filter Kategori -->
            <section>
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('news.index', array_filter(['sort' => $sort !== 'latest' ? $sort : null])) }}"
                        class="px-4 py-1.5 rounded-full text-sm font-medium border
                    {{ !$category ? 'bg-green-600 text-white border-green-600' : 'bg-white text-gray-600 border-gray-200 hover:border-green-400 hover:text-green-600' }}">
                        Semua
                    </a>

                    @foreach($categories as $cat)
                        <a href="{{ route('kategori.show', $cat->slug) }}" class="px-4 py-1.5 rounded-full text-sm font-medium border
                        bg-white text-gray-600 border-gray-200 hover:border-green-400 hover:text-green-600">
                            {{ $cat->name }}
                            <span class="text-xs opacity-60 ml-0.5">({{ $cat->news_count }})</span>
                        </a>
                    @endforeach

                    {{-- Divider --}}
                    <x-divider />

                    {{-- Sort --}}
                    <a href="{{ route('news.index', array_filter(['sort' => 'latest'])) }}"
                        class="px-4 py-1.5 rounded-full text-sm font-medium border
                        {{ $sort === 'latest' || !$sort ? 'bg-gray-800 text-white border-gray-800' : 'bg-white text-gray-600 border-gray-200 hover:border-gray-400' }}">
                                Terbaru
                            </a>
                            <a href="{{ route('news.index', array_filter(['sort' => 'popular'])) }}"
                                class="px-4 py-1.5 rounded-full text-sm font-medium border
                        {{ $sort === 'popular' ? 'bg-gray-800 text-white border-gray-800' : 'bg-white text-gray-600 border-gray-200 hover:border-gray-400' }}">
                                Terpopuler
                    </a>
                    </div>
            </section>

            <!-- List berita & Sidebar -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- List Berita (kiri) --}}
                <div class="lg:col-span-2 space-y-6">
                    @if($news->isEmpty())
                        <div
                            class="flex flex-col items-center justify-center py-20 text-center bg-white rounded-2xl border border-gray-200">
                            <div class="w-14 h-14 rounded-2xl bg-gray-100 flex items-center justify-center mb-3">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5"
                                    viewBox="0 0 24 24">
                                    <path
                                        d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5M6 7.5h3v3H6v-3z" />
                                </svg>
                            </div>
                            <p class="text-gray-700 font-medium mb-1">Belum ada berita</p>
                            <p class="text-gray-400 text-sm">
                                {{ $category ? 'Tidak ada berita untuk kategori ini.' : 'Belum ada berita yang dipublikasikan.' }}
                            </p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            @foreach($news as $item)
                                <x-home.news.article-card :news="$item" :showExcerpt="true" :showCategory="true" />
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        @if($news->hasPages())
                            <div class="flex justify-center pt-2">
                                {{ $news->links() }}
                            </div>
                        @endif
                    @endif
                </div>

                {{-- Sidebar (kanan) --}}
                <aside class="space-y-6 lg:sticky lg:top-6 h-fit">
                    {{-- Popular This Week --}}
                    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                        <div class="flex items-center gap-2 px-5 py-4 border-b border-gray-100">
                            <span class="w-1 h-4 bg-green-600 rounded-full inline-block"></span>
                            <h3 class="text-sm font-semibold text-gray-700">Terpopuler Minggu Ini</h3>
                        </div>

                        @if($popularNews->isEmpty())
                            <div class="flex flex-col items-center justify-center py-8 text-center">
                                <i class="fas fa-chart-simple text-2xl text-gray-300 mb-2"></i>
                                <p class="text-sm font-medium text-gray-500">Belum ada berita populer</p>
                                <p class="text-xs text-gray-400 mt-0.5">Minggu ini belum ada data yang tersedia</p>
                            </div>
                        @else
                            <div class="divide-y divide-gray-50">
                                @foreach($popularNews as $i => $popular)
                                    @php $pv = $popular->currentVersion @endphp
                                    <a href="{{ route('news.show', $popular->slug) }}"
                                        class="flex items-start gap-3 px-5 py-3.5 hover:bg-gray-50 group">

                                        {{-- Nomor --}}
                                        <span class="shrink-0 w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold
                                                    {{ $i === 0 ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-500' }}">
                                            {{ $i + 1 }}
                                        </span>

                                        <div class="flex-1 min-w-0">
                                            <p
                                                class="text-sm font-medium text-gray-800 leading-snug line-clamp-2 group-hover:text-green-600">
                                                {{ $pv?->title ?? 'Untitled' }}
                                            </p>
                                            <div class="flex items-center gap-2 mt-1">
                                                <span
                                                    class="text-xs text-gray-400">{{ $popular->created_at->diffForHumans() }}</span>
                                                <span class="text-gray-300">·</span>
                                                <span class="text-xs text-gray-400 flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2"
                                                        viewBox="0 0 24 24">
                                                        <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    {{ number_format($popular->views) }}
                                                </span>
                                            </div>
                                        </div>

                                        {{-- Thumbnail kecil --}}
                                        @if($pv?->thumbnail)
                                            <div class="shrink-0 w-14 h-14 rounded-lg overflow-hidden bg-gray-100">
                                                <img src="{{ $pv->thumbnail }}" alt="{{ $pv->title }}"
                                                    class="w-full h-full object-cover" />
                                            </div>
                                        @endif

                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>

                </aside>
            </div>

        </div>
    </div>
</x-app>
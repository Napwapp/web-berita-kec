    {{-- Tambahkan wrapper grid di luar foreach --}}
    @if($featuredCategories->isNotEmpty())
    <section id="featured-categories" class="featured-categories">
        <h2 class="text-xl font-bold text-gray-800 my-6">Berita dengan Kategori Pilihan</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach ($featuredCategories as $category)
                <div class="flex flex-col" x-data="{ expanded: false }">
                    <h3 class="text-base font-medium text-green-600 pb-2 border-b-2 border-green-600 mb-5">
                        {{ $category->name }}
                    </h3>

                    @if($category->news->isNotEmpty())
                        @foreach ($category->news as $index => $news)
                            @php $version = $news->currentVersion; @endphp
                            <article
                                class="flex flex-col gap-2 pb-5 mb-5 border-b border-gray-100 last:border-none last:mb-0 last:pb-0 group">
                                @if ($version->thumbnail)
                                    <a href="{{ route('news.show', $news->slug) }}" class="block">
                                        <img src="{{ $version->thumbnail }}" alt="{{ $version->title }}"
                                            class="w-full aspect-video object-cover rounded-md">
                                    </a>
                                @endif

                                <a href="{{ route('news.show', $news->slug) }}">
                                    <x-home.news.meta :title="$version->title" :publishedAt="$version->published_at" titleTag="h4"
                                        titleClass="text-sm group-hover:text-green-600" />

                                    @if($version->excerpt)
                                        <p class="text-sm text-gray-500 leading-relaxed line-clamp-3">
                                            {{ $version->excerpt }}
                                        </p>
                                    @endif
                                </a>
                            </article>
                        @endforeach

                        @if($category->news->count() >= 2)
                            <a href="{{ route('kategori.show', $category->slug) }}"
                                class="mt-2 self-start px-4 py-1.5 border border-green-600 text-green-600 text-xs font-medium tracking-wide hover:bg-green-100 rounded-sm">
                                Lihat Selengkapnya
                            </a>
                        @endif
                    @else
                        <div class="py-8 text-center">
                            <svg class="mx-auto h-10 w-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                            <p class="mt-2 text-xs text-gray-500">Belum ada berita</p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </section>
    @endif
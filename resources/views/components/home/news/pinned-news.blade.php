<!-- Berita yang di pin (manual) oleh admin -->
@props(['pinnedNews' => null, 'latestNews' => collect()])
@if($pinnedNews)
    <!-- Pinned news -->
    <section class="pinned-news-section w-full flex flex-col">
        @if($pinnedNews)
            @php
                $content = $pinnedNews->currentVersion;
                $publishedAt = $content?->published_at ?? $pinnedNews->created_at;
            @endphp

            <a href="{{ route('news.show', $pinnedNews) }}"
                class="pinned-hero flex flex-col overflow-hidden bg-white group mb-2">

                {{-- Thumbnail --}}
                <div class="pinned-hero__image-wrap relative w-full max-h-80 aspect-video overflow-hidden">
                    @if($content?->thumbnail)
                        <img src="{{ $content->thumbnail }}" alt="{{ $content?->title }}"
                            class="pinned-hero__image w-full h-full object-cover"
                            loading="eager" />
                    @else
                        <div class="pinned-hero__image--placeholder w-full h-full bg-gradient-to-br from-gray-700 to-gray-500">
                        </div>
                    @endif
                </div>

                {{-- Meta & Judul — di bawah thumbnail --}}
                <div class="pinned-hero__body flex flex-col gap-1 px-3 py-3">
                    <x-home.news.meta 
                        :title="$content?->title"
                        :publishedAt="$publishedAt"
                        titleTag="h2"
                        titleClass="text-gray-900 group-hover:text-green-600 font-bold text-xl leading-snug line-clamp-2"
                    />
                </div>
            </a>
        @endif

        <x-divider class="mb-3" />

        <!-- 3 Latest News -->
        @if($latestNews)
            <div class="latest-news-grid grid grid-cols-3 border-gray-200">
                @foreach($latestNews as $news)
                    @php
                        $c = $news->currentVersion;
                        $pub = $c?->published_at ?? $news->created_at;
                    @endphp

                    <a href="{{ route('news.show', $news) }}"
                        class="latest-news-item flex flex-col px-3 py-3 border-r border-gray-200 last:border-r-0 hover:bg-gray-50 transition-colors duration-150">

                        <x-home.news.meta 
                            :title="Str::limit($c?->title, 100)"
                            :publishedAt="$pub"
                            titleTag="p"
                            titleClass="latest-news-item__title text-[0.9rem] font-semibold m-0 line-clamp-3"
                            timestampClass="latest-news-item__timestamp text-[0.65rem]"
                        />
                    </a>
                @endforeach
            </div>
        @endif
    </section>
@endif
{{-- Tambahkan wrapper grid di luar foreach --}}
<h2 class="text-xl font-bold text-gray-800 my-6">Berita dengan Kategori Pilihan</h2>

<div class="grid grid-cols-1 md:grid-cols-3 gap-8">
    @foreach ($featuredCategories as $category)
    <div class="flex flex-col" x-data="{ expanded: false }">

        <h3 class="text-base font-medium text-green-600 pb-2 border-b-2 border-green-600 mb-5">
            {{ $category->name }}
        </h3>

        @foreach ($category->news as $index => $news)
            @php $version = $news->currentVersion; @endphp
            <article
                class="flex flex-col gap-2 pb-5 mb-5 border-b border-gray-100 last:border-none last:mb-0 last:pb-0 group"
                x-show="{{ $index }} < 2 || expanded"
                x-transition
            >
                @if($version->thumbnail)
                <a href="{{ route('news.show', $news->slug) }}" class="block">
                    <img
                        src="{{ $version->thumbnail }}"
                        alt="{{ $version->title }}"
                        class="w-full aspect-video object-cover rounded-md"
                    >
                </a>
                @endif

                <a href="{{ route('news.show', $news->slug) }}">
                    <x-home.news.meta
                        :title="$version->title"
                        :publishedAt="$version->published_at"
                        titleTag="h4"
                        titleClass="text-sm group-hover:text-green-600"
                    />

                    @if($version->excerpt)
                    <p class="text-sm text-gray-500 leading-relaxed line-clamp-3">
                        {{ $version->excerpt }}
                    </p>
                    @endif
                </a>
            </article>
        @endforeach

        @if($category->news->count() > 2)
        <button
            x-show="!expanded"
            x-on:click="expanded = true"
            class="mt-2 self-start px-4 py-1.5 border border-green-600 text-green-600 text-xs font-medium tracking-wide hover:bg-green-100 rounded-sm"
        >
            Muat Lebih Banyak
        </button>
        @endif

    </div>
    @endforeach
</div>
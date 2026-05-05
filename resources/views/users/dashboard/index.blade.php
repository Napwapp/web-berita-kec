@php
    $statusConfig = [
        'published' => ['label' => 'Diterbitkan', 'icon' => 'fa-circle-check', 'badge' => 'bg-green-100 text-green-700', 'dot' => 'bg-green-500'],
        'review' => ['label' => 'Review', 'icon' => 'fa-clock', 'badge' => 'bg-yellow-100 text-yellow-700', 'dot' => 'bg-yellow-500'],
        'need_revision' => ['label' => 'Perlu Revisi', 'icon' => 'fa-pen-to-square', 'badge' => 'bg-blue-100 text-blue-700', 'dot' => 'bg-blue-500'],
        'rejected' => ['label' => 'Ditolak', 'icon' => 'fa-circle-xmark', 'badge' => 'bg-red-100 text-red-700', 'dot' => 'bg-red-500'],
        'draft' => ['label' => 'Draft', 'icon' => 'fa-file-lines', 'badge' => 'bg-gray-100 text-gray-600', 'dot' => 'bg-gray-400'],
    ];
@endphp

    <x-dashboard.layout 
        title="Kelola Beritamu" 
        breadcrumb="Semua berita yang kamu upload ada di sini."
        :sidebarCounts="$counts"
    >

    <div class="p-4 space-y-5">
        {{-- =====================================================
        HEADER ROW
        ===================================================== --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-lg font-bold text-gray-800">Beritaku</h2>
                <p class="text-sm text-gray-500 mt-0.5">
                    {{ $counts['all'] }} berita total
                    @if($status)
                        · menampilkan <span
                            class="font-medium text-green-600">{{ $statusConfig[$status]['label'] ?? $status }}</span>
                    @endif
                </p>
            </div>

            <!-- Tombol Upload beita -->
            <x-dashboard.btn-upload-news> Tulis Berita </x-dashboard.btn-upload-news>
        </div>

        {{-- =====================================================
        STATUS TABS
        ===================================================== --}}
        <x-dashboard.news.filter-tabs :tabs="$tabs" :status="$status" :perPageRaw="$perPageRaw" />

        {{-- =====================================================
        TOOLBAR: info + per-page select
        ===================================================== --}}
        <div class="flex items-center justify-between">
            <p class="text-sm text-gray-500">
                Menampilkan
                <span class="font-semibold text-gray-700">{{ $news->firstItem() ?? 0 }}</span>–<span
                    class="font-semibold text-gray-700">{{ $news->lastItem() ?? 0 }}</span>
                dari <span class="font-semibold text-gray-700">{{ $news->total() }}</span> berita
            </p>

            {{-- Per-page select --}}
            <form method="GET" action="{{ route('user.news') }}" class="flex items-center gap-2">
                @if($status)
                    <input type="hidden" name="status" value="{{ $status }}">
                @endif
                <label for="per_page" class="text-sm text-gray-500 hidden sm:block">Tampilkan</label>

                <select id="per_page" name="per_page" onchange="this.form.submit()"
                    class="text-sm border border-gray-200 rounded-lg pl-2.5 pr-8 py-1.5 bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent cursor-pointer">
                        @foreach([9, 18, 27, 50] as $opt)
                            <option value="{{ $opt }}" {{ $perPageRaw == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                        @endforeach
                        <option value="all" {{ $perPageRaw === 'all' ? 'selected' : '' }}>Semua</option>
                </select>

                <span class="text-sm text-gray-500 hidden sm:block">per halaman</span>
            </form>
        </div>

        {{-- =====================================================
        GRID BERITA
        ===================================================== --}}
        @if($news->isEmpty())
            {{-- Empty State --}}
            <div class="flex flex-col items-center justify-center py-20 text-center">
                <div class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mb-4">
                    <i class="fa-regular fa-newspaper text-gray-300 text-3xl"></i>
                </div>
                <h3 class="text-base font-semibold text-gray-700">Belum ada berita</h3>
                <p class="text-sm text-gray-400 mt-1 max-w-xs">
                    @if($status)
                        Tidak ada berita dengan status
                        <span class="font-medium">{{ $statusConfig[$status]['label'] ?? $status }}</span>.
                    @else
                        Kamu belum mengupload berita apapun. Mulai sekarang!
                    @endif
                </p>
                @if(!$status)
                    <x-dashboard.btn-upload-news> Tulis Berita Pertamamu </x-dashboard.btn-upload-news>
                @endif
            </div>

        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                @foreach($news as $item)
                    @php
                        $cfg = $statusConfig[$item->status] ?? ['label' => $item->status, 'icon' => 'fa-circle', 'badge' => 'bg-gray-100 text-gray-600', 'dot' => 'bg-gray-400'];
                        $version = $item->currentVersion;
                        $thumbnail = $version->thumbnail ;
                    @endphp

                    <article
                        class="group bg-white rounded border border-gray-100 overflow-hidden hover:shadow-md hover:border-green-100 transition-all duration-200 flex flex-col">

                        {{-- Thumbnail --}}
                        <div class="relative h-44 bg-gray-100 overflow-hidden shrink-0">
                            @if($thumbnail)
                                <img src="{{ $thumbnail }}" alt="{{ $version->thumbnail_description ?? $version->title }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <i class="fa-regular fa-image text-gray-300 text-3xl"></i>
                                </div>
                            @endif

                            {{-- Status Badge --}}
                            <span
                                class="absolute top-3 left-3 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $cfg['badge'] }} backdrop-blur-sm bg-white/80">
                                <span class="w-1.5 h-1.5 rounded-full {{ $cfg['dot'] }}"></span>
                                {{ $cfg['label'] }}
                            </span>

                            {{-- Type Badge --}}
                            <span
                                class="absolute top-3 right-3 px-2 py-1 rounded-full text-xs font-medium bg-black/30 text-white backdrop-blur-sm capitalize">
                                {{ $item->type }}
                            </span>
                        </div>

                        {{-- Content --}}
                        <div class="flex flex-col flex-1 p-4">

                            {{-- Kategori --}}
                            @if($item->categories->isNotEmpty())
                                <div class="flex flex-wrap gap-1 mb-2">
                                    @foreach($item->categories->take(2) as $cat)
                                        <span class="text-[11px] font-medium px-2 py-0.5 bg-green-50 text-green-700 rounded-full">
                                            {{ $cat->name }}
                                        </span>
                                    @endforeach
                                    @if($item->categories->count() > 2)
                                        <span class="text-[11px] font-medium px-2 py-0.5 bg-gray-100 text-gray-500 rounded-full">
                                            +{{ $item->categories->count() - 2 }}
                                        </span>
                                    @endif
                                </div>
                            @endif

                            {{-- Judul --}}
                            <h3
                                class="text-sm font-bold text-gray-800 line-clamp-2 leading-snug mb-1 group-hover:text-green-700 transition-colors">
                                {{ $version?->title ?? '(Tanpa Judul)' }}
                            </h3>

                            {{-- Excerpt --}}
                            @if($version?->excerpt)
                                <p class="text-xs text-gray-400 line-clamp-2 leading-relaxed mb-3">
                                    {{ $version->excerpt }}
                                </p>
                            @endif

                            {{-- Meta --}}
                            <div class="mt-auto flex items-center justify-between pt-3 border-t border-gray-50">
                                <div class="flex items-center gap-3 text-xs text-gray-400">
                                    <span class="flex items-center gap-1">
                                        <i class="fa-regular fa-eye text-[10px]"></i>
                                        {{ number_format($item->views) }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <i class="fa-regular fa-heart text-[10px]"></i>
                                        {{ number_format($item->likes) }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <i class="fa-regular fa-clock text-[10px]"></i>
                                        {{ $item->created_at->locale('id')->diffForHumans() }}
                                    </span>
                                </div>

                                {{-- Action --}}
                                <div class="flex items-center gap-1">
                                    @if(in_array($item->status, ['rejected', 'need_revision', 'draft']))
                                        <a href="{{ route('news.edit', $item->slug) }}"
                                            class="p-1.5 rounded-lg text-gray-400 hover:text-green-600 hover:bg-green-50 transition-colors"
                                            title="Edit berita">
                                            <i class="fa-solid fa-pen text-xs"></i>
                                        </a>
                                    @endif

                                    @if($item->status === 'published')
                                        <a href="{{ url('/berita/' . $item->slug) }}" target="_blank"
                                            class="p-1.5 rounded-lg text-gray-400 hover:text-green-600 hover:bg-green-50 transition-colors"
                                            title="Lihat berita">
                                            <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>

                    </article>
                @endforeach
            </div>

            {{-- =====================================================
            PAGINATION
            ===================================================== --}}

            @if($news->hasPages())
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-2">
                    {{-- Info --}}
                    <p class="text-sm text-gray-500 order-2 sm:order-1">
                        Halaman {{ $news->currentPage() }} dari {{ $news->lastPage() }}
                    </p>

                    {{-- Tombol Navigasi --}}
                    <div class="flex items-center gap-1 order-1 sm:order-2">

                        {{-- Prev --}}
                        @if($news->onFirstPage())
                            <span class="px-3 py-2 rounded-xl text-sm text-gray-300 cursor-not-allowed select-none">
                                <i class="fa-solid fa-chevron-left text-xs"></i>
                            </span>
                        @else
                            <a href="{{ $news->previousPageUrl() }}"
                                class="px-3 py-2 rounded-xl text-sm text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-colors">
                                <i class="fa-solid fa-chevron-left text-xs"></i>
                            </a>
                        @endif

                        {{-- Page Numbers --}}
                        @foreach($news->getUrlRange(max(1, $news->currentPage() - 2), min($news->lastPage(), $news->currentPage() + 2)) as $page => $url)
                            @if($page == $news->currentPage())
                                <span class="w-9 h-9 flex items-center justify-center rounded-xl text-sm font-bold bg-green-600 text-white">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}"
                                    class="w-9 h-9 flex items-center justify-center rounded-xl text-sm text-gray-600 hover:bg-gray-100 transition-colors">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach

                        {{-- Next --}}
                        @if($news->hasMorePages())
                            <a href="{{ $news->nextPageUrl() }}"
                                class="px-3 py-2 rounded-xl text-sm text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-colors">
                                <i class="fa-solid fa-chevron-right text-xs"></i>
                            </a>
                        @else
                            <span class="px-3 py-2 rounded-xl text-sm text-gray-300 cursor-not-allowed select-none">
                                <i class="fa-solid fa-chevron-right text-xs"></i>
                            </span>
                        @endif
                    </div>
                </div>
            @endif
        @endif
    </div>
</x-dashboard.layout>
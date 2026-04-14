<x-app title="Hasil Pencarian: {{ $query }}">

    <x-breadcumb :items="[
        ['label' => 'Beranda', 'url' => '/'],
        ['label' => 'Berita', 'url' => route('news.index')],
        ['label' => $query ? 'Cari :' . $query : 'Pencarian']
    ]">
        
    </x-breadcumb>
    <div class="min-h-screen">
        <div class="max-w-5xl mx-auto space-y-8">            
            <!-- Header hasil query -->
            @if($query)
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-lg font-bold text-gray-800">
                            Hasil pencarian:
                            <span class="text-green-600">"{{ $query }}"</span>
                        </h1>
                        <p class="text-sm text-gray-500 mt-0.5">
                            {{ $total > 0 ? "Ditemukan {$total} berita" : 'Tidak ada berita ditemukan' }}
                        </p>
                    </div>
                </div>
            @endif

            <!-- Empty state -->
            @if(!$query)
                <!-- Belum ada query -->
                <div class="flex flex-col items-center justify-center py-24 text-center">
                    <div
                        class="w-16 h-16 rounded-2xl bg-gray-100 border border-gray-200 flex items-center justify-center mb-4">
                        <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5"
                            viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8" />
                            <path d="M21 21l-4.35-4.35" />
                        </svg>
                    </div>
                    <p class="text-gray-700 font-medium mb-1">Ketik kata kunci untuk mulai mencari</p>
                    <p class="text-gray-400 text-sm">Contoh: politik, ekonomi, pendidikan</p>
                </div>

                <!-- Empty state -->
                @elseif($total === 0)
                    <div class="flex flex-col items-center justify-center py-24 text-center">
                        <div
                            class="w-16 h-16 rounded-2xl bg-gray-100 border border-gray-200 flex items-center justify-center mb-4">
                            <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5"
                                viewBox="0 0 24 24">
                                <path d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <p class="text-gray-700 font-medium mb-1">
                            Tidak ada berita untuk <span class="text-green-600">"{{ $query }}"</span>
                        </p>
                        <p class="text-gray-400 text-sm">Coba gunakan kata kunci yang berbeda atau lebih umum.</p>
                    </div>

                @else
                <!-- Grid hasil berita -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach($results as $item)
                        <x-home.news.article-card :news="$item" :showExcerpt="true" :showCategory="true" :highlight="$query" />
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($results->hasPages())
                    <div class="flex justify-center pt-4">
                        {{ $results->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-app>
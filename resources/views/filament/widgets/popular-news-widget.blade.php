<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                <i class="fas fa-fire text-orange-500 mr-2"></i>
                Top 10 Berita Terpopuler Minggu Ini
            </h2>
            <span class="text-xs text-gray-500">
                <i class="fas fa-chart-simple mr-1"></i>
                Score: (Views × 1) + (Likes × 3)
            </span>
        </div>
        
        <div class="space-y-3">
            @forelse($popularNews as $index => $news)
                <div class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                    <div class="flex items-center gap-3 flex-1">
                        <div class="flex-shrink-0 w-8 text-center">
                            @if($index < 3)
                                @if($index == 0)
                                    <i class="fas fa-trophy text-yellow-500 text-2xl"></i>
                                @elseif($index == 1)
                                    <i class="fas fa-medal text-gray-400 text-2xl"></i>
                                @else
                                    <i class="fas fa-medal text-amber-600 text-2xl"></i>
                                @endif
                            @else
                                <span class="text-gray-400 font-bold">{{ $index + 1 }}</span>
                            @endif
                        </div>
                        
                        <div class="flex-1 min-w-0">
                            <a href="{{ route('filament.admin.resources.news.edit', $news) }}" 
                               class="text-sm font-medium text-gray-900 dark:text-gray-100 hover:text-primary-600 truncate block">
                                {{ $news->title }}
                            </a>
                            <div class="flex gap-3 text-xs text-gray-500 mt-1">
                                <span class="flex items-center gap-1">
                                    <i class="fas fa-eye text-blue-500"></i>
                                    {{ number_format($news->views) }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <i class="fas fa-thumbs-up text-green-500"></i>
                                    {{ number_format($news->likes) }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <i class="fas fa-chart-line text-purple-500"></i>
                                    <span class="font-semibold">Score:</span>
                                    {{ number_format($news->views * 1 + $news->likes * 3) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-chart-line text-3xl mb-2"></i>
                    <p>Belum ada data berita populer minggu ini</p>
                </div>
            @endforelse
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
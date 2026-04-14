<x-filament-widgets::widget>
    <x-filament::section>
        <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">
            <i class="fas fa-tags text-indigo-500 mr-2"></i>
            Kategori Terpopuler
        </h2>
        
        <div class="space-y-4">
            @forelse($categories as $category)
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="font-medium text-gray-700 dark:text-gray-300">
                            <i class="fas fa-tag text-gray-400 mr-1"></i>
                            {{ $category->name }}
                        </span>
                        <span class="text-gray-500">
                            <i class="fas fa-file-alt mr-1"></i>
                            {{ $category->news_count }} berita
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                        @php
                            $percentage = ($category->news_count / max($totalNews, 1)) * 100;
                        @endphp
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-2 rounded-full" 
                            style="width: {{ $percentage }}%"></div>
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-folder-open text-3xl mb-2"></i>
                    <p>Belum ada data kategori</p>
                </div>
            @endforelse
        </div>
        
        @if($categories->isNotEmpty())
            <div class="mt-4 pt-3 text-center">
                <span class="text-xs text-gray-500">
                    <i class="fas fa-chart-pie mr-1"></i>
                    Total {{ $totalNews }} berita dari {{ $categories->count() }} kategori
                </span>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
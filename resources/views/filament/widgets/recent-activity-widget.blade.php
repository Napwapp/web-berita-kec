<x-filament-widgets::widget>
    <x-filament::section>
        <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">
            <i class="fas fa-clock text-blue-500 mr-2"></i>
            Aktivitas Terbaru
        </h2>

        <div class="space-y-6">
            <!-- Berita Terbaru -->
            <div>
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 flex items-center gap-2">
                    <i class="fas fa-newspaper text-green-500"></i>
                    Berita Terbaru
                </h3>
                <div class="space-y-2">
                    @forelse($recentNews as $news)
                        <div
                            class="flex items-center justify-between text-sm p-2 rounded hover:bg-gray-50 dark:hover:bg-gray-800">
                            <div class="flex-1 min-w-0">
                                <a href="{{ route('filament.admin.resources.news.edit', $news) }}"
                                    class="text-primary-600 hover:text-primary-700 truncate block">
                                    <i class="fas fa-file-alt text-gray-400 mr-1"></i>
                                    {{ $news->title }}
                                </a>
                                <span class="text-xs text-gray-500">
                                    <i class="far fa-clock mr-1"></i>
                                    {{ $news->created_at->diffForHumans() }}
                                </span>
                            </div>
                            <span class="text-xs text-gray-400 ml-2">
                                <i class="far fa-calendar-alt mr-1"></i>
                                {{ $news->created_at->format('H:i') }}
                            </span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">
                            <i class="fas fa-inbox mr-1"></i>
                            Belum ada berita
                        </p>
                    @endforelse
                </div>
            </div>

            <div class="border-t border-gray-200 dark:border-gray-700"></div>

            <!-- User Terbaru -->
            <div>
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 flex items-center gap-2">
                    <i class="fas fa-users text-purple-500"></i>
                    User Terbaru
                </h3>
                <div class="space-y-2">
                    @forelse($recentUsers as $user)
                        <div
                            class="flex items-center justify-between text-sm p-2 rounded hover:bg-gray-50 dark:hover:bg-gray-800">
                            <div class="flex-1">
                                <p class="font-medium text-gray-900 dark:text-gray-100">
                                    <i class="fas fa-user-circle text-gray-400 mr-1"></i>
                                    {{ $user->name }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    <i class="fas fa-envelope mr-1"></i>
                                    {{ $user->email }}
                                </p>
                            </div>
                            <span class="text-xs text-gray-400">
                                <i class="fas fa-user-plus mr-1"></i>
                                {{ $user->created_at->diffForHumans() }}
                            </span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">
                            <i class="fas fa-user-slash mr-1"></i>
                            Belum ada user terdaftar
                        </p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="mt-4 pt-3 border-t border-gray-200 dark:border-gray-700 text-center">
            <a href="{{ route('filament.admin.resources.news.index') }}"
                class="text-xs text-primary-600 hover:text-primary-700">
                <i class="fas fa-arrow-right mr-1"></i>
                Lihat semua berita
            </a>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
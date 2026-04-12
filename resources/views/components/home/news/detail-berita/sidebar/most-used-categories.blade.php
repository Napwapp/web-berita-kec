@if ($mostUsedCategories->isNotEmpty())
    <!-- Most Used Categories -->
    <div class="p-2">
        <div class="flex items-center gap-2.5 mb-4">
            <div class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-tag text-green-700 text-xs" aria-hidden="true"></i>
            </div>
            <h2 class="text-xs font-medium text-gray-600 uppercase tracking-widest">Kategori dengan Berita Terbanyak
            </h2>
        </div>

        <ul class="flex flex-col gap-0.5">
            @foreach($mostUsedCategories as $category)
                <li>
                    <a href="{{ route('kategori.show', $category->slug) }}"
                        class="flex items-center gap-3 px-2.5 py-2.5 rounded-xl hover:bg-gray-50 transition-colors duration-150 group">
                        <i class="fa-solid fa-circle text-green-400 text-[6px] shrink-0" aria-hidden="true"></i>
                        <span
                            class="text-sm text-gray-800 flex-1 group-hover:text-green-700 transition-colors duration-150">{{ $category->name }}</span>
                        <span
                            class="text-[11px] font-medium text-green-700 bg-green-100 px-2 py-0.5 rounded-full">{{ $category->news_count }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
    <hr>
@endif
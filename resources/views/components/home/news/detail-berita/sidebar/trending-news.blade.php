<!-- Trending News -->
@if ($trendingNews->isNotEmpty())
    <div class="p-2">
        <div class="flex items-center gap-2.5 mb-4">
            <div class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-arrow-trend-up text-green-700 text-xs" aria-hidden="true"></i>
            </div>
            <h2 class="text-xs font-medium text-gray-600 uppercase tracking-widest">Trending</h2>
        </div>
        
        @foreach($trendingNews as $index => $trending)
            @php $trendingContent = $trending->currentVersion; @endphp

            <a href="{{ route('news.show', $trending->slug) }}"
                class="flex items-center gap-3 rounded-sm hover:bg-gray-50 duration-150 group my-2">
                <div class="shrink-0 w-16 h-16 rounded-sm overflow-hidden bg-gray-100 relative">
                    <img src="{{ $trendingContent->thumbnail }}" alt="{{ $trendingContent->title }}"
                        class="w-full h-full object-cover" />
                    <span
                        class="absolute top-1 left-1 w-5 h-5 rounded-full bg-green-100 text-green-600 text-[12px] font-medium flex items-center justify-center">
                        {{ $index + 1 }}
                    </span>
                </div>
                <div class="flex-1 min-w-0">
                    <h3
                        class="text-sm font-medium text-gray-900 group-hover:text-green-700 duration-150 line-clamp-2">
                        {{ $trendingContent->title }}
                    </h3>
                    <p class="text-xs text-gray-500 line-clamp-2 mt-0.5 leading-relaxed">
                        {{ Str::limit($trendingContent->excerpt, 100) }}
                    </p>
                    <span class="inline-flex items-center gap-1 mt-1.5 text-[10px] text-gray-400">
                        <i class="fa-regular fa-clock" aria-hidden="true"></i>
                        {{ ($trendingContent->published_at ?? $trending->created_at)->diffForHumans() }}
                    </span>
                </div>
            </a>
            <hr>
        @endforeach
    </div>
    <hr>
@endif
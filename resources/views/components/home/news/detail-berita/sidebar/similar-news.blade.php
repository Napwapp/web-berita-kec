@if($relatedNews->isNotEmpty())
    <div class="sticky top-6 p-2">
        <div class="flex items-center gap-2.5 mb-4">
            <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-newspaper text-green-700 text-xs" aria-hidden="true"></i>
            </div>
            <h2 class="text-xl font-medium text-gray-600 uppercase tracking-widest border-b-2 border-green-600">Berita
                Serupa</h2>
        </div>

        <div class="flex flex-col gap-2">
            @foreach($relatedNews as $related)
                @php $relatedContent = $related->currentVersion; @endphp

                <a href="{{ route('news.show', $related->slug) }}"
                    class="flex items-center gap-3 rounded-sm hover:bg-gray-50 transition-colors duration-150 group">
                    <div class="shrink-0 w-20 h-20 rounded-sm overflow-hidden bg-gray-100">
                        <img src="{{ $relatedContent->thumbnail }}" alt="{{ $relatedContent->title }}"
                            class="w-full h-full object-cover" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3
                            class="text-sm font-medium text-gray-900 group-hover:text-green-700 transition-colors duration-150 line-clamp-2">
                            {{ $relatedContent->title }}
                        </h3>
                        <p class="text-xs text-gray-500 line-clamp-2 mt-0.5 leading-relaxed">
                            {{ Str::limit($relatedContent->excerpt, 100) }}
                        </p>
                        <span class="inline-flex items-center gap-1 mt-1.5 text-[10px] text-gray-400">
                            <i class="fa-regular fa-clock" aria-hidden="true"></i>
                            {{ ($relatedContent->published_at ?? $related->created_at)->diffForHumans() }}
                        </span>
                    </div>
                </a>
                <hr>
            @endforeach
        </div>
    </div>
@endif

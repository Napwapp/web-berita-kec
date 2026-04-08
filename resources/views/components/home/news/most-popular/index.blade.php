@props(['popularNews' => collect()])

@if($popularNews->isNotEmpty())
    <div class="popular flex flex-col gap-1  max-h-[480px]">
        <h3 class="border-l-4 border-green-600 px-3 text-base font-semibold text-gray-900 mb-1">
            Terpopuler
        </h3>

        <div class="popular__list overflow-y-auto">
            @foreach($popularNews as $news)
                <x-home.news.most-popular.list-item :news="$news" :rank="$loop->iteration" />
            @endforeach
        </div>
    </div>
@endif
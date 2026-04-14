@if ($allRelatedNews->isNotEmpty())
    <section class="mt-16 border-t border-gray-200 pt-10" x-data="{
            perPage: 4,
            steps: [4, 8],
            get visible() { return $el.querySelectorAll('[data-item]') },
            get total() { return {{ $allRelatedNews->count() }} },
            loadMore() {
                const idx = this.steps.indexOf(this.perPage);
                if (idx < this.steps.length - 1) {
                    this.perPage = this.steps[idx + 1];
                } else {
                    this.perPage = this.total;
                }
            },
            get hasMore() { return this.perPage < this.total },
            get isLastStep() { return this.perPage === this.steps[this.steps.length - 1] }
        }">

        <h3 class="text-2xl font-bold mb-6 text-gray-900">Semua Berita Serupa</h3>

        <div class="grid grid-cols-1 sm:grid-cols-3 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($allRelatedNews as $index => $related)
                <div data-item x-show="{{ $index }} < perPage" x-transition>
                    <x-home.news.article-card :news="$related" :showExcerpt="true" :showCategory="false" />
                </div>
            @endforeach
        </div>

        <div class="flex justify-center mt-8" x-show="hasMore">
            <button x-on:click="loadMore"
                class="px-6 py-2 border border-green-600 text-green-600 text-sm font-medium hover:bg-green-100 transition-colors rounded-sm">
                <span x-text="isLastStep ? 'Lihat Selengkapnya' : 'Muat Lebih Banyak'"></span>
            </button>
        </div>
    </section>
@endif

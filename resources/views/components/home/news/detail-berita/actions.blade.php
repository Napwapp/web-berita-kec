<!-- Share Buttons -->
<div class="flex flex-row items-center justify-between mb-4">

    {{-- Share Buttons (kiri) --}}
    <div class="flex items-center gap-3">
        <span class="text-gray-700 text-sm">Bagikan ke: </span>

        <a aria-label="Share ke WhatsApp"
            class="transition-opacity hover:opacity-80 text-xl text-[#25D366]"
            href="https://wa.me/?text={{ $shareTitle }}%20{{ $shareUrl }}"
            target="_blank" rel="noopener noreferrer">
            <i class="fab fa-whatsapp" title="Share ke Whatsapp"></i>
        </a>

        <a aria-label="Share ke Facebook"
            class="transition-opacity hover:opacity-80 text-xl text-[#1877F2]"
            href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}"
            target="_blank" rel="noopener noreferrer">
            <i class="fab fa-facebook" title="Share ke Facebook"></i>
        </a>

        <a aria-label="Share ke X (Twitter)"
            class="transition-opacity hover:opacity-80 text-xl text-black"
            href="https://twitter.com/intent/tweet?text={{ $shareTitle }}&url={{ $shareUrl }}"
            target="_blank" rel="noopener noreferrer">
            <i class="fab fa-x-twitter" title="Share ke X (Twitter)"></i>
        </a>

        <a aria-label="Salin tautan"
            class="relative transition-opacity hover:opacity-80 text-xl text-black" href="#"
            x-data="{ copied: false }"
            x-on:click.prevent="
                navigator.clipboard.writeText('{{ route('news.show', $news->slug) }}');
                copied = true;
                setTimeout(() => copied = false, 2000)
            ">
            <i class="fas fa-link text-sm" title="Salin Tautan"></i>
            <span x-show="copied"
                class="absolute -top-8 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded whitespace-nowrap pointer-events-none">
                Tautan disalin!
            </span>
        </a>
    </div>

    {{-- Like Button (kanan) --}}
    <div
        x-data="{
            liked: false,
            count: {{ $news->likes }},
            loading: false,
            errorMessage: null,

            async toggle() {
                if (this.loading) return
                this.loading      = true
                this.errorMessage = null

                this.liked  = !this.liked
                this.count += this.liked ? 1 : -1

                try {
                    const res = await fetch('{{ route('news.like', $news->slug) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept':       'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })

                    const data = await res.json()

                    if (!res.ok) {
                        // Rollback + tampilkan pesan error dari server
                        this.liked        = !this.liked
                        this.count       += this.liked ? 1 : -1
                        this.errorMessage = data.message || 'Terjadi kesalahan.'
                        setTimeout(() => this.errorMessage = null, 3000)
                        return
                    }

                    this.liked = data.liked
                    this.count = data.likes

                } catch (e) {
                    this.liked  = !this.liked
                    this.count += this.liked ? 1 : -1
                    console.error(e)
                } finally {
                    this.loading = false
                }
            },

            init() {
                this.liked = {{ auth()->check() ? (Cache::has('news_like_' . $news->id . '_user_' . auth()->id()) ? 'true' : 'false') : 'false' }}
            }
        }"
        x-init="init()"
    >
        <!-- Button like -->
        <button
            @click="toggle()"
            :disabled="loading"
            class="flex items-center gap-2 px-3 py-1.5 rounded-lg border transition-all duration-200 select-none"
            :class="liked
                ? 'border-red-200 bg-red-50 text-red-500 hover:bg-red-100'
                : 'border-gray-200 bg-white text-gray-400 hover:bg-gray-50 hover:text-red-400'"
            :aria-label="liked ? 'Batalkan suka' : 'Suka berita ini'"
        >
            {{-- Icon solid (liked) --}}
            <svg
                x-show="liked"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                fill="currentColor"
                class="w-5 h-5"
            >
                <path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.004-.003.001a.752.752 0 01-.704 0l-.003-.001z"/>
            </svg>

            {{-- Icon outline (not liked) --}}
            <svg
                x-show="!liked"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor"
                class="w-5 h-5"
            >
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/>
            </svg>

            <span class="text-sm font-medium tabular-nums" x-text="count"></span>

            <!-- Error message -->
            <p
                x-show="errorMessage"
                x-transition
                x-text="errorMessage"
                class="text-xs text-red-500 mt-1 text-right"
            ></p>
        </button>
    </div>
</div>
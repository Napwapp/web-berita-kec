<x-app :title="$agenda->title">
    <div class="px-2 sm:px-4 lg:px-6 py-6 lg:py-10">
        {{-- Back button --}}
        <a href="{{ route('agenda.index') }}"
            class="inline-flex items-center gap-2 text-green-600 hover:text-green-700 font-semibold mb-8 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali ke Halaman Agenda
        </a>

        {{-- Header --}}
        <div class="mb-8">
            {{-- Badges --}}
            <div class="flex flex-wrap items-center gap-2 mb-4">
                {{-- Category --}}
                @if ($agenda->category)
                    <span class="text-xs font-semibold px-3 py-1 rounded-full"
                        style="background: {{ $agenda->category->color }}18; color: {{ $agenda->category->color }};">
                        {{ $agenda->category->name }}
                    </span>
                @endif

                {{-- Status --}}
                <span class="text-xs font-semibold px-3 py-1 rounded-full flex items-center gap-1
                    @if ($agenda->isOngoing()) bg-green-50 text-green-700
                    @elseif ($agenda->isUpcoming()) bg-blue-50 text-blue-700
                    @else bg-gray-100 text-gray-400 @endif">
                    @if ($agenda->isOngoing())
                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full live"></span>
                        Sedang Berlangsung
                    @elseif ($agenda->isUpcoming())
                        Akan Datang
                    @else
                        Telah Berlalu
                    @endif
                </span>
            </div>

            {{-- Title --}}
            <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-2 leading-tight">
                {{ $agenda->title }}
            </h1>
        </div>

        {{-- Content Grid --}}
        <div class="grid lg:grid-cols-3 gap-8">

            {{-- Main Content --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Date & Time --}}
                <div class="flex items-start gap-4 p-6 bg-green-50 border border-green-100 rounded-2xl">
                    <div class="w-12 h-12 bg-green-600 rounded-xl grid place-items-center flex-shrink-0">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="1.8"
                            viewBox="0 0 24 24">
                            <rect x="3" y="4" width="18" height="18" rx="2" />
                            <line x1="16" y1="2" x2="16" y2="6" />
                            <line x1="8" y1="2" x2="8" y2="6" />
                            <line x1="3" y1="10" x2="21" y2="10" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-bold text-green-600 uppercase tracking-widest mb-2">Tanggal & Waktu</p>
                        <p class="text-lg font-semibold text-gray-900 mb-1">
                            {{ $agenda->start_at->format('d F Y') }}
                            @if ($agenda->start_at->format('Y-m-d') !== $agenda->end_at->format('Y-m-d'))
                                - {{ $agenda->end_at->format('d F Y') }}
                            @endif
                        </p>
                        @if (!$agenda->is_all_day)
                            <p class="text-sm text-gray-600">
                                {{ $agenda->start_at->format('H:i') }} - {{ $agenda->end_at->format('H:i') }} WIB
                            </p>
                        @else
                            <p class="text-sm text-gray-600">Sepanjang hari</p>
                        @endif
                    </div>
                </div>

                {{-- Location / Online --}}
                @if ($agenda->location || $agenda->is_online)
                    <div class="flex items-start gap-4 p-6 bg-gray-50 border border-gray-100 rounded-2xl">
                        <div class="w-12 h-12 bg-gray-200 rounded-xl grid place-items-center flex-shrink-0">
                            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" stroke-width="1.8"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z M12 11.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">
                                @if ($agenda->is_online)
                                    Pelaksanaan
                                @else
                                    Lokasi
                                @endif
                            </p>
                            @if ($agenda->is_online)
                                <p class="text-lg font-semibold text-gray-900 mb-3">Online / Daring</p>
                                @if ($agenda->online_link)
                                    <a href="{{ $agenda->online_link }}" target="_blank"
                                        class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded-xl transition-colors">
                                        Buka Tautan Meeting
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6M15 3h6v6M10 14L21 3" />
                                        </svg>
                                    </a>
                                @endif
                            @elseif ($agenda->location)
                                <p class="text-lg font-semibold text-gray-900">
                                    {{ $agenda->location }}
                                </p>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Description --}}
                @if ($agenda->description)
                    <div>
                        <h2 class="text-lg font-bold text-gray-900 mb-4">Deskripsi</h2>
                        <div class="prose prose-sm max-w-none text-gray-700 leading-relaxed">
                            {!! nl2br(e($agenda->description)) !!}
                        </div>
                    </div>
                @endif
            </div>

            {{-- Sidebar --}}
            <aside class="lg:col-span-1">
                {{-- Info Box --}}
                <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm sticky top-6">
                    <h3 class="text-sm font-bold text-gray-500 uppercase tracking-widest mb-4">Informasi</h3>

                    <div class="space-y-4">
                        {{-- Status --}}
                        <div>
                            <p class="text-xs text-gray-500 font-semibold uppercase tracking-widest mb-1">Status</p>
                            <p class="text-sm font-semibold
                                @if ($agenda->isOngoing()) text-green-600
                                @elseif ($agenda->isUpcoming()) text-blue-600
                                @else text-gray-400 @endif">
                                @if ($agenda->isOngoing())
                                    <span class="flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full live"></span>
                                        Sedang Berlangsung
                                    </span>
                                @elseif ($agenda->isUpcoming())
                                    Akan Datang
                                @else
                                    Telah Berlalu
                                @endif
                            </p>
                        </div>

                        {{-- Published Date --}}
                        <div>
                            <p class="text-xs text-gray-500 font-semibold uppercase tracking-widest mb-1">Dipublikasikan</p>
                            <p class="text-sm text-gray-600">
                                {{ $agenda->published_at?->format('d M Y, H:i') ?? '-' }}
                            </p>
                        </div>

                        {{-- Duration --}}
                        <div>
                            <p class="text-xs text-gray-500 font-semibold uppercase tracking-widest mb-1">Durasi</p>
                            <p class="text-sm text-gray-600">
                                @if ($agenda->start_at->format('Y-m-d') === $agenda->end_at->format('Y-m-d'))
                                    1 Hari
                                @else
                                    {{ $agenda->start_at->diffInDays($agenda->end_at) + 1 }} Hari
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</x-app>

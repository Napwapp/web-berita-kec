<x-app title="Agenda Kecamatan Binong">
    @php
        // Map warna untuk kategori
        $colorMap = [
            'green' => [
                'dot' => 'bg-green-500',
                'active' => 'bg-green-50 text-green-700',
                'badge' => 'bg-green-100 text-green-700',
            ],
            'blue' => [
                'dot' => 'bg-blue-500',
                'active' => 'bg-blue-50 text-blue-700',
                'badge' => 'bg-blue-100 text-blue-700',
            ],
            'red' => [
                'dot' => 'bg-red-500',
                'active' => 'bg-red-50 text-red-700',
                'badge' => 'bg-red-100 text-red-700',
            ],
            'gray' => [
                'dot' => 'bg-gray-400',
                'active' => 'bg-gray-100 text-gray-700',
                'badge' => 'bg-gray-200 text-gray-700',
            ],
        ];

        // Map warna untuk agenda (status)
        $categoryColorMap = [
            'green' => 'bg-green-100 text-green-700',
            'blue' => 'bg-blue-100 text-blue-700',
            'red' => 'bg-red-100 text-red-700',
            'gray' => 'bg-gray-100 text-gray-700',
        ];

        // Tabs
        $tabs = [
            [
            'key' => 'all',
            'label' => 'Semua',
            'full' => 'Semua Agenda',
            'count' => $all->sum(fn($group) => $group->count()),
            ],
            [
                'key' => 'upcoming',
                'label' => 'Mendatang',
                'full' => 'Acara Mendatang',
                'count' => $upcoming->count(),
            ],
            [
                'key' => 'ongoing',
                'label' => 'Berlangsung',
                'full' => 'Sedang Berlangsung',
                'count' => $ongoing->count(),
            ],
            [
                'key' => 'past',
                'label' => 'Selesai',
                'full' => 'Acara Selesai',
                'count' => $past->count(),
            ],
        ];
    @endphp

    <div class="px-2 sm:px-4 lg:px-6 py-6 lg:py-10">
        <div class="mb-10">
            <!-- Eyebrow -->
            <div class="flex items-center gap-2 mb-3">
                <div class="w-7 h-7 bg-green-600 rounded-lg grid place-items-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <rect x="3" y="4" width="18" height="18" rx="2" />
                        <line x1="16" y1="2" x2="16" y2="6" />
                        <line x1="8" y1="2" x2="8" y2="6" />
                        <line x1="3" y1="10" x2="21" y2="10" />
                    </svg>
                </div>
                <span class="text-xs font-bold text-green-700 uppercase tracking-widest">Kecamatan Binong</span>
            </div>

            <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-2 leading-tight">
                Agenda Kecamatan Binong
            </h1>
            <p class="text-gray-500 text-sm max-w-lg leading-relaxed">
                Informasi jadwal dan agenda kegiatan resmi pemerintahan Kecamatan Binong, Kabupaten Subang. Selalu
                diperbarui secara berkala.
            </p>

            <!-- Statistik -->
            <div class="flex flex-wrap gap-3 mt-6">
                <!-- Agenda bulan ini -->
                <div class="flex items-center gap-3 bg-white border border-gray-100 rounded-2xl px-4 py-3 shadow-sm">
                    <div class="w-9 h-9 bg-green-50 rounded-xl grid place-items-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" stroke-width="1.8"
                            viewBox="0 0 24 24">
                            <rect x="3" y="4" width="18" height="18" rx="2" />
                            <line x1="16" y1="2" x2="16" y2="6" />
                            <line x1="8" y1="2" x2="8" y2="6" />
                            <line x1="3" y1="10" x2="21" y2="10" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xl font-bold text-gray-900 leading-none">{{ $agendaBulanIni }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">Agenda bulan ini</p>
                    </div>
                </div>

                <!-- Agenda hari ini -->
                <div class="flex items-center gap-3 bg-white border border-gray-100 rounded-2xl px-4 py-3 shadow-sm">
                    <div class="relative w-9 h-9 bg-green-600 rounded-xl grid place-items-center">
                        @if($agendaHariIni > 0)
                            <span
                                class="absolute -top-1 -right-1 w-2.5 h-2.5 bg-green-400 rounded-full border-2 border-white live"></span>
                        @endif
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="1.8"
                            viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" />
                            <polyline points="12 6 12 12 16 14" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xl font-bold text-gray-900 leading-none">{{ $agendaHariIni }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">Agenda hari ini</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-6 items-start">

            <!-- Sidebar -->
            <aside class="w-full lg:w-60 xl:w-64 flex-shrink-0 space-y-4 lg:sticky lg:top-6">
                <form method="GET" action="{{ route('agenda.index') }}" id="filterForm">
                    <!-- Search -->
                    <div class="bg-white border border-gray-100 rounded-2xl p-4 shadow-sm">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Pencarian</label>
                        <div class="relative flex">
                            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"
                                fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="11" cy="11" r="8" />
                                <path d="m21 21-4.35-4.35" />
                            </svg>
                            <input type="text" name="search" value="{{ $search }}" placeholder="Cari agenda..."
                                class="w-full pl-9 pr-8 py-2 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:bg-white placeholder-gray-400 transition" />
                            @if($search)
                                <a href="{{ route('agenda.index', ['tab' => $activeTab]) }}"
                                    class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Filter Kategori -->
                    <div class="bg-white border border-gray-100 rounded-2xl p-4 shadow-sm">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-3">Kategori</label>

                        <div class="space-y-0.5">
                            <!-- Semua Kategori -->
                            <button type="submit" name="category" value=""
                                @class([
                                    'w-full flex items-center justify-between px-3 py-2 rounded-xl text-sm transition text-left',
                                    'bg-green-50 text-green-700 font-semibold' => !$categoryId,
                                    'text-gray-600 hover:bg-gray-50' => $categoryId,
                                ])>

                                <span>Semua Kategori</span>

                                <span @class([
                                    'text-xs font-bold px-2 py-0.5 rounded-full min-w-[24px] text-center',
                                    'bg-green-100 text-green-700' => !$categoryId,
                                    'bg-gray-100 text-gray-500' => $categoryId,
                                ])>
                                    {{ $upcoming->count() + $ongoing->count() + $past->count() }}
                                </span>
                            </button>

                            <!-- List kategori -->
                            @foreach($categories as $category)
                                @php
                                    $isActive = $categoryId == $category->id;
                                    $color = $category->color ?? '#9ca3af';

                                    // style button saat aktif
                                    $buttonStyle = $isActive
                                        ? "background: {$color}18; color: {$color};"
                                        : '';

                                    // style badge saat aktif
                                    $badgeStyle = $isActive
                                        ? "background: {$color}25; color: {$color};"
                                        : '';

                                    // class fallback (saat tidak aktif)
                                    $buttonClass = !$isActive
                                        ? 'text-gray-600 hover:bg-gray-50'
                                        : 'font-semibold';

                                    $badgeClass = !$isActive
                                        ? 'bg-gray-100 text-gray-500'
                                        : '';
                                @endphp

                                <button type="submit" name="category" value="{{ $category->id }}"
                                    style="{{ $buttonStyle }}"
                                    class="w-full flex items-center justify-between px-3 py-2 rounded-xl text-sm transition text-left {{ $buttonClass }}">

                                    <span class="flex items-center gap-2 min-w-0">
                                        <span class="w-2 h-2 rounded-full flex-shrink-0"
                                            style="background: {{ $color }}"></span>

                                        <span class="truncate">{{ $category->name }}</span>
                                    </span>

                                    <span
                                        style="{{ $badgeStyle }}"
                                        class="text-xs font-bold px-2 py-0.5 rounded-full flex-shrink-0 ml-1 {{ $badgeClass }}">
                                        {{ $category->agendas_count }}
                                    </span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                </form>
            </aside>

            <!-- KONTEN UTAMA -->
            <div class="flex-1 min-w-0">

                <!-- Filter Tab -->
                <div class="bg-white border border-gray-100 rounded-2xl p-1.5 shadow-sm flex mb-5">
                    <form method="GET" action="{{ route('agenda.index') }}" class="flex w-full gap-1.5">
                        @if($search)
                            <input type="hidden" name="search" value="{{ $search }}">
                        @endif

                        @if($categoryId)
                            <input type="hidden" name="category" value="{{ $categoryId }}">
                        @endif

                        @foreach($tabs as $tab)
                            @php
                                $isActive = $activeTab === $tab['key'];
                                $buttonClass = $isActive
                                    ? 'bg-green-600 text-white shadow-sm shadow-green-200/60'
                                    : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50';

                                $badgeClass = $isActive
                                    ? 'bg-white/25 text-white'
                                    : 'bg-gray-100 text-gray-500';

                                $dotClass = $isActive
                                    ? 'bg-green-300 live'
                                    : 'bg-green-400';
                            @endphp

                            <button type="submit" name="tab" value="{{ $tab['key'] }}"
                                class="flex-1 flex items-center justify-center gap-1.5 px-2 py-2.5 rounded-xl text-xs sm:text-sm font-medium transition-all duration-150 whitespace-nowrap {{ $buttonClass }}">

                                {{-- Dot khusus ongoing --}}
                                @if($tab['key'] === 'ongoing' && $tab['count'] > 0)
                                    <span class="w-1.5 h-1.5 rounded-full hidden sm:inline-block {{ $dotClass }}"></span>
                                @endif

                                <span class="hidden sm:inline">{{ $tab['full'] }}</span>
                                <span class="sm:hidden">{{ $tab['label'] }}</span>

                                <span class="text-xs font-bold px-1.5 py-0.5 rounded-full min-w-[22px] text-center {{ $badgeClass }}">
                                    {{ $tab['count'] }}
                                </span>
                            </button>
                        @endforeach
                    </form>
                </div>

                <!-- Empty State -->
                @if($list->isEmpty())
                    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm py-20 text-center">
                        <div
                            class="w-14 h-14 bg-gray-50 border border-gray-100 rounded-2xl grid place-items-center mx-auto mb-4">
                            <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5"
                                viewBox="0 0 24 24">
                                <rect x="3" y="4" width="18" height="18" rx="2" />
                                <line x1="16" y1="2" x2="16" y2="6" />
                                <line x1="8" y1="2" x2="8" y2="6" />
                                <line x1="3" y1="10" x2="21" y2="10" />
                            </svg>
                        </div>
                        <p class="font-semibold text-gray-600 text-sm mb-1">
                            @if($search || $categoryId)
                                Tidak ada agenda yang ditemukan
                            @else
                                @if($activeTab === 'upcoming')
                                    Belum ada agenda mendatang
                                @elseif($activeTab === 'ongoing')
                                    Tidak ada agenda berlangsung
                                @else
                                    Belum ada agenda selesai
                                @endif
                            @endif
                        </p>
                        <p class="text-xs text-gray-400">
                            @if($search || $categoryId)
                                Tidak dapat menemukan Agenda. Coba ubah kata kunci pencarian atau hapus filter.
                            @else
                                @if($activeTab === 'upcoming')
                                    Agenda mendatang akan ditampilkan di sini
                                @elseif($activeTab === 'ongoing')
                                    Agenda yang berlangsung akan ditampilkan di sini
                                @else
                                    Agenda yang telah berakhir akan ditampilkan di sini
                                @endif
                            @endif
                        </p>
                        @if($search || $categoryId)
                            <a href="{{ route('agenda.index') }}"
                                class="inline-block mt-4 text-xs text-green-600 hover:text-green-700 font-semibold transition-colors">
                                Reset filter
                            </a>
                        @endif
                    </div>
                @else
                    <!-- List Agenda -->
                    <div class="space-y-3">
                        @foreach($list as $agenda)
                            @php
                            $isOngoing = $agenda->isOngoing();
                            $isUpcoming = $agenda->isUpcoming();
                            $isPast = $agenda->isPast();

                            // Date color
                                $dateColorClass = ($isOngoing || $isUpcoming)
                                    ? 'text-green-600'
                                    : 'text-gray-300';

                                $monthColorClass = $isPast
                                    ? 'text-gray-300'
                                    : 'text-gray-400';

                                // Status
                                if ($isOngoing) {
                                    $statusLabel = 'Sedang Berlangsung';
                                    $statusClass = 'bg-green-50 text-green-700';
                                    $showDot = true;
                                } elseif ($isUpcoming) {
                                    $statusLabel = 'Akan Datang';
                                    $statusClass = 'bg-blue-50 text-blue-700';
                                    $showDot = false;
                                } else {
                                    $statusLabel = 'Selesai';
                                    $statusClass = 'bg-gray-100 text-gray-400';
                                    $showDot = false;
                                }

                                // Category color (pakai mapping yang kamu buat di atas)
                                $categoryColorKey = $agenda->category->color ?? 'gray';
                                $categoryClass = $categoryColorMap[$categoryColorKey] ?? $categoryColorMap['gray'];
                            @endphp
                            <a href="{{ route('agenda.show', $agenda->slug) }}"
                                class="block bg-white border border-gray-100 rounded-2xl p-5 shadow-sm group
                                    hover:border-green-200 hover:shadow-md hover:shadow-green-50/60 transition-all duration-150">
                                <div class="flex items-start gap-4">
                                    <!-- Date stamp -->
                                    <div class="flex-shrink-0 w-11 text-center">
                                        <p class="text-2xl font-bold leading-none {{ $dateColorClass }}">
                                            {{ $agenda->start_at->format('d') }}
                                        </p>

                                        <p class="text-[10px] font-bold uppercase mt-0.5 {{ $monthColorClass }}">
                                            {{ $agenda->start_at->format('M') }}
                                        </p>
                                    </div>

                                    <!-- Divider -->
                                    <div class="w-px self-stretch bg-gray-100 flex-shrink-0"></div>

                                    <!-- Content -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex-1 min-w-0">
                                            <div class="flex flex-wrap items-center gap-1.5 mb-2">
                                                {{-- Kategori --}}
                                                @if($agenda->category)
                                                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $categoryClass }}">
                                                        {{ $agenda->category->name }}
                                                    </span>
                                                @endif

                                                {{-- Status --}}
                                                <span class="text-xs font-semibold px-2 py-0.5 rounded-full flex items-center gap-1 {{ $statusClass }}">
                                                    @if($showDot)
                                                        <span class="w-1.5 h-1.5 rounded-full bg-current live"></span>
                                                    @endif
                                                    {{ $statusLabel }}
                                                </span>
                                            </div>

                                            {{-- Title --}}
                                            <h3 class="font-semibold text-gray-900 text-sm leading-snug mb-2.5 line-clamp-2 group-hover:text-green-700 transition-colors">
                                                {{ $agenda->title }}
                                            </h3>
                                        </div>

                                        <!-- Meta -->
                                        <div class="flex flex-wrap gap-x-4 gap-y-1.5">
                                            <!-- Waktu -->
                                            <span class="flex items-center gap-1.5 text-xs text-gray-400">
                                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor"
                                                    stroke-width="2" viewBox="0 0 24 24">
                                                    <circle cx="12" cy="12" r="10" />
                                                    <polyline points="12 6 12 12 16 14" />
                                                </svg>
                                                <span>
                                                    @if($agenda->is_all_day)
                                                        Sepanjang hari
                                                    @else
                                                        {{ $agenda->start_at->format('H:i') }} - {{ $agenda->end_at->format('H:i') }} WIB
                                                    @endif
                                                </span>
                                            </span>

                                            <!-- Lokasi -->
                                            @if($agenda->location || $agenda->is_online)
                                                <span class="flex items-center gap-1.5 text-xs text-gray-400">
                                                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor"
                                                        stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z M12 11.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5z" />
                                                    </svg>
                                                    <span class="truncate max-w-[180px]">
                                                        @if($agenda->is_online)
                                                            Online
                                                        @else
                                                            {{ $agenda->location }}
                                                        @endif
                                                    </span>
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Arrow -->
                                    <svg class="w-4 h-4 text-gray-300 group-hover:text-green-500 flex-shrink-0 mt-1 transition-colors"
                                        fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6" />
                                    </svg>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app>           
@php
    $isBeritaActive = request()->routeIs('kategori.show');
@endphp

<nav class="navbar mt-4 py-4">
    <!-- Navbar -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-5">
        <x-navbar.logo />

        <!-- Bagian kanan navbar -->
        <div class="flex flex-col md:flex-row items-center gap-4 w-full md:w-auto">
            <!-- Search Bar -->
            <x-navbar.search-bar />

            <!-- Tombol Masuk dan Daftar -->
            <x-navbar.auth />
        </div>
    </div>

    <div x-data="{ open: false }">
        <div class="news-filter overflow-x-auto">
            <div class="flex items-center w-full">
                <div class="flex items-center gap-2">
                    <x-navbar.nav-link href="/" :active="request()->routeIs('home')">Beranda</x-navbar.nav-link>

                    <!-- Menu berita yang memunculkan sebmenu berita-->
                    <x-navbar.nav-link 
                        tag="button"
                        :active="request()->routeIs('kategori.show')"
                        @click="open = !open"
                        class="gap-2">
                        Berita
                        <i class="fa-solid fa-chevron-down transition" :class="{ 'rotate-180': open }"></i>
                    </x-navbar.nav-link>

                    <x-navbar.nav-link href="{{ route('struktur-organisasi') }}" :active="request()->routeIs('struktur-organisasi')">Struktur Organisasi Kecamatan Binong</x-navbar.nav-link>
                    <x-navbar.nav-link href="{{ route('agenda.index') }}" :active="request()->routeIs(['agenda.index', 'agenda.show'])">Agenda Kecamatan Binong</x-navbar.nav-link>
                    <x-navbar.nav-link href="{{ route('news.create') }}" :active="request()->routeIs('news.create')" class="gap-2">
                        Upload Berita
                        <i class="fa-solid fa-plus text-green-600 font-semibold"></i>
                    </x-navbar.nav-link>
                </div>                       
            </div>

            <!-- Sebmenu berita -->
            <div x-show="open" 
                x-cloak
                @click.outside="open = false"
                class="w-full bg-white border-t shadow">

                <div class="flex flex-col gap-2 p-6 mx-auto">
                    <h2 class="text-lg font-bold text-gray-700">
                        Pilih Kategori Berita
                    </h2>

                    <div class="flex flex-wrap gap-2">
                        @foreach($categories as $category)
                            <a href="{{ route('kategori.show', $category->slug) }}" class="px-3 py-1 text-sm border rounded
                                {{ request()->routeIs('kategori.show') && request()->route('slug') === $category->slug
                                        ? 'bg-green-200 text-green-600 border-green-600 font-semibold'
                                        : 'bg-green-50 text-green-700 border-green-600 hover:bg-green-100' }}">
                                {{ $category->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>
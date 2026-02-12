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

    <div class="news-filter overflow-x-auto">
        <div class="flex w-full items-center">
            <x-navbar.nav-link href="/" :active="request()->routeIs('home')">Beranda</x-navbar.nav-link>
            <x-navbar.nav-link href="#">Terpopuler</x-navbar.nav-link>
            <x-navbar.nav-link href="#">Pemerintahan</x-navbar.nav-link>
            <x-navbar.nav-link href="#">Banjir</x-navbar.nav-link>
            <x-navbar.nav-link href="#">Kecelakaan</x-navbar.nav-link>
            <x-navbar.nav-link href="#">kegiatan</x-navbar.nav-link>
            <x-navbar.nav-link href="#">Mulyasari</x-navbar.nav-link>
            <x-navbar.nav-link href="#">Lainnya</x-navbar.nav-link>
        </div>
    </div>
</nav>
{{-- Sidebar Overlay --}}
<div id="sidebar-overlay" class="fixed inset-0 z-40 bg-black/50 hidden lg:hidden" aria-hidden="true"></div>

{{-- Sidebar --}}
<aside id="sidebar" class="fixed top-0 left-0 z-50 h-full w-72 bg-white border-r border-gray-200 flex flex-col shadow-xl
    transform -translate-x-full transition-transform duration-300 ease-in-out
    lg:translate-x-0 lg:shadow-none">

    <!-- Header sidebar -->
    <x-dashboard.sidebar.header />

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-0.5">
        <section class="mb-4">
            <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-widest px-3 mb-2">Profil</p>

            <x-dashboard.sidebar.sidebar-link
                :href="route('profile.index')"
                :active="request()->routeIs('profile.*')"
                icon="fa-regular fa-user"
            >
                Detail Profil
            </x-dashboard.sidebar.sidebar-link>
        </section>

        {{-- Kelola Berita --}}
        <section class="mb-4">
            <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-widest px-3 mb-2">Kelola Beritamu</p>

            <!-- Navigasi spesifik untuk role admin -->
            @if (Auth::user()->role === 'admin')
                <x-dashboard.sidebar.sidebar-link
                    :href="route('user.news')"
                    :active="request()->routeIs('user.news') && !request()->has('status')"
                    icon="fa-solid fa-layer-group"
                    variant="green"
                    :count="$sidebarCounts['all']"
                >
                    Semua Berita
                </x-dashboard.sidebar.sidebar-link>

                <x-dashboard.sidebar.sidebar-link
                    :href="route('user.news', ['status' => 'published'])"
                    :active="request()->get('status') === 'published'"
                    icon="fa-solid fa-circle-check"
                    variant="green"
                    :count="$sidebarCounts['published']"
                >
                    Telah Diterbitkan
                </x-dashboard.sidebar.sidebar-link>

                <x-dashboard.sidebar.sidebar-link
                    :href="route('user.news', ['status' => 'draft'])"
                    :active="request()->get('status') === 'draft'"
                    icon="fa-solid fa-file-pen"
                    variant="yellow"
                    :count="$sidebarCounts['draft']"
                >
                    Draft
                </x-dashboard.sidebar.sidebar-link>

                <x-dashboard.sidebar.sidebar-link
                    href="/admin/news/create"
                    :active="request()->routeIs('admin.news.create')"
                    icon="fa-solid fa-circle-plus"
                    title="Kamu adalah Admin. Kamu akan langsung diarahkan ke halaman upload berita di panel admin untuk menulis berita baru nya."
                >
                    Upload Berita
                </x-dashboard.sidebar.sidebar-link>
            @else
            <!-- Semua berita -->
                <x-dashboard.sidebar.sidebar-link
                    :href="route('user.news')"
                    :active="request()->routeIs('user.news') && !request()->has('status')"
                    icon="fa-solid fa-layer-group"
                    variant="green"
                    :count="$sidebarCounts['all']"
                >
                    Semua Berita
                </x-dashboard.sidebar.sidebar-link>

                {{-- Telah Diterbitkan --}}
                <x-dashboard.sidebar.sidebar-link
                    :href="route('user.news', ['status' => 'published'])"
                    :active="request()->get('status') === 'published'"
                    icon="fa-solid fa-circle-check"
                    variant="green"
                    :count="$sidebarCounts['published']"
                >
                    Telah Diterbitkan
                </x-dashboard.sidebar.sidebar-link>

                {{-- Menunggu Review --}}
                <x-dashboard.sidebar.sidebar-link
                    :href="route('user.news', ['status' => 'review'])"
                    :active="request()->get('status') === 'review'"
                    icon="fa-solid fa-hourglass-half"
                    variant="yellow"
                    :count="$sidebarCounts['review']"
                >
                    Menunggu Proses Review
                </x-dashboard.sidebar.sidebar-link>            

                {{-- Menunggu Review Revisi --}}
                <x-dashboard.sidebar.sidebar-link
                    :href="route('user.news', ['status' => 'need_revision'])"
                    :active="request()->get('status') === 'need_revision'"
                    icon="fa-solid fa-pen-to-square"
                    variant="blue"
                    :count="$sidebarCounts['need_revision']"
                >
                    Permintaan Revisi
                </x-dashboard.sidebar.sidebar-link>

                {{-- Ditolak --}}
                <x-dashboard.sidebar.sidebar-link
                    :href="route('user.news', ['status' => 'rejected'])"
                    :active="request()->get('status') === 'rejected'"
                    icon="fa-solid fa-xmark"
                    variant="red"
                    :count="$sidebarCounts['rejected']"
                    countStyle="red"
                >
                    Ditolak
                </x-dashboard.sidebar.sidebar-link>

                {{-- Upload Berita --}}
                <x-dashboard.sidebar.sidebar-link
                    :href="route('news.create')"
                    :active="request()->routeIs('news.create')"
                    icon="fa-solid fa-circle-plus"
                >
                    Upload Berita
                </x-dashboard.sidebar.sidebar-link>
            @endif
        </section>

        {{-- Lainnya --}}
        <section class="mb-4">
            <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-widest px-3 mb-2">Lainnya</p>

            <x-dashboard.sidebar.sidebar-link
                :href="route('notifications.index')"
                :active="request()->routeIs('notifications.*')"
                icon="fa-regular fa-bell"
                variant="red"
                :count="Auth::user()->unread_notifications_count"
            >
                Notifikasi
            </x-dashboard.sidebar.sidebar-link>
        </section>

        {{-- Footer Links --}}
        <section class="pt-2 border-t border-gray-100 space-y-0.5">
            <a href="/"
                class="group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-gray-600 hover:bg-green-50 hover:text-green-700 transition-all duration-150">
                <i class="fa-solid fa-arrow-left w-4 text-center text-sm text-gray-400 group-hover:text-green-600"></i>
                <span>Kembali ke Beranda</span>
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="group w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-red-500 hover:bg-red-50 transition-all duration-150">
                    <i class="fa-solid fa-arrow-right-from-bracket w-4 text-center text-sm"></i>
                    <span>Logout</span>
                </button>
            </form>
        </section>
    </nav>
</aside>
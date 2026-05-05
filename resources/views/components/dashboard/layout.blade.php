<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }}</title>

    <!-- Links -->
    <x-link />
</head>

<body class="bg-gray-50 antialiased">
    {{-- Mobile Top Bar --}}
    <x-dashboard.header.mobile-top-bar />

    {{-- SIDEBAR --}}
    <x-dashboard.sidebar :sidebarCounts="$sidebarCounts" />

    {{-- MAIN WRAPPER --}}
    <div class="lg:pl-72 flex flex-col min-h-screen">
        {{-- Desktop Top Bar --}}
        <header class="hidden lg:flex sticky top-0 z-30 h-16 bg-white border-b border-gray-200 items-center justify-between px-6 shrink-0">
            <div>
                <h1 class="text-base font-semibold text-gray-800">{{ $title ?? 'Dashboard' }}</h1>
                @isset($breadcrumb)
                    <p class="text-xs text-gray-400 mt-0.5">{{ $breadcrumb }}</p>
                @endisset
            </div>

            <div class="flex items-center gap-1nt">
                <a href="{{ route('notifications.index') }}"
                    class="relative p-2 rounded-xl text-gray-400 hover:bg-gray-100 hover:text-green-600 transition-colors">
                    <i class="fa-regular fa-bell text-lg"></i>
                    @if(Auth::user()->unread_notifications_count > 0)
                        <span
                            class="absolute top-1.5 right-1.5 w-4 h-4 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center leading-none">
                            {{ Auth::user()->unread_notifications_count > 9 ? '9+' : Auth::user()->unread_notifications_count }}
                        </span>
                    @endif
                </a>

                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <button @click="open = !open"
                        class="flex items-center gap-2.5 px-2 py-1.5 rounded-xl hover:bg-gray-100 transition-colors">
                        <img src="{{ Auth::user()->profile_photo }}" alt="{{ Auth::user()->name }}"
                            class="w-8 h-8 rounded-full object-cover ring-2 ring-green-600/20">
                        <div class="text-left hidden xl:block">
                            <p class="text-sm font-semibold text-gray-700 leading-tight">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-400 leading-tight">Kontributor</p>
                        </div>
                        <i class="fa-solid fa-chevron-down text-xs text-gray-400 ml-0.5"
                            :class="{ 'rotate-180': open }"></i>
                    </button>

                    <div x-show="open" x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                        class="absolute right-0 mt-2 w-52 bg-white rounded shadow-lg ring-1 ring-black/5 py-1 z-50">

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="flex w-full items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                <i class="fa-solid fa-arrow-right-from-bracket w-4 text-center text-sm"></i>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        {{-- MAIN CONTENT SLOT --}}
        <main class="flex-1 pt-16 lg:p-6">
            {{ $slot }}
        </main>

    </div>


    {{-- SIDEBAR TOGGLE SCRIPT --}}
    <script>
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        const openBtn = document.getElementById('sidebar-open-btn');
        const closeBtn = document.getElementById('sidebar-close-btn');

        function openSidebar() {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeSidebar() {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        openBtn?.addEventListener('click', openSidebar);
        closeBtn?.addEventListener('click', closeSidebar);
        overlay?.addEventListener('click', closeSidebar);
    </script>
</body>

</html>
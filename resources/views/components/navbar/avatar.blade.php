<div class="relative" x-data="{ open: false }" @click.outside="open = false">
    {{-- Avatar Button --}}
    <button @click="open = !open"
        class="focus:outline-none focus:ring-2 focus:ring-green-600 focus:ring-offset-2 rounded-full">
        <img class="inline-block size-11 rounded-full object-cover" src="{{ Auth::user()->profile_photo }}"
            alt="Avatar">
    </button>

    {{-- Dropdown Panel --}}
    <div x-show="open" x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute right-0 mt-2 w-52 rounded-xl bg-white shadow-lg ring-1 ring-black/5 focus:outline-none z-50">
        
        {{-- User Info --}}
        <div class="px-4 py-3 border-b border-gray-100">
            <p class="text-sm font-semibold text-gray-800 truncate">{{ Auth::user()->name }}</p>
            <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
        </div>

        <div class="py-1">
            <a href="/profile"
                class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-green-50 hover:text-green-700 transition-colors"
                @click="open = false">
                <i class="fa-regular fa-user w-4 text-center text-green-600"></i>
                Detail Profil
            </a>

            <a href="{{ route('user.news') }}"
                class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-green-50 hover:text-green-700 transition-colors"
                @click="open = false">
                <i class="fa-regular fa-newspaper w-4 text-center text-green-600"></i>
                Kelola Beritamu
            </a>
        </div>

        {{-- Divider --}}
        <x-divider />

        {{-- Section 2: Logout --}}
        <div class="py-1">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="flex w-full items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
                    <i class="fa-solid fa-arrow-right-from-bracket w-4 text-center"></i>
                    Logout
                </button>
            </form>
        </div>
    </div>
</div>
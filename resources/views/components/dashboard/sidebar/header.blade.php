{{-- Logo --}}
<div class="h-16 flex items-center justify-between px-6 border-b border-gray-100 shrink-0">
    <a href="/" class="flex items-center gap-2.5">
        <div class="w-8 h-8 bg-green-600 rounded-lg flex items-center justify-center">
            <i class="fa-solid fa-newspaper text-white text-sm"></i>
        </div>
        <span class="font-bold text-gray-800 text-base tracking-tight">{{ config('app.name') }}</span>
    </a>
    <button id="sidebar-close-btn"
        class="lg:hidden p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors"
        aria-label="Tutup menu">
        <i class="fa-solid fa-xmark text-base"></i>
    </button>
</div>

{{-- User Profile Card --}}
<div class="px-4 py-4 border-b border-gray-100 shrink-0">
    <div class="flex items-center gap-3 p-3 bg-green-50 rounded-xl">
        <img src="{{ Auth::user()->profile_photo }}" alt="{{ Auth::user()->name }}"
            class="w-10 h-10 rounded-full object-cover ring-2 ring-green-200 shrink-0">
        <div class="min-w-0">
            <p class="text-sm font-semibold text-gray-800 truncate">{{ Auth::user()->name }}</p>
            <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
        </div>
    </div>
</div>
<div
    class="lg:hidden fixed top-0 inset-x-0 z-40 h-16 bg-white border-b border-gray-200 flex items-center justify-between px-4 shadow-sm">
    <button id="sidebar-open-btn"
        class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-green-600 transition-colors"
        aria-label="Buka menu">
        <i class="fa-solid fa-bars text-lg"></i>
    </button>

    <a href="/" class="flex items-center gap-2">
        <span class="text-green-600 font-bold text-lg tracking-tight">{{ config('app.name') }}</span>
    </a>

    <div class="flex items-center gap-2">
        <a href="{{ route('notifications.index') }}"
            class="relative p-2 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-green-600 transition-colors">
            <i class="fa-regular fa-bell text-lg"></i>
            @if(Auth::user()->unread_notifications_count > 0)
                <span
                    class="absolute top-1 right-1 w-4 h-4 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center leading-none">
                    {{ Auth::user()->unread_notifications_count > 9 ? '9+' : Auth::user()->unread_notifications_count }}
                </span>
            @endif
        </a>

        <a href="/">
            <img src="{{ Auth::user()->profile_photo }}" alt="Avatar"
                class="w-8 h-8 rounded-full object-cover ring-2 ring-green-600/20">
        </a>
    </div>
</div>
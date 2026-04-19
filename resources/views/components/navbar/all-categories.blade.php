<div x-data="{ open: false }" class="relative">
    <button 
        @click="open = !open"
        type="button"
        class="px-4 py-2 font-semibold inline-flex items-center gap-2 whitespace-nowrap hover:text-green-600 transition-colors"
        :class="{ 'text-green-600': open }"
    >
        Selengkapnya
        <i class="fa-solid fa-chevron-down transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
    </button>

    <!-- Dropdown megamenu -->
    <div 
        x-show="open" 
        x-cloak
        x-transition
        @click.outside="open = false"
        class="w-full bg-white border-t shadow mt-2"
    >            
    <div class="flex flex-wrap gap-2 p-3">
        @foreach($categories as $category)
            <a href="{{ route('kategori.show', $category->slug) }}"
                class="px-3 py-1 text-sm bg-gray-100 rounded hover:bg-gray-200">
                {{ $category->name }}
            </a>
        @endforeach
    </div>
</div>
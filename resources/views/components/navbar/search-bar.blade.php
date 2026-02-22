<form class="flex items-center max-w-sm mx-auto">
    <div class="relative w-full">
        <!-- Icon -->
        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
            <i class="fa-solid fa-magnifying-glass text-gray-500 text-sm"></i>
        </div>
        
        <x-text-input type="text" id="search-news"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-2xl focus:ring-green-700 focus:border-green-700 block w-full ps-10 py-2.5 px-8"
            placeholder="Berita apa yang ingin anda cari?" required />
    </div>

    <button type="submit"
        class="py-2.5 px-4 ms-2 text-sm font-medium text-white bg-primary rounded-lg border border-light-green hover:bg-green-500">
        <span>Cari</span>
    </button>
</form>
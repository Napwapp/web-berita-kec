<div class="flex gap-3">
    <a href="{{ route('login') }}">
        <x-primary-button class="px-4 py-2 rounded-lg hover:bg-green-600 transition">
            Masuk
        </x-primary-button>
    </a>
    
    <a href="{{ route('register') }}">
        <x-secondary-button
            class="px-4 py-2 border-2 border-green-600 text-primary font-medium rounded-lg hover:bg-green-50 transition">
            Daftar
        </x-secondary-button>
    </a>
</div>
<x-auth-layout heading="Lupa Password?" title="Reset Password">
    <x-slot name="header">
        <x-flash-messages></x-flash-messages>
    </x-slot>
    
    <div class="text-center mb-4 text-sm text-gray-700">
        {{ __('Lupa password kamu?? Jangan khawatir. Masukkan email kamu di bawah ini dan kami akan mengirimkan link untuk mereset password ke email kamu.') }}
    </div>

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input placeholder="Masukkan email kamu" aria-placeholder="Masukkan email kamu" id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
        </div>

        <div class="mt-4">
            <x-button class="w-full justify-center">
                {{ __('Kirim Link Reset Password') }}
            </x-button>
        </div>
    </form>
</x-auth-layout>

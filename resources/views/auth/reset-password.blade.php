<x-auth-layout heading="Reset Password" title="Reset Password">
    <div class="text-center mb-4 text-sm text-gray-700">
        {{ __('Reset password kamu di sini. Silahkan masukkan password baru nya.') }}
    </div>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input placeholder="Masukkan email kamu" id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" readonly/>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password baru')" />
            <x-text-input aria-placeholder="Masukkan password baru anda" placeholder="Masukkan password baru anda" id="password" class="block mt-1 w-full"
                type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Konfirmasi password')" />

            <x-text-input aria-placeholder="Konfirmasi password baru anda" placeholder="Konfirmasi password baru anda" id="password_confirmation"
                class="block mt-1 w-full" type="password" name="password_confirmation" required
                autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-button class="w-full">
                {{ __('Reset Password') }}
            </x-button>
        </div>
    </form>
</x-auth-layout>
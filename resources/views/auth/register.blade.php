<x-auth-layout title="Daftar Akun" heading="Daftar Akun Baru">
    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
        @csrf
        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Nama Pengguna')" />
            <x-text-input placeholder="Masukkan nama anda" id="name" class="block mt-1 w-full" type="text" name="name"
                :value="old('name')" autofocus autocomplete="name" required />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input placeholder="Contoh: user@domain.com" id="email" class="block mt-1 w-full" type="email"
                name="email" :value="old('email')" autocomplete="username" required />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input placeholder="Password minimal 8 karakter" id="password" class="block mt-1 w-full"
                type="password" name="password" autocomplete="new-password" required />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" />
            <x-text-input placeholder="Konfirmasi password anda" id="password_confirmation" class="block mt-1 w-full"
                type="password" name="password_confirmation" autocomplete="new-password" required />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="mt-4 border-gray-300">
            <x-input-label class="mb-1" for="profile_picture" :value="__('Foto profil (Opsional)')" />
            <x-file-input name="profile_picture" id="profile_picture" label="Foto profil (Opsional)" accept="image/*"
                hint="Format file: JPG, PNG, JPEG, WEBP. Max: 2MB." :value="old('profile_picture')" />
            <x-input-error :messages="$errors->get('profile_picture')" class="mt-2" />
        </div>

        <div class="mt-4">

        </div>

        <div class="flex flex-row justify-end mt-2">
            <a class="text-md text-gray-700 hover:text-green-700 underline" href="{{ route('login') }}">
                {{ __('Sudah punya akun? Masuk di sini') }}
            </a>
        </div>

        <div class="flex flex-col items-center mt-4 gap-2">
            <x-primary-button class="w-full">
                {{ __('Daftar Akun') }}
            </x-primary-button>

            <!-- Divider -->
            <x-divider>Atau</x-divider>

            <x-button href="{{ route('google.redirect') }}" variant="secondary" type="button" class="w-full flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 48 48" class="flex-shrink-0">
                        <path fill="#FFC107" d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12c0-6.627,5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C12.955,4,4,12.955,4,24c0,11.045,8.955,20,20,20c11.045,0,20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z" />
                        <path fill="#FF3D00" d="M6.306,14.691l6.571,4.819C14.655,15.108,18.961,12,24,12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C16.318,4,9.656,8.337,6.306,14.691z" />
                        <path fill="#4CAF50" d="M24,44c5.166,0,9.86-1.977,13.409-5.192l-6.19-5.238C29.211,35.091,26.715,36,24,36c-5.202,0-9.619-3.317-11.283-7.946l-6.522,5.025C9.505,39.556,16.227,44,24,44z" />
                        <path fill="#1976D2" d="M43.611,20.083H42V20H24v8h11.303c-0.792,2.237-2.231,4.166-4.087,5.571c0.001-0.001,0.002-0.001,0.003-0.002l6.19,5.238C36.971,39.205,44,34,44,24C44,22.659,43.862,21.35,43.611,20.083z" />
                    </svg>
                    {{ __('Masuk dengan Google') }}
            </x-button>
        </div>
    </form>
</x-auth-layout>
<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    // Auto set email_verified_at saat admin membuat user
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['email_verified_at'] = now();

        // Generate password yang lebih mudah diingat
        // Contoh: JohnDoe@2024
        $name = preg_replace('/[^a-zA-Z0-9]/', '', $data['name']); // Hapus karakter spesial
        $randomNumber = rand(100, 999);
        $password = $name . '@' . date('Y') . $randomNumber;

        // Atau format lain: user123!ABC
        // $password = strtolower($name) . rand(100, 999) . '!';

        $data['password'] = Hash::make($password);
        $data['password_confirmation'] = $data['password'];

        // Simpan password asli
        session()->flash('generated_password', $password);

        return $data;
    }

    protected function afterCreate(): void
    {
        $password = session('generated_password');
        $userName = $this->record->name;

        \Filament\Notifications\Notification::make()
            ->title('✅ User Berhasil Ditambahkan!')
            ->body("User: {$userName}\nPassword: {$password}\n\n⚠️ Harap catat password ini dan berikan ke user yang bersangkutan.\nUser dapat mengubah passwordnya setelah login.")
            ->success()
            ->persistent()
            ->send();
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return null;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

}

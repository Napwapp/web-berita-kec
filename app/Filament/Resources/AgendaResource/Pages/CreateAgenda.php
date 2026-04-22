<?php

namespace App\Filament\Resources\AgendaResource\Pages;

use App\Filament\Resources\AgendaResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateAgenda extends CreateRecord
{
    protected static string $resource = AgendaResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Agenda berhasil dibuat';
    }

    // Buat data slug sebelum disimpan, pastikan unik
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['slug'] = $this->resolveUniqueSlug(
            $data['slug'] ?? $data['title'] ?? ''
        );

        return $data;
    }

    // Jika slug sudah ada, tambahkan suffix angka untuk memastikan keunikan
    private function resolveUniqueSlug(string $base): string
    {
        $slug = Str::slug($base);
        $original = $slug;
        $model = static::getResource()::getModel();
        $i = 2;

        while ($model::where('slug', $slug)->exists()) {
            $slug = $original . '-' . $i++;
        }

        return $slug;
    }

}
<?php

namespace App\Filament\Resources\CategoryAgendaResource\Pages;

use App\Filament\Resources\CategoryAgendaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCategoryAgenda extends EditRecord
{
    protected static string $resource = CategoryAgendaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Agenda berhasil diperbarui';
    }
}

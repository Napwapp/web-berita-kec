<?php

namespace App\Filament\Resources\CategoryAgendaResource\Pages;

use App\Filament\Resources\CategoryAgendaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCategoryAgendas extends ListRecords
{
    protected static string $resource = CategoryAgendaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

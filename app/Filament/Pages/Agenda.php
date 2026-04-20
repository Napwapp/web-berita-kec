<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Agenda extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static string $view = 'filament.pages.agenda';
    protected static ?string $navigationLabel = 'Agenda Kecamatan';
}
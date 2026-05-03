<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NewsResource\Pages;
use App\Filament\Resources\NewsResource\RelationManagers;
use App\Models\NewsContent;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use App\Filament\Resources\Schemas\NewsForm;
use App\Filament\Resources\Schemas\Tables\NewsTable;

class NewsResource extends Resource
{
    protected static ?string $model = NewsContent::class;
    protected static ?string $navigationIcon = 'heroicon-o-newspaper';
    protected static ?string $navigationLabel = 'Berita';
    protected static ?string $modelLabel = 'Berita';
    protected static ?string $pluralModelLabel = 'Berita';
    protected static ?string $navigationGroup = 'Kelola Berita';
    protected static ?int $navigationSort = 0;

    // Form tambah berita & kategori
    public static function form(Form $form): Form
    {
        return $form
            ->schema(NewsForm::schema())
            ->columns(3);
    }

    // Tabel data berita
    public static function table(Table $table): Table
    {
        return NewsTable::table($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNews::route('/'),
            'create' => Pages\CreateNews::route('/create'),
            'edit' => Pages\EditNews::route('/{record}/edit'),
        ];
    }
}

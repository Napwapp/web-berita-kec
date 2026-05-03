<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryAgendaResource\Pages;
use App\Filament\Resources\CategoryAgendaResource\RelationManagers;
use App\Models\CategoryAgenda;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Str;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ColorColumn;
use Illuminate\Support\HtmlString;

class CategoryAgendaResource extends Resource
{
    protected static ?string $model = CategoryAgenda::class;
    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationLabel = 'Kategori Agenda Kecamatan';
    protected static ?string $modelLabel = 'Kategori Agenda Kecamatan';
    protected static ?string $pluralModelLabel = 'Kategori Agenda Kecamatan';
    protected static ?string $navigationGroup = 'Kelola Agenda Kecamatan';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Kategori')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Kategori')
                            ->required()
                            ->maxLength(255)
                            ->afterStateUpdated(function (string $state, Set $set): void {
                                $set('slug', Str::slug($state));
                            }),

                        ColorPicker::make('color')
                            ->label('Warna Kategori (Opsional)')
                            ->nullable()
                            ->helperText('Pilih warna yang diinginkan untuk membedakan warna pada kategori ini.'),
                        ])
                ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Kategori')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn(string $state, $record): string => $record->color ?? '#6B7280'),

                TextColumn::make('color')
                    ->label('Warna')
                    ->placeholder('Tidak ada warna')
                    ->formatStateUsing(function ($state) {
                        if (empty($state)) {
                            return null;
                        }

                        return new HtmlString("
                            <div class='flex items-center gap-2'>
                                <span 
                                    class='inline-block w-6 h-6 rounded-md'
                                    style='background-color: {$state};'
                                ></span>
                                <span class='font-mono text-sm font-medium'>{$state}</span>
                            </div>
                        ");
                    })
                    ->html(),

                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Terakhir Diperbarui')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListCategoryAgendas::route('/'),
            'create' => Pages\CreateCategoryAgenda::route('/create'),
            'edit' => Pages\EditCategoryAgenda::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;
    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationLabel = 'Kategori Berita';
    protected static ?string $modelLabel = 'Kategori Berita';
    protected static ?string $pluralModelLabel = 'Kategori Berita';
    protected static ?string $navigationGroup = 'Kelola Berita';
    protected static ?int $navigationSort = 2;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama Kategori')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        $set('slug', Str::slug($state));
                    }),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
        ->description('Kategori Unggulan akan ditampilkan di beranda untuk pengunjung. Hanya bisa memilih maksimal 3 kategori untuk dijadikan Kategori Unggulan.')
        ->columns([
                // Toggle Featured kategori
                Tables\Columns\IconColumn::make('is_featured')
                    ->label(fn() => new \Illuminate\Support\HtmlString(
                        'Unggulan <span class="text-xs font-normal text-gray-400">(' .
                        Category::where('is_featured', true)->count() .
                        '/3)</span>'
                    ))
                    ->boolean()
                    ->trueIcon('heroicon-s-star')
                    ->falseIcon('heroicon-o-star')
                    ->trueColor('warning')
                    ->falseColor('gray')
                    ->tooltip(
                        fn($record) => $record->is_featured
                        ? 'Klik untuk menghapus kategori ini dari Kategori Unggulan'
                        : 'Klik untuk menjadikan kategori ini sebagai Kategori Unggulan'
                    )
                    ->action(
                        Action::make('toggleFeatured')
                            ->modalHeading(
                                fn($record) => $record->is_featured
                                ? 'Hapus dari Unggulan?'
                                : 'Jadikan Kategori Unggulan?'
                            )
                            ->modalDescription(
                                fn($record) => $record->is_featured
                                ? "Kategori \"{$record->name}\" tidak akan lagi tampil di beranda."
                                : "Kategori \"{$record->name}\" akan ditampilkan di beranda sebagai kategori pilihan. Hanya 3 kategori yang bisa menjadi unggulan."
                            )
                            ->modalSubmitActionLabel(
                                fn($record) => $record->is_featured
                                ? 'Ya, Hapus'
                                : 'Ya, Jadikan Unggulan'
                            )
                            ->modalIcon(
                                fn($record) => $record->is_featured
                                ? 'heroicon-o-x-circle'
                                : 'heroicon-s-star'
                            )
                            ->modalIconColor(fn($record) => $record->is_featured ? 'danger' : 'warning')
                            ->action(function ($record) {
                                if ($record->is_featured) {
                                    $record->update(['is_featured' => false]);

                                    Notification::make()
                                        ->title("Kategori \"{$record->name}\" dihapus dari unggulan.")
                                        ->success()
                                        ->send();

                                    return;
                                }

                                $featuredCount = \App\Models\Category::where('is_featured', true)->count();

                                if ($featuredCount >= 3) {
                                    Notification::make()
                                        ->title('Batas unggulan tercapai')
                                        ->body('Sudah ada 3 kategori unggulan. Hapus salah satu terlebih dahulu sebelum menambahkan yang baru.')
                                        ->danger()
                                        ->persistent()
                                        ->send();

                                    return;
                                }

                                $record->update(['is_featured' => true]);

                                Notification::make()
                                    ->title("Kategori \"{$record->name}\" berhasil dijadikan unggulan.")
                                    ->success()
                                    ->send();
                            })
                    ),

                TextColumn::make('name')
                    ->label('Nama Kategori')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('news_count')
                    ->label('Jumlah Berita')
                    ->counts('news')
                    ->badge()
                    ->color('warning')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('updated_at')
                    ->label('Terakhir Diupdate')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}

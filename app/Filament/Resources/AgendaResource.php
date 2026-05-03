<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AgendaResource\Pages;
use Filament\Resources\Resource;
use App\Filament\Resources\Schemas\Forms\AgendaForms;
use App\Filament\Resources\Schemas\Tables\AgendaTables;
use App\Models\Agenda;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Form;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;


class AgendaResource extends Resource
{
    protected static ?string $model = Agenda::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Agenda Kecamatan';
    protected static ?string $modelLabel = 'Agenda Kecamatan';
    protected static ?string $pluralModelLabel = 'Agenda';
    protected static ?string $navigationGroup = 'Kelola Agenda Kecamatan';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema(AgendaForms::schema())
            ->columns(['lg' => 3]);
    }

    public static function table(Table $table): Table
    {
        return AgendaTables::table($table) // Tabel di file terpisah

            // Actions
            ->actions([
                // Publikasi dan Archive
                Tables\Actions\Action::make('publish')
                    ->label('Terbitkan')
                    ->icon('heroicon-m-paper-airplane')
                    ->color('success')
                    ->visible(
                        fn($record) =>
                        !$record->trashed() && $record->status === 'draft'
                    )
                    ->requiresConfirmation()
                    ->modalHeading('Terbitkan Agenda?')
                    ->modalDescription('Agenda ini akan ditampilkan ke publik setelah diterbitkan.')
                    ->successNotificationTitle('Agenda berhasil diterbitkan')
                    ->action(fn($record) => $record->publish()),

                Tables\Actions\Action::make('archive')
                    ->label('Arsipkan')
                    ->icon('heroicon-m-archive-box')
                    ->color('warning')
                    ->visible(fn($record) => !$record->trashed() && $record->status === 'published')
                    ->requiresConfirmation()
                    ->modalHeading('Arsipkan Agenda?')
                    ->modalDescription('Agenda ini tidak akan tampil ke publik setelah diarsipkan.')
                    ->successNotificationTitle('Agenda berhasil diarsipkan')
                    ->action(fn($record) => $record->archive()),

                Tables\Actions\Action::make('unarchive')
                    ->label('Lepas Arsip')
                    ->icon('heroicon-m-arrow-uturn-left')
                    ->color('info')
                    ->visible(fn($record) => !$record->trashed() && $record->status === 'archived')
                    ->requiresConfirmation()
                    ->modalHeading('Lepas Arsip Agenda?')
                    ->modalDescription('Agenda ini akan kembali ditampilkan ke publik.')
                    ->successNotificationTitle('Agenda berhasil dilepas dari arsip')
                    ->action(fn($record) => $record->unarchive()),

                Tables\Actions\RestoreAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])

            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('bulk_publish')
                        ->label('Terbitkan Terpilih')
                        ->icon('heroicon-m-paper-airplane')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn($records) => $records->each->publish()),

                    Tables\Actions\BulkAction::make('bulk_archive')
                        ->label('Arsipkan Terpilih')
                        ->icon('heroicon-m-archive-box')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(fn($records) => $records->each->archive()),

                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('start_at', 'asc')
            ->modifyQueryUsing(fn(Builder $query) => $query->with('category'))
            ->emptyStateIcon('heroicon-o-calendar-days')
            ->emptyStateHeading('Belum ada agenda')
            ->emptyStateDescription('Buat agenda baru untuk menampilkan jadwal kegiatan kecamatan.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()->label('Buat Agenda Baru'),
            ])
            ->striped();
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
            'index' => Pages\ListAgendas::route('/'),
            'create' => Pages\CreateAgenda::route('/create'),
            'view' => Pages\ViewAgenda::route('/{record}'),
            'edit' => Pages\EditAgenda::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class]);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'published')
            ->whereDate('end_at', '>=', now())
            ->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Agenda aktif yang sedang berlangsung / akan datang';
    }
}

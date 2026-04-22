<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AgendaResource\Pages;
use Filament\Resources\Resource;

// Models
use App\Models\Agenda;
use App\Models\CategoryAgenda;

// Filament Tables
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\Filter;

// Filament Forms
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Components\Actions\Action;

// Eloquent
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Carbon\Carbon;

// Support
use Illuminate\Support\Str;

class AgendaResource extends Resource
{
    protected static ?string $model = Agenda::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Agenda';
    protected static ?string $modelLabel = 'Agenda';
    protected static ?string $pluralModelLabel = 'Agenda';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()
                    ->schema([
                        // ── Informasi Utama ───────────────────────────
                        Section::make('Informasi Agenda')
                            ->schema([
                                TextInput::make('title')
                                    ->label('Agenda')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (string $state, Set $set): void {
                                        $set('slug', Str::slug($state));
                                    })
                                    ->columnSpanFull(),

                                Textarea::make('description')
                                    ->label('Deskripsi (Opsional)')
                                    ->rows(5)
                                    ->nullable()
                                    ->columnSpanFull(),
                            ]),

                        // ── Waktu Pelaksanaan ─────────────────────────
                        Section::make('Waktu Pelaksanaan')
                            ->schema([
                                Toggle::make('is_all_day')
                                    ->label('Sepanjang Hari')
                                    ->helperText('Aktifkan jika agenda berlangsung sepanjang hari tanpa jam tertentu.')
                                    ->default(false)
                                    ->live()
                                    ->columnSpanFull(),

                                DateTimePicker::make('start_at')
                                    ->label('Tanggal & Jam Mulai')
                                    ->required()
                                    ->seconds(false)
                                    ->displayFormat(function (Get $get): string {
                                        return $get('is_all_day') ? 'd/m/Y' : 'd/m/Y H:i';
                                    }),

                                DateTimePicker::make('end_at')
                                    ->label('Tanggal & Jam Selesai')
                                    ->required()
                                    ->seconds(false)
                                    ->after('start_at')
                                    ->displayFormat(function (Get $get): string {
                                        return $get('is_all_day') ? 'd/m/Y' : 'd/m/Y H:i';
                                    }),
                            ])
                            ->columns(2),

                    ])
                    ->columnSpan(['lg' => 2]),


                Group::make()
                    ->schema([
                        // Publikasi 
                        Section::make('Publikasi')
                            ->schema([
                                Toggle::make('is_published')
                                    ->label('Terbitkan Agenda')
                                    ->helperText(function (Get $get): string {
                                        return $get('is_published')
                                            ? 'Agenda akan tampil ke publik.'
                                            : 'Jika tidak diterbitkan, agenda disimpan sebagai draft.';
                                    })
                                    ->default(false)
                                    ->live(),

                                // Hint readonly — tampil saat sudah pernah diterbitkan
                                Placeholder::make('published_at_info')
                                    ->label('Diterbitkan pada')
                                    ->content(
                                        fn($record) => $record?->published_at
                                        ? Carbon::parse($record->published_at)
                                            ->translatedFormat('d F Y, H:i') . ' WIB'
                                        : '—'
                                    )
                                    ->visible(fn($record) => filled($record?->published_at)),
                            ]),

                        // ── Kategori ──────────────────────────────────
                        Section::make('Kategori')
                            ->schema([
                                Select::make('category_id')
                                    ->label('Kategori Agenda (Opsional)')
                                    ->options(CategoryAgenda::pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->nullable()
                                    ->noSearchResultsMessage('Kategori tidak ditemukan.')

                                    // ── Modal inline: Tambah Kategori ───
                                    ->createOptionForm([
                                        TextInput::make('name')
                                            ->label('Nama Kategori')
                                            ->required()
                                            ->maxLength(100)
                                            ->live(onBlur: true)
                                        ->afterStateUpdated(function (string $state, Set $set): void {
                                            $set('slug', Str::slug($state));
                                        }),
                                        ColorPicker::make('color')
                                            ->label('Warna Kategori')
                                            ->nullable()
                                            ->helperText('Pilih warna untuk membedakan kategori ini.'),
                                    ])

                                    ->createOptionAction(function (Action $action) {
                                        $action
                                            ->label('Tambah Kategori')
                                            ->icon('heroicon-m-plus')
                                            ->modalHeading('Buat Kategori Baru')
                                            ->modalDescription('Tambahkan kategori agenda baru. Slug akan terisi otomatis dari nama.')
                                            ->modalSubmitActionLabel('Simpan Kategori')
                                            ->modalWidth('md');
                                    })
                                    ->createOptionUsing(function (array $data): int {
                                        return CategoryAgenda::create([
                                            'name' => $data['name'],
                                            'slug' => $data['slug'] ?? Str::slug($data['name']),
                                            'color' => $data['color'] ?? null,
                                        ])->getKey();
                                    }),
                            ]),

                        // ── Lokasi & Tautan ───────────────────────────
                        Section::make('Lokasi & Tautan')
                            ->schema([
                                Toggle::make('is_online')
                                    ->label('Agenda Online')
                                    ->helperText('Aktifkan jika agenda dilaksanakan secara daring.')
                                    ->default(false)
                                    ->live(),

                                TextInput::make('online_link')
                                    ->label('Tautan Meeting / Webinar')
                                    ->url()
                                    ->nullable()
                                    ->placeholder('Contoh: https://meet.google.com/...')
                                    ->prefixIcon('heroicon-m-video-camera')
                                    ->visible(function (Get $get): bool {
                                        return $get('is_online');
                                    })
                                    ->required(function (Get $get): bool {
                                        return $get('is_online');
                                    })
                                    ->helperText('Masukkan link Zoom, Meet, atau platform daring lainnya.'),

                                TextInput::make('location')
                                    ->label('Lokasi / Tempat')
                                    ->nullable()
                                    ->placeholder('Contoh: Aula Kecamatan Binong')
                                    ->prefixIcon('heroicon-m-map-pin')
                                    ->visible(function (Get $get): bool {
                                        return !$get('is_online');
                                    })
                                    ->required(function (Get $get): bool {
                                        return !$get('is_online');
                                    })
                                    ->helperText('Masukkan alamat lengkap tempat pelaksanaan agenda.'),
                            ]),

                    ])
                    ->columnSpan(['lg' => 1]),

            ])
            ->columns(['lg' => 3]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->limit(60)
                    ->tooltip(fn($record) => $record->title),

                TextColumn::make('description')
                    ->label('Deskripsi')
                    ->searchable()
                    ->limit(80)
                    ->tooltip(fn($record) => $record->description)
                    ->wrap()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('start_at')
                    ->label('Tanggal')
                    ->sortable()
                    ->formatStateUsing(function ($record): string {
                        $start = Carbon::parse($record->start_at);
                        $end = Carbon::parse($record->end_at);

                        if ($record->is_all_day) {
                            return $start->isSameDay($end)
                                ? $start->translatedFormat('d M Y') . ' · Sepanjang Hari'
                                : $start->translatedFormat('d M Y') . ' – ' . $end->translatedFormat('d M Y') . ' · Sepanjang Hari';
                        }

                        return $start->isSameDay($end)
                            ? $start->translatedFormat('d M Y') . "\n" . $start->format('H:i') . ' – ' . $end->format('H:i') . ' WIB'
                            : $start->translatedFormat('d M Y H:i') . ' –' . "\n" . $end->translatedFormat('d M Y H:i') . ' WIB';
                    })
                    ->icon('heroicon-m-clock')
                    ->weight('medium'),

                TextColumn::make('time_status')
                    ->label('Status Waktu')
                    ->badge()
                    ->getStateUsing(function ($record): string {
                        if ($record->isOngoing()) {
                            return 'ongoing';
                        }

                        if ($record->isUpcoming()) {
                            return Carbon::parse($record->start_at)->diffForHumans();
                        }

                        return 'Selesai ' . Carbon::parse($record->end_at)->diffForHumans();
                    })
                    ->formatStateUsing(function ($record): string {
                        if ($record->isOngoing()) {
                            return 'Sedang Berlangsung';
                        }

                        if ($record->isUpcoming()) {
                            return Carbon::parse($record->start_at)->diffForHumans();
                        }

                        return 'Selesai ' . Carbon::parse($record->end_at)->diffForHumans();
                    })
                    ->color(function ($record): string {
                        if ($record->isOngoing())
                            return 'success';
                        if ($record->isUpcoming())
                            return 'warning';
                        return 'info';

                    })
                    ->icon(function ($record): string {
                        if ($record->isOngoing())
                            return 'heroicon-m-arrow-path';
                        if ($record->isUpcoming())
                            return 'heroicon-m-clock';
                        return 'heroicon-m-check-badge';
                    }),

                TextColumn::make('online_link')
                    ->label('Tautan')
                    ->formatStateUsing(fn($state) => $state ? 'Buka Tautan' : '-')
                    ->url(fn($record) => ($record->is_online && $record->online_link) ? $record->online_link : null)
                    ->openUrlInNewTab()
                    ->color(fn($record) => ($record->is_online && $record->online_link) ? 'info' : 'gray')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('location')
                    ->label('Lokasi')
                    ->searchable()
                    ->placeholder('Online')
                    ->limit(40)
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('status')
                    ->label('Status')
                    ->sortable()
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'draft' => 'Draft',
                        'published' => 'Diterbitkan',
                        'archived' => 'Diarsipkan',
                        default => $state,
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'draft' => 'warning',
                        'published' => 'success',
                        'archived' => 'warning',
                        default => 'warning',
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'draft' => 'heroicon-m-pencil-square',
                        'published' => 'heroicon-m-check-circle',
                        'archived' => 'heroicon-m-archive-box',
                        default => 'heroicon-m-question-mark-circle',
                    }),

                TextColumn::make('published_at')
                    ->label('Dipublikasikan Pada')
                    ->sortable()
                    ->dateTime('d M Y, H:i')
                    ->placeholder('Belum diterbitkan')
                    ->description(
                        fn($record) => $record->published_at
                        ? Carbon::parse($record->published_at)->translatedFormat('l')
                        : null
                    )
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Diterbitkan',
                        'archived' => 'Arsip',
                    ])
                    ->multiple()
                    ->preload(),

                SelectFilter::make('category_id')
                    ->label('Kategori')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),

                TernaryFilter::make('is_online')
                    ->label('Lokasi Acara')
                    ->trueLabel('Secara Online')
                    ->falseLabel('Di Lokasi Fisik')
                    ->placeholder('Semua Mode'),

                Filter::make('start_at')
                    ->label('Periode Agenda')
                    ->form([
                        DatePicker::make('start_from')->label('Dari Tanggal'),
                        DatePicker::make('start_until')->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['start_from'], fn($q, $v) => $q->whereDate('start_at', '>=', $v))
                            ->when($data['start_until'], fn($q, $v) => $q->whereDate('start_at', '<=', $v));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['start_from'])
                            $indicators[] = 'Dari: ' . Carbon::parse($data['start_from'])->translatedFormat('d M Y');
                        if ($data['start_until'])
                            $indicators[] = 'Sampai: ' . Carbon::parse($data['start_until'])->translatedFormat('d M Y');
                        return $indicators;
                    }),

                Tables\Filters\TrashedFilter::make()->label('Data Terhapus'),
            ])

            ->actions([
                // Publikasi dan Archive
                Tables\Actions\Action::make('publish')
                    ->label('Terbitkan')
                    ->icon('heroicon-m-paper-airplane')
                    ->color('success')
                    ->visible(fn($record) =>
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
                    ->visible(fn($record) => !$record->trashed() && $record->status === 'archived' )
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

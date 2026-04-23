<?php
namespace App\Filament\Resources\Schemas\Tables;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;

use Illuminate\Database\Eloquent\Builder;

use Carbon\Carbon;

class AgendaTables
{
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
            ]);
    }
}
?>
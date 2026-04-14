<?php

namespace App\Filament\Resources\Schemas\Tables;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Filament\Support\Enums\FontWeight;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Storage;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use App\Services\CloudinaryService;
use Illuminate\Support\Facades\App;

class NewsTable
{
    public static function table(Table $table): Table
    {
        return $table
            // Modifikasi query untuk join dengan tabel news dan query dari berita yang di pin lalu desc
            ->modifyQueryUsing(
                fn(Builder $query) =>
                $query
                    ->join('news', 'news_contents.news_id', '=', 'news.id') // sesuaikan nama tabel
                    ->orderByRaw("
                    CASE
                        WHEN news.pinned_at IS NOT NULL
                        AND (news.pin_expired_at IS NULL OR news.pin_expired_at >= NOW())
                        THEN 0
                        ELSE 1
                    END ASC
                ")
                    ->orderBy('news_contents.created_at', 'desc')
                    ->select('news_contents.*')
            )


            ->columns([
                // Pin Badge
                IconColumn::make('is_pinned_indicator')
                    ->label('')
                    ->state(fn($record) => $record->news?->isPinned())
                    ->icon(fn($state) => $state ? 'heroicon-s-bookmark' : null)
                    ->color(fn($state) => $state ? 'warning' : 'gray')
                    ->tooltip(fn($state) => $state ? 'Disematkan' : null)
                    ->width(20),

                ImageColumn::make('thumbnail')
                    ->label('Thumbnail')
                    ->width(80)
                    ->height(50),

                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable()
                    ->limit(50)

                    // Highlight judul jika ada berita yang di pin
                    ->weight(
                        fn($record) => $record->news?->isPinned()
                        ? FontWeight::Bold
                        : null
                    )
                    ->color(
                        fn($record) => $record->news?->isPinned()
                        ? 'warning'
                        : null
                    ),

                TextColumn::make('news.author.name')
                    ->label('Penulis')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('version')
                    ->label('Versi')
                    ->sortable(),

                TextColumn::make('news.categories.name')
                    ->label('Kategori')
                    ->badge()
                    ->separator(',')
                    ->placeholder('-'),

                TextColumn::make('is_published')
                    ->label('Status Publikasi')
                    ->badge()
                    ->icon(fn($state) => $state ? 'heroicon-o-check-circle' : 'heroicon-o-clock')
                    ->color(fn($state) => $state ? 'success' : 'warning')
                    ->formatStateUsing(fn($state) => $state ? 'Telah Dipublikasi' : 'Draft'),

                TextColumn::make('published_at')
                    ->label('Tanggal Publikasi')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->placeholder('Belum dipublikasi'),

                TextColumn::make('news.views')
                    ->label('Views')
                    ->sortable(),

                TextColumn::make('news.likes')
                    ->label('Likes')
                    ->sortable(),
            ])

            ->filters([
                SelectFilter::make('news.categories')
                    ->label('Kategori')
                    ->relationship('news.categories', 'name')
                    ->multiple()
                    ->preload(),

                TernaryFilter::make('is_published')
                    ->label('Status Publikasi')
                    ->placeholder('Semua')
                    ->trueLabel('Sudah Dipublikasi')
                    ->falseLabel('Draft'),
            ])

            ->actions([
                Action::make('publish')
                    ->label('Publish')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (\App\Models\NewsContent $record) {
                        $record->publish();
                    })
                    ->visible(fn($record) => !$record->is_published),

                // Pin Berita
                Action::make('pin')
                    ->label('Sematkan')
                    ->icon('heroicon-o-bookmark')
                    ->color('warning')
                    ->visible(fn($record) => $record->is_published && !$record->news?->isPinned())

                    // 
                    ->modalHeading(function (\App\Models\NewsContent $record) {
                        $pinnedNews = \App\Models\News::whereNotNull('pinned_at')
                            ->where(function ($q) {
                                $q->whereNull('pin_expired_at')->orWhere('pin_expired_at', '>=', now());
                            })
                            ->where('id', '!=', $record->news_id)
                            ->exists();

                        return $pinnedNews
                            ? '⚠️ Sudah Ada Berita yang Disematkan'
                            : 'Sematkan Berita';
                    })
                    ->modalDescription(function (\App\Models\NewsContent $record) {
                        $pinnedNews = \App\Models\News::whereNotNull('pinned_at')
                            ->where(function ($q) {
                                $q->whereNull('pin_expired_at')->orWhere('pin_expired_at', '>=', now());
                            })
                            ->where('id', '!=', $record->news_id)
                            ->first();

                        // Jika sudah ada berita yang di pin
                        if ($pinnedNews) {
                            $pinnedTitle = $pinnedNews->currentVersion?->title ?? 'Tanpa Judul';
                            return new HtmlString(
                                "Saat ini berita <span class=\"font-bold text-base text-yellow-700 bg-yellow-50 px-3 py-2 rounded inline-block\">\"{$pinnedTitle}\"</span> sedang disematkan. Apakah kamu ingin menggantinya dengan berita ini?"
                            );
                        }

                        return null;
                    })
                    ->modalSubmitActionLabel(function (\App\Models\NewsContent $record) {
                        $hasPinned = \App\Models\News::whereNotNull('pinned_at')
                            ->where(function ($q) {
                                $q->whereNull('pin_expired_at')->orWhere('pin_expired_at', '>=', now());
                            })
                            ->where('id', '!=', $record->news_id)
                            ->exists();

                        return $hasPinned ? 'Ya, Ganti Sematan' : 'Sematkan';
                    })
                    ->form([
                        Forms\Components\Select::make('duration')
                            ->label('Durasi Sematkan')
                            ->options([
                                1 => '1 Hari',
                                3 => '3 Hari',
                                7 => '7 Hari',
                                30 => '30 Hari',
                                '' => 'Tidak Memilih (Berita akan tetap disematkan sampai kamu mencabutnya secara manual)',
                            ])
                            ->default('')
                            ->live()
                            ->helperText(
                                fn($state) => ($state === '' || $state === null)
                                ? 'Berita akan tetap disematkan sampai kamu mencabutnya secara manual.'
                                : null
                            ),
                    ])
                    ->action(function (\App\Models\NewsContent $record, array $data) {
                        $news = $record->news;
                        $duration = ($data['duration'] !== '' && $data['duration'] !== null)
                            ? (int) $data['duration']
                            : null;

                        // Cabut pin berita lain jika ada
                        \App\Models\News::whereNotNull('pinned_at')
                            ->where('id', '!=', $news->id)
                            ->each(fn($n) => $n->unpin());

                        // Pin berita ini
                        $news->pin($duration);

                        Notification::make()
                            ->success()
                            ->title('Berita berhasil disematkan!')
                            ->body($duration
                                ? "Berita akan disematkan selama {$duration} hari."
                                : 'Berita disematkan tanpa batas waktu.')
                            ->send();
                    }),

                // ── Aksi Cabut Sematan ─────────────────────────────────────
                Action::make('unpin')
                    ->label('Cabut Sematan')
                    ->icon('heroicon-o-bookmark-slash')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->modalHeading('Cabut Sematan Berita')
                    ->modalDescription('Apakah kamu yakin ingin mencabut sematan berita ini?')
                    ->modalSubmitActionLabel('Ya, Cabut')
                    // Hanya tampil jika berita ini sedang disematkan
                    ->visible(fn($record) => $record->news?->isPinned())
                    ->action(function (\App\Models\NewsContent $record) {
                        $record->news->unpin();

                        Notification::make()
                            ->success()
                            ->title('Sematan berhasil dicabut!')
                            ->send();
                    }),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])

            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                ]),
            ]);
    }
}
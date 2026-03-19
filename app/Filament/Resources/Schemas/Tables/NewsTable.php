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
use Illuminate\Support\Facades\Storage;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use App\Services\CloudinaryService;

class NewsTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('thumbnail')
                    ->label('Thumbnail')
                    ->width(80)
                    ->height(50),

                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable()
                    ->limit(50),

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

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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

                    // publish
                    ->action(function (\App\Models\NewsContent $record) {
                        $record->publish();
                    })
                    ->visible(fn($record) => !$record->is_published),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
            ])

            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                ]),
            ])

            ->defaultSort('created_at', 'desc');
    }
}
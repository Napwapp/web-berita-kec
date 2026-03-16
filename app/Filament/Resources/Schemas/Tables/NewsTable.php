<?php

namespace App\Filament\Resources\Schemas\Tables;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
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
                    ->separator(','),

                IconColumn::make('is_published')
                    ->label('Dipublikasi')
                    ->boolean(),

                TextColumn::make('published_at')
                    ->label('Tanggal Publikasi')
                    ->dateTime('d M Y')
                    ->sortable(),

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
                Tables\Actions\Action::make('publish')
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
                    ->before(function ($record) {
                        app(CloudinaryService::class)->deleteByUrl($record->thumbnail);
                    }),
            ])

            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function ($records) {
                            $cloudinaryService = app(CloudinaryService::class);
                            foreach ($records as $record) {
                                $cloudinaryService->deleteByUrl($record->thumbnail);
                            }
                        }),
                ]),
            ])

            ->defaultSort('created_at', 'desc');
    }
}
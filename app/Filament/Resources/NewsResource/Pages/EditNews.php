<?php

namespace App\Filament\Resources\NewsResource\Pages;

use App\Filament\Resources\NewsResource;
use App\Models\NewsContent;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EditNews extends EditRecord
{
    protected static string $resource = NewsResource::class;

    // Ambil data saat form edit dibuka
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $newsContent = $this->getRecord();
        $news = $newsContent->news;

        // Data sudah terisi dari NewsContent, hanya perlu ambil categories dari News
        if (!isset($data['categories'])) {
            $data['categories'] = $news->categories->pluck('id')->toArray();
        }

        return $data;
    }

    // Buat newscontent versi baru nya
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $newsContent = $record;
        $news = $newsContent->news;
        
        $newVersion = ($news->contents()->max('version') ?? 0) + 1;
        $isPublished = $data['is_published'] ?? false;

        // Versioning slug
        $baseSlug = Str::slug($data['title']);

        $slug = $newVersion > 1
            ? $baseSlug . '-v' . $newVersion
            : $baseSlug;

        // Buat versi baru di news_contents
        $newNewsContent = NewsContent::create([
            'news_id' => $news->id,
            'title' => $data['title'],
            'subtitle' => $data['subtitle'] ?? null,
            'slug' => $slug,
            'thumbnail' => $data['thumbnail'],
            'thumbnail_description' => $data['thumbnail_description'] ?? null,
            'excerpt' => $data['excerpt'] ?? null,
            'content' => $data['content'],
            'version' => $newVersion,
            'is_published' => $isPublished,
            'published_at' => $isPublished ? now() : null,
        ]);

        // Update current_version_id ke versi terbaru jika is_published nya true
        if ($isPublished) {
            $news->update(['current_version_id' => $newNewsContent->id]);
        }

        // Sinkronisasi kategori
        if (isset($data['categories'])) {
            $news->categories()->sync($data['categories']);
        } else {
            $news->categories()->detach();
        }

        return $newNewsContent;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    // Otomatis refresh halaman
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

<?php

namespace App\Filament\Resources\NewsResource\Pages;

use App\Filament\Resources\NewsResource;
use App\Models\NewsContent;
use App\Services\CloudinaryService;
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
        // Ambil news dan newsContent dari record yang sedang diedit
        $newsContent = $record;
        $news = $newsContent->news;

        // Hitung versi baru
        $newVersion = ($news->contents()->max('version') ?? 0) + 1;
        $isPublished = $data['is_published'] ?? false;

        // Jika thumbnail diganti, upload ke cloudinary dan hapus thumbnail lama
        $cloudinary = app(CloudinaryService::class);

        // Versioning slug
        $baseSlug = Str::slug($data['title']);

        // Jika versi baru lebih dari 1, tambhakan -v{version}
        $slug = $newVersion > 1
            ? $baseSlug . '-v' . $newVersion
            : $baseSlug;

        // Jika ada thumbnail baru
        $isNewThumbnail = !empty($data['thumbnail'])
            && !str_starts_with($data['thumbnail'], 'http');

        if ($isNewThumbnail) {
            // Upload thumbnail baru ke Cloudinary
            $tmpPath = storage_path('app/livewire-tmp/' . $data['thumbnail']);
            $thumbnailUrl = $cloudinary->upload($tmpPath, 'news/thumbnails');

            // Hapus thumbnail lama dari Cloudinary jika ada
            if ($newsContent->thumbnail) {
                $cloudinary->deleteByUrl($newsContent->thumbnail);
            }
        } else {
            // Pakai thumbnail dari record yang sedang diedit
            $thumbnailUrl = $newsContent->thumbnail;
        }

        // Buat versi baru di news_contents
        $newNewsContent = NewsContent::create([
            'news_id' => $news->id,
            'title' => $data['title'],
            'subtitle' => $data['subtitle'] ?? null,
            'slug' => $slug,
            'thumbnail' => $thumbnailUrl,
            'thumbnail_description' => $data['thumbnail_description'] ?? null,
            'excerpt' => $data['excerpt'] ?? null,
            'content' => $data['content'],
            'version' => $newVersion,
            'is_published' => $isPublished,
            'published_at' => $isPublished ? now() : null,
        ]);

        // Update current_version_id ke versi terbaru jika is_published nya true
        if ($isPublished) {
            // gunakan helper agar logic tetap konsisten
            $newNewsContent->publish();
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

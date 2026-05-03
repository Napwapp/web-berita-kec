<?php

namespace App\Filament\Resources\NewsResource\Pages;

use App\Filament\Resources\NewsResource;
use App\Models\NewsContent;
use App\Services\CloudinaryService;
use App\Helpers\ExcerptHelper;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

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

        // Mutate field type dari News
        if (!isset($data['type'])) {
            $data['type'] = $news->type;
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
        $status = $isPublished ? 'published' : 'draft';
        
        // Panggil method CLouidnaryService
        $cloudinary = app(CloudinaryService::class);

        // Jika ada thumbnail baru
        $thumbnailPath = $data['thumbnail'] ?? null;
        $isNewThumbnail = !empty($data['thumbnail']) && !str_starts_with($data['thumbnail'], 'http');

        if ($isNewThumbnail) {
            // Ambil full path
            $fullPath = Storage::disk('local')->path($thumbnailPath);

            // Upload thumbnail baru ke Cloudinary da hapus file temp nya
            $thumbnailUrl = $cloudinary->upload($fullPath, 'news/thumbnails');
            Storage::disk('local')->delete($thumbnailPath);

            // Hapus thumbnail lama dari Cloudinary jika ada
            if ($newsContent->thumbnail) {
                $cloudinary->deleteByUrl($newsContent->thumbnail);
            }
        } else {
            // Pakai thumbnail dari record yang sedang diedit
            $thumbnailUrl = $newsContent->thumbnail;
        }

        $excerpt = ExcerptHelper::generate($data['content'], 200);

        // Buat versi baru di news_contents
        $newNewsContent = NewsContent::create([
            'news_id' => $news->id,
            'title' => $data['title'],
            'subtitle' => $data['subtitle'] ?? null,
            'thumbnail' => $thumbnailUrl,
            'content' => $data['content'],
            'excerpt' => $excerpt,
            'version' => $newVersion,
            'is_published' => $isPublished,
            'published_at' => $isPublished ? now() : null,
        ]);

        // Sinkronisasi kategori
        if (isset($data['categories'])) {
            $news->categories()->sync($data['categories']);
        } else {
            $news->categories()->detach();
        }

        // Update field type pada News
        if (isset($data['type'])) {
            $news->update(['type' => $data['type']]);
        }

        // Update current_version_id ke versi terbaru jika is_published nya true
        if ($isPublished) {
            // gunakan helper agar logic tetap konsisten
            $newNewsContent->publish();
        }

        return $newNewsContent;
    }

    // Override method untuk optimasi gambar content berita sebelum update data
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['content'] = app(CloudinaryService::class)
            ->optimizeContentImages($data['content']);

        return $data;
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

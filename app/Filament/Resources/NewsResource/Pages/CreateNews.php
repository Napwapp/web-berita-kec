<?php

namespace App\Filament\Resources\NewsResource\Pages;

use App\Filament\Resources\NewsResource;
use App\Models\News;
use App\Models\NewsContent;
use App\Services\CloudinaryService;
use App\Helpers\ExcerptHelper;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class CreateNews extends CreateRecord
{
    protected static string $resource = NewsResource::class;

    // Override method untuk menangani pembuatan record news dan news_content secara bersamaan
    protected function handleRecordCreation(array $data): Model
    {
        // Panggil CloudinaryService
        $cloudinary = app(CloudinaryService::class);

        // Ambil path file thumbnail dari data form
        $thumbnailPath = $data['thumbnail'] ?? null;
        $thumbnailUrl = null;

        // slug
        $slug = Str::slug($data['title']);

        // Simpan file temp ke local dan up ke cloudinary lalu hapus file temp
        if ($thumbnailPath) {
            $fullPath = Storage::disk('local')->path($thumbnailPath);
            $thumbnailUrl = $cloudinary->upload($fullPath, 'news/thumbnails');
            Storage::disk('local')->delete($thumbnailPath);
        }


        // Record news dengan author_id yang diambil dari user yang sedang login
        $news = News::create([
            'author_id' => Auth::id(),
            'slug' => $slug,
        ]);

        // Simpan data is_published
        $isPublished = $data['is_published'] ?? false;
        $excerpt = ExcerptHelper::generate($data['content'], 200);

        // Buat record konten beritanya
        $newsContent = NewsContent::create([
            'news_id' => $news->id,
            'title' => $data['title'],
            'thumbnail' => $thumbnailUrl,
            'content' => $data['content'],
            'excerpt' => $excerpt,
            'version' => 1,
            'is_published' => $isPublished,
            'published_at' => $isPublished ? now() : null,
        ]);

        // Jika berita langsung dipublikasi, gunakan helper terpusat
        if ($isPublished) {
            $newsContent->publish();
        }

        // Sinkronisasi kategori jika ada
        if (!empty($data['categories'])) {
            $news->categories()->sync($data['categories']);
        }

        return $newsContent;
    }

    // Override method untuk optimasi gambar content berita sebelum create data
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['content'] = app(CloudinaryService::class)
            ->optimizeContentImages($data['content']);

        return $data;
    }

    // Redirect setelah berhasil
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

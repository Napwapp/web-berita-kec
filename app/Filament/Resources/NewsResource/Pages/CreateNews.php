<?php

namespace App\Filament\Resources\NewsResource\Pages;

use App\Filament\Resources\NewsResource;
use App\Models\News;
use App\Models\NewsContent;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CreateNews extends CreateRecord
{
    protected static string $resource = NewsResource::class;

    // Override method untuk menangani pembuatan record news dan news_content secara bersamaan
    protected function handleRecordCreation(array $data): Model
    {
        // Record news dengan author_id yang diambil dari user yang sedang login
        $news = News::create([
            'author_id' => Auth::id(),
        ]);

        // Simpan data is_published
        $isPublished = $data['is_published'] ?? false;

        // Buat record konten beritanya
        $newsContent = NewsContent::create([
            'news_id' => $news->id,
            'title' => $data['title'],
            'subtitle' => $data['subtitle'] ?? null,
            'slug' => Str::slug($data['title']),
            'thumbnail' => $data['thumbnail'],
            'thumbnail_description' => $data['thumbnail_description'] ?? null,
            'excerpt' => $data['excerpt'] ?? null,
            'content' => $data['content'],
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

    // Redirect setelah berhasil
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

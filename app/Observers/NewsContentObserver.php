<?php

namespace App\Observers;

use App\Models\NewsContent;
use App\Services\CloudinaryService;

class NewsContentObserver
{
    public function __construct(private CloudinaryService $cloudinary)
    {
    }

    public function created(NewsContent $newsContent): void
    {
        //
    }

    public function updated(NewsContent $newsContent): void
    {
        //
    }
    
    public function deleting(NewsContent $newsContent): void
    {
        // Hapus thumbnail
        $this->cloudinary->deleteByUrl($newsContent->thumbnail);

        // Hapus semua gambar di dalam content
        $this->cloudinary->deleteContentImages($newsContent->content);
    }

    public function deleted(NewsContent $newsContent): void
    {
        //
    }

    public function restored(NewsContent $newsContent): void
    {
        //
    }

    public function forceDeleted(NewsContent $newsContent): void
    {
        //
    }


}

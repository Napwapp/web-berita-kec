<?php
namespace App\Services;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class CloudinaryService
{
    /**
     * Upload file ke Cloudinary dan kembalikan full URL-nya.
     *
     * @param  \Illuminate\Http\UploadedFile|string  $file  — UploadedFile atau path sementara
     * @param  string  $folder  — folder tujuan di Cloudinary
     * @return string URL gambar
     */

    // jika $file berupa string, anggap itu adalah path sementara (misalnya dari Livewire temporary file upload)
    public function upload($file, string $folder = 'general', ?string $publicId = null): string
    {
        // file path sementara jika bukan, adalah UploadedFile
        $path = is_string($file) ? $file : $file->getRealPath();

        $options = [
            'folder' => $folder,
            'resource_type' => 'image',
        ];

        // Jika publicId ada, gunakan. jika tidak, pakai nama file dari path (tanpa ekstensi)
        if ($publicId) {
            $options['public_id'] = $publicId;
        } else {
            // Alternatif: ambil dari nama file asli (jika path berisi nama)
            $options['public_id'] = pathinfo($path, PATHINFO_FILENAME);
        }

        $result = Cloudinary::uploadApi()->upload($path, $options);
        return $result['secure_url'] ?? $result['url'] ?? '';
    }


    /**
     * Method hapus gambar dari Cloudinary berdasarkan full URL-nya.
     * @param  string|null  $url  Full URL gambar Cloudinary
     */
    public function deleteByUrl(?string $url): void
    {
        if (!$url) return;

        // Skip URL yang mengandung transformation parameter
        if (!preg_match('/\/upload\/v\d+\//', $url)) {
            return;
        }

        // Ekstrak public_id dari URL
        $pattern = '/\/upload\/(?:v\d+\/)?(.+?)(?:\.[a-zA-Z]+)?$/';

        // Jika cocok, hapus gambar menggunakan public_id
        if (preg_match($pattern, $url, $matches)) {
            $publicId = $matches[1];
            Cloudinary::uploadApi()->destroy($publicId);
        }
    }

    // Method hapus data di cloudinarynya dari konten gambar richeditor 
    /**
     * @param string|null $content
     */
    public function deleteContentImages(?string $content): void
    {
        if (!$content)
            return;

        // Decode HTML entities dulu (&quot; -> ", &amp; -> &, dst)
        $decoded = html_entity_decode($content, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Cari semua URL gambar Cloudinary di dalam content
        $cloudinaryDomain = 'res.cloudinary.com';
        $pattern = '/https?:\/\/' . preg_quote($cloudinaryDomain, '/') . '\/[^\s"\'<>]+/';
        preg_match_all($pattern, $decoded, $matches);
        if (empty($matches[0]))
            return;

        // Hapus duplikat
        $urls = array_unique($matches[0]);

        foreach ($urls as $url) {
            $this->deleteByUrl($url);
        }
    }

    // Method optimize gambar pada konten berita
    public function optimizeContentImageUrl(string $url): string
    {
        // Hanya proses URL cloudinary
        if (!str_contains($url, 'res.cloudinary.com')) {
            return $url;
        }

        // Transform!
        // f_auto   : format otomatis (WebP/AVIF tergantung browser)
        // q_auto   : kualitas otomatis (Cloudinary tentukan optimal)
        // w_1200   : max width 1200px (cukup untuk konten artikel)
        // c_limit  : hanya resize jika lebih besar (tidak upscale)
        $transformation = 'f_auto,q_auto,w_1200,c_limit';

        // Insert transformation setelah /upload/
        return preg_replace(
            '/\/upload\/(?:v\d+\/)?/',
            '/upload/' . $transformation . '/',
            $url,
            1
        );
    }
    // Optimize semua gambar di dalam konten berita
    public function optimizeContentImages(?string $content): ?string
    {
        if (!$content)
            return $content;

        // Cari semua url cloudinary didalam content
        $cloudinaryDomain = 'res.cloudinary.com';
        $pattern = '/(https?:\/\/' . preg_quote($cloudinaryDomain, '/') . '\/[^\s"\']+)/';

        // Replace URL dengan versi yang sudah dioptimasi
        return preg_replace_callback($pattern, function ($matches) {
            return $this->optimizeContentImageUrl($matches[1]);
        }, $content);
    }
}

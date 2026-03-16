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
        // Jika tidak ada url, tidak perlu melakukan apa-apa
        if (!$url)
            return;

        // Extract public_id dari full URL
        // Contoh URL: https://res.cloudinary.com/cloud_name/image/upload/v123456/news/thumbnails/abc.jpg
        // Public ID  : news/thumbnails/abc
        $pattern = '/\/upload\/(?:v\d+\/)?(.+?)(?:\.[a-zA-Z]+)?$/';

        // Jika URL cocok dengan pola, hapus gambar menggunakan public_id
        if (preg_match($pattern, $url, $matches)) {
            $publicId = $matches[1];
            Cloudinary::uploadApi()->destroy($publicId);
        }
    }
}
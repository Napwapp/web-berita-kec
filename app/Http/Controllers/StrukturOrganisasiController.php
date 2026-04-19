<?php

namespace App\Http\Controllers;

use App\Models\StrukturOrganisasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StrukturOrganisasiController extends Controller
{
    // Private variable
    private const DISK = 'public';
    private const FOLDER = 'images/struktur-organisasi';
    private const MAX_SIZE_MB = 5;
    private const ALLOWED_MIMES = ['jpg', 'jpeg', 'png', 'webp', 'svg'];

    public function index()
    {
        $data = StrukturOrganisasi::first();

        return view('struktur-organisasi.index', [
            'gambar' => $data->gambar ?? null,
            'updated_at' => $data->updated_at ?? null,
        ]);
    }

    // Method store (upload or update)
    public function store(Request $request)
    {
        $request->validate([
            'gambar' => [
                'required',
                'image',
                'mimes:' . implode(',', self::ALLOWED_MIMES),
                'max:' . (self::MAX_SIZE_MB * 1024),
            ],
        ], $this->validationMessages());

        // Cek jika sudah ada data, jika ada hapus file lama
        $existing = StrukturOrganisasi::first();
        
        if ($existing && $existing->gambar && Storage::disk(self::DISK)->exists($existing->gambar)) {
            Storage::disk(self::DISK)->delete($existing->gambar);
        }

        // Ambil nama asli file, buang ekstensi
        $originalName = pathinfo($request->file('gambar')->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $request->file('gambar')->getClientOriginalExtension();

        // Sanitize: huruf kecil, spasi jadi strip, buang karakter aneh
        $safeName = Str::slug($originalName);

        // Tambah timestamp supaya tidak bentrok jika nama sama
        $fileName = $safeName . '_' . now()->format('Ymd_His') . '.' . $extension;

        $path = $request->file('gambar')->storeAs(self::FOLDER, $fileName, self::DISK);

        StrukturOrganisasi::updateOrCreate(
            ['id' => 1],
            ['gambar' => $path]
        );

        $message = $existing
            ? 'Gambar struktur organisasi berhasil diperbarui.'
            : 'Gambar struktur organisasi berhasil diupload.';

        return redirect()->route('struktur-organisasi')->with('success', $message);
    }

    // Method delete untuk menghapus data dan file gambar
    public function destroy()
    {
        $data = StrukturOrganisasi::firstOrFail();

        // Hapus file dari storage
        if ($data->gambar && Storage::disk(self::DISK)->exists($data->gambar)) {
            Storage::disk(self::DISK)->delete($data->gambar);
        }

        $data->delete();

        return redirect()->route('struktur-organisasi')
            ->with('success', 'Gambar struktur organisasi berhasil dihapus.');
    }

    // Pesan Validasi
    private function validationMessages(): array
    {
        $mimes = strtoupper(implode(', ', self::ALLOWED_MIMES));
        $maxSize = self::MAX_SIZE_MB . ' MB';

        return [
            'gambar.required' => 'Gambar struktur organisasi wajib diisi.',
            'gambar.image' => 'File yang diupload harus berupa gambar.',
            'gambar.mimes' => "Format gambar yang didukung: {$mimes}.",
            'gambar.max' => "Ukuran gambar maksimal {$maxSize}.",
        ];
    }
}
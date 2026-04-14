<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    private const DISK = 'public';
    private const FOTO_DIR = 'images/profile-pictures';

    public function index()
    {
        return view('profile.index');
    }

    // Update profile
    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $request->validate([
            'name' => ['required', 'string', 'max:100'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.max' => 'Nama maksimal 100 karakter.',
        ]);

        $user->update([
            'name' => $request->name,
        ]);

        return redirect()->route('profile.index')->with('success', 'Profil berhasil diperbarui.');
    }

    // Update password
    public function updatePassword(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ], [
            'password.required' => 'Password baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'redirect' => route('profile.index'),
            'message' => 'Password berhasil diperbarui.'
        ]);
    }

    // Update foto profile
    public function updateFoto(Request $request)
    {
        $request->validate([
            'foto' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'foto.required' => 'Foto wajib dipilih.',
            'foto.image' => 'File harus berupa gambar.',
            'foto.mimes' => 'Format foto yang didukung: JPG, JPEG, PNG, WEBP.',
            'foto.max' => 'Ukuran foto maksimal 2 MB.',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Hapus foto lama dari storage hanya jika bukan URL eksternal (Google, dll)
        if (
            $user->profile_picture &&
            !str_starts_with($user->profile_picture, 'http') &&
            Storage::disk(self::DISK)->exists($user->profile_picture)
        ) {
            Storage::disk(self::DISK)->delete($user->profile_picture);
        }

        // Simpan foto baru ke storage
        $originalName = pathinfo($request->file('foto')->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $request->file('foto')->getClientOriginalExtension();
        $fileName = Str::slug($originalName) . '_' . now()->format('Ymd_His') . '.' . $extension;
        $path = $request->file('foto')->storeAs(self::FOTO_DIR, $fileName, self::DISK);

        $user->update(['profile_picture' => $path]);

        return redirect()->route('profile.index')->with('success', 'Foto profil berhasil diperbarui.');
    }

    // Hapus foto profil
    public function hapusFoto()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->profile_picture) {
            return redirect()->route('profile.index')->with('error', 'Tidak ada foto untuk dihapus.');
        }

        // Hapus dari storage HANYA jika foto lokal, bukan URL Google
        if (
            !str_starts_with($user->profile_picture, 'http') &&
            Storage::disk(self::DISK)->exists($user->profile_picture)
        ) {
            Storage::disk(self::DISK)->delete($user->profile_picture);
        }

        $user->update(['profile_picture' => null]);

        return redirect()->route('profile.index')->with('success', 'Foto profil berhasil dihapus.');
    }

}
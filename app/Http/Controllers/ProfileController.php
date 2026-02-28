<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage; // WAJIB ditambahkan untuk hapus file lama
use App\Models\User;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        return view('profile', compact('user'));
    }

    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 1. Tambahkan validasi untuk avatar (gambar, format tertentu, max 2MB)
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'no_telp' => ['nullable', 'string', 'max:20'],
            'email' => ['required', 'email', 'unique:users,email,' . $user->id],
            'nama_usaha'     => ['nullable', 'string', 'max:25'],
            'kategori_usaha' => ['nullable', 'string', 'max:50'],
            'password' => ['nullable', 'confirmed', 'min:8'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        // 2. Logika Upload Foto Profil
        if ($request->hasFile('avatar')) {
            // Jika user sebelumnya sudah punya foto profil, hapus foto lama agar server tidak penuh
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Simpan foto baru ke dalam folder storage/app/public/avatars
            $path = $request->file('avatar')->store('avatars', 'public');

            // Simpan path lokasi file ke properti user
            $user->avatar = $path;
        }

        // 3. Update data lainnya seperti biasa
        $user->name = $data['name'];
        $user->no_telp = $data['no_telp'] ?? null;
        $user->email = $data['email'];
        $user->nama_usaha = $data['nama_usaha'] ?? null;
        $user->kategori_usaha = $data['kategori_usaha'] ?? null;

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return back()->with('status', 'Profile updated successfully.');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class ProfileController extends Controller
{
    public function show()
    {
        return view('profile', ['user' => Auth::user()]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ];

        if (! $user->isAdmin()) {
            $rules += [
                'phone' => 'nullable|string|max:20',
                'nama_usaha' => 'required|string|max:200',
                'kategori_usaha' => 'required|string|in:' . implode(',', User::KATEGORI_USAHA),
            ];
        }

        $request->validate($rules, [
            'nama_usaha.required' => 'Nama usaha wajib diisi.',
            'nama_usaha.max' => 'Nama usaha maksimal 200 karakter.',
            'kategori_usaha.required' => 'Kategori usaha wajib dipilih.',
            'kategori_usaha.in' => 'Kategori usaha tidak valid.',
        ]);

        $fields = $user->isAdmin()
            ? ['name', 'email']
            : ['name', 'phone', 'nama_usaha', 'kategori_usaha', 'email'];

        $user->update($request->only($fields));

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        Auth::user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password berhasil diubah.');
    }

    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user = Auth::user();

        if ($user->photo && Storage::disk('public')->exists($user->photo)) {
            Storage::disk('public')->delete($user->photo);
        }

        $path = $request->file('photo')->store('photos', 'public');
        $user->update(['photo' => $path]);

        return back()->with('success', 'Foto profil berhasil diubah.');
    }
}

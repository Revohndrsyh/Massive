@extends('layouts.app')
@section('title', 'Profile')
@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8 animate-fade-in-up">
        <h1 class="text-2xl font-bold text-gray-900">Profil Saya</h1>
        <p class="text-gray-500 mt-1">Kelola informasi profil dan keamanan akun Anda.</p>
    </div>

    <div class="grid md:grid-cols-3 gap-6">
        {{-- Left Column: Avatar & Info --}}
        <div class="animate-fade-in-up">
            <div class="card-static p-6 text-center">
                <div class="relative inline-block mb-4">
                    <div class="w-28 h-28 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white text-3xl font-bold shadow-lg overflow-hidden">
                        @if($user->photo)
                        <img src="{{ asset('storage/' . $user->photo) }}" alt="Photo" class="w-full h-full object-cover">
                        @else
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                        @endif
                    </div>
                </div>
                <h3 class="text-lg font-bold text-gray-900">{{ $user->name }}</h3>
                @if(!$user->isAdmin() && $user->nama_usaha)
                <p class="text-sm text-gray-600 mt-0.5">{{ $user->nama_usaha }}</p>
                @endif
                @if(!$user->isAdmin() && $user->kategori_usaha)
                <span class="inline-flex items-center gap-1 mt-2 px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                    {{ $user->kategori_usaha }}
                </span>
                @elseif($user->isAdmin())
                <p class="text-sm text-gray-500">Administrator</p>
                @else
                <p class="text-sm text-gray-500">Pemilik UMKM</p>
                @endif

                @unless($user->isAdmin())
                <form method="POST" action="{{ route('profile.photo') }}" enctype="multipart/form-data" class="mt-4">
                    @csrf
                    <label class="text-sm text-blue-600 hover:text-blue-700 font-medium cursor-pointer">
                        Ganti Foto Profil
                        <input type="file" name="photo" accept="image/*" class="hidden" onchange="this.form.submit()">
                    </label>
                </form>
                @endunless

                <div class="mt-6 pt-6 border-t border-gray-100">
                    <div class="flex items-center gap-3 px-3 py-2.5 rounded-lg bg-blue-50 text-blue-700 text-sm font-medium">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Edit Profil
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column: Forms --}}
        <div class="md:col-span-2 space-y-6">
            {{-- Personal Info Form --}}
            <div class="card-static p-6 animate-fade-in-up">
                <h2 class="text-lg font-bold text-gray-900 mb-1">Informasi Pribadi</h2>
                <p class="text-sm text-gray-500 mb-6">Perbarui informasi profil dan alamat email Anda.</p>

                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Lengkap</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                </div>
                                <input type="text" name="name" value="{{ $user->name }}" class="form-input form-input-icon" required>
                            </div>
                        </div>

                        @unless($user->isAdmin())
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Nomor Telepon</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                </div>
                                <input type="text" name="phone" value="{{ $user->phone }}" class="form-input form-input-icon">
                            </div>
                        </div>
                        @endunless

                        @unless($user->isAdmin())
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Usaha</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                </div>
                                <input type="text" name="nama_usaha" value="{{ $user->nama_usaha }}" class="form-input form-input-icon" placeholder="Contoh: Warung Bu Siti" required maxlength="200">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Kategori Usaha</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                </div>
                                <select name="kategori_usaha" class="form-input form-input-icon appearance-none pr-10" required>
                                    <option value="" disabled {{ $user->kategori_usaha ? '' : 'selected' }}>-- Pilih Kategori Usaha --</option>
                                    @foreach(\App\Models\User::KATEGORI_USAHA as $kategori)
                                    <option value="{{ $kategori }}" {{ $user->kategori_usaha === $kategori ? 'selected' : '' }}>{{ $kategori }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </div>
                            </div>
                        </div>
                        @endunless

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </div>
                                <input type="email" name="email" value="{{ $user->email }}" class="form-input form-input-icon" required>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-100">
                        <a href="{{ route('dashboard') }}" class="btn-outline text-sm py-2 px-4">Batal</a>
                        <button type="submit" class="btn-primary text-sm py-2 px-4 inline-flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

            {{-- Change Password Form --}}
            <div class="card-static p-6 animate-fade-in-up">
                <h2 class="text-lg font-bold text-gray-900 mb-1">Ganti Password</h2>
                <p class="text-sm text-gray-500 mb-6">Pastikan menggunakan password yang kuat.</p>

                <form method="POST" action="{{ route('profile.password') }}">
                    @csrf
                    @method('PUT')

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Password Baru</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                </div>
                                <input type="password" name="password" class="form-input form-input-icon" placeholder="Password baru" required>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Konfirmasi Password</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                </div>
                                <input type="password" name="password_confirmation" class="form-input form-input-icon" placeholder="Ulangi password baru" required>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end mt-6 pt-6 border-t border-gray-100">
                        <button type="submit" class="btn-primary text-sm py-2 px-4">Ubah Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')
@section('title', 'Modul Pembelajaran')
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
    <div class="mb-6 sm:mb-8 animate-fade-in-up">
        <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-900">Modul Pembelajaran</h1>
        <p class="text-gray-500 mt-1 text-sm sm:text-base">Tingkatkan kemampuan bisnis Anda melalui video pembelajaran interaktif.</p>
    </div>

    @php
        $categories = collect($modules)->pluck('kategori')->unique()->values();
        $categoryColors = ['Operasional' => 'bg-blue-100 text-blue-700', 'Pemasaran' => 'bg-green-100 text-green-700', 'Keuangan' => 'bg-yellow-100 text-yellow-700', 'Teknologi' => 'bg-purple-100 text-purple-700'];
    @endphp

    {{-- Banner for users who haven't filled kuesioner --}}
    @if(!$hasKuesioner)
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 animate-fade-in-up">
        <div class="flex items-center gap-3">
            <span class="text-2xl">💡</span>
            <div>
                <p class="text-sm font-medium text-blue-800">Isi kuesioner bisnis untuk mendapatkan rekomendasi modul yang personal untukmu!</p>
            </div>
        </div>
        <a href="{{ route('kuesioner', ['step' => 1]) }}" class="btn-primary text-sm py-2 px-4 inline-flex items-center gap-1 shrink-0 min-h-[40px] justify-center">
            Isi Kuesioner →
        </a>
    </div>
    @endif

    {{-- Recommended for You --}}
    @if(!empty($rekomendasiModul) && count($rekomendasiModul) > 0)
    <section class="mb-8 animate-fade-in-up">
        <div class="flex items-center gap-2 mb-4">
            <span class="text-xl">⭐</span>
            <h2 class="text-lg font-bold text-gray-800">Direkomendasikan untuk Anda</h2>
            <span class="text-xs bg-blue-100 text-blue-600 px-2 py-1 rounded-full font-medium">Berdasarkan hasil kuesioner</span>
        </div>
        <p class="text-sm text-gray-500 mb-4">Modul berikut dipilih khusus berdasarkan aspek bisnis yang perlu Anda tingkatkan.</p>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 stagger-children">
            @foreach($rekomendasiModul as $mod)
            <a href="{{ route('modul.show', $mod['slug']) }}" class="bg-white rounded-xl shadow-sm border border-blue-100 overflow-hidden hover:shadow-md transition group block">
                <div class="relative aspect-video bg-gray-200 overflow-hidden">
                    <img src="{{ $mod['thumbnail'] }}" alt="{{ $mod['judul'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy" onerror="this.style.display='none'">
                    <div class="absolute top-2 left-2">
                        <span class="bg-blue-600 text-white text-xs px-2 py-1 rounded-full font-semibold">⭐ Untuk Anda</span>
                    </div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="w-12 h-12 bg-white/80 rounded-full flex items-center justify-center opacity-80 group-hover:opacity-100 group-hover:scale-110 transition-all">
                            <svg class="w-5 h-5 text-blue-600 ml-0.5" fill="currentColor" viewBox="0 0 20 20"><path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z"/></svg>
                        </div>
                    </div>

                </div>
                <div class="p-4">
                    <p class="text-xs text-blue-600 font-medium mb-1">📌 {{ $mod['alasan'] ?? 'Relevan dengan kondisi bisnis Anda' }}</p>
                    <h3 class="font-semibold text-gray-800 text-sm group-hover:text-blue-600 transition-colors">{{ $mod['judul'] }}</h3>
                    <div class="flex items-center justify-between mt-3">
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $categoryColors[$mod['kategori']] ?? 'bg-gray-100 text-gray-700' }}">{{ $mod['kategori'] }}</span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </section>
    <hr class="mb-6 border-gray-200">
    @endif

    {{-- All Modules --}}
    <h2 class="text-lg font-bold text-gray-900 mb-4">Semua Modul</h2>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4 lg:gap-6 stagger-children">
        @foreach($modules as $mod)
        <a href="{{ route('modul.show', $mod['slug']) }}" class="card overflow-hidden group">
            <div class="relative aspect-video bg-gray-200 overflow-hidden">
                <img src="{{ $mod['thumbnail'] }}" alt="{{ $mod['judul'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy" onerror="this.style.display='none'">
                <div class="absolute inset-0 bg-black/20 flex items-center justify-center">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-white/80 flex items-center justify-center opacity-80 group-hover:opacity-100 group-hover:scale-110 transition-all">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-blue-600 ml-0.5" fill="currentColor" viewBox="0 0 20 20"><path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z"/></svg>
                    </div>
                </div>

            </div>
            <div class="p-3 sm:p-4">
                <span class="text-[10px] sm:text-xs font-semibold px-1.5 sm:px-2 py-0.5 rounded-full {{ $categoryColors[$mod['kategori']] ?? 'bg-gray-100 text-gray-700' }}">{{ $mod['kategori'] }}</span>
                <h3 class="font-semibold text-gray-900 text-xs sm:text-sm mt-2 line-clamp-2 group-hover:text-blue-600 transition-colors">{{ $mod['judul'] }}</h3>
            </div>
        </a>
        @endforeach
    </div>
</div>
@endsection

@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
    <div class="mb-6 sm:mb-8 animate-fade-in-up">
        <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-900">Selamat Datang Kembali, {{ $user->name }}!</h1>
        <p class="text-gray-500 mt-1 text-sm sm:text-base">Berikut ringkasan analisis untuk bisnis Anda.</p>
    </div>

    @if(!$hasKuesioner)
    {{-- Empty State --}}
    <div class="card-static p-6 sm:p-8 mb-6 animate-fade-in-up border-l-4 border-blue-500">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-lg sm:text-xl font-bold text-gray-900 flex items-center gap-2">
                    <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Kuesioner Anda
                </h2>
                <p class="text-gray-600 mt-1 text-sm">Mohon isi kuesioner untuk mendapatkan analisis yang lebih akurat.</p>
            </div>
            <a href="{{ route('kuesioner', ['step' => 1]) }}" class="btn-primary inline-flex items-center gap-2 shrink-0 min-h-[44px] justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Mulai Isi
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 mb-6">
        <div class="card-static p-6 sm:p-8 text-center">
            <div class="w-16 h-16 sm:w-20 sm:h-20 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                <svg class="w-8 h-8 sm:w-10 sm:h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/></svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-400">Skor Sentimen</h3>
            <p class="text-sm text-gray-400 mt-1">Isi kuesioner untuk melihat skor kondisi bisnis Anda</p>
        </div>
        <div class="card-static p-6 sm:p-8 text-center">
            <div class="w-16 h-16 sm:w-20 sm:h-20 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                <svg class="w-8 h-8 sm:w-10 sm:h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-400">Tingkat Kondisi Bisnis</h3>
            <p class="text-sm text-gray-400 mt-1">Data akan muncul setelah pengisian kuesioner</p>
        </div>
    </div>

    {{-- Mengapa MASSIVE --}}
    <h2 class="text-lg sm:text-xl font-bold text-gray-900 mb-4">Mengapa MASSIVE?</h2>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">
        <div class="card p-5 sm:p-6 text-center group">
            <div class="w-12 h-12 mx-auto mb-4 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </div>
            <h3 class="font-bold text-gray-900 mb-2">Analisis Sentimen Bisnis</h3>
            <p class="text-sm text-gray-500">Ketahui kondisi bisnis Anda berdasarkan jawaban kuesioner yang dianalisis oleh 3 algoritma ML.</p>
        </div>
        <div class="card p-5 sm:p-6 text-center group">
            <div class="w-12 h-12 mx-auto mb-4 rounded-xl bg-green-100 text-green-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
            </div>
            <h3 class="font-bold text-gray-900 mb-2">Visualisasi Knowledge Graph</h3>
            <p class="text-sm text-gray-500">Lihat hubungan antar isu bisnis Anda dalam bentuk graf interaktif yang mudah dipahami.</p>
        </div>
        <div class="card p-5 sm:p-6 text-center group">
            <div class="w-12 h-12 mx-auto mb-4 rounded-xl bg-yellow-100 text-yellow-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
            </div>
            <h3 class="font-bold text-gray-900 mb-2">Rekomendasi dari AI</h3>
            <p class="text-sm text-gray-500">Dapatkan saran strategis dari Gemini AI untuk mengatasi setiap isu bisnis yang teridentifikasi.</p>
        </div>
    </div>

    @else
    {{-- Filled State --}}
    <div class="card-static p-4 sm:p-6 mb-6 animate-fade-in-up border-l-4 border-green-500">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-base sm:text-lg font-bold text-gray-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    Kuesioner Terisi
                </h2>
                <p class="text-sm text-gray-500">Terakhir diisi: {{ $latestKuesioner->updated_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB</p>
            </div>
            <div class="flex gap-2 sm:gap-3">
                <a href="{{ route('analisis') }}" class="btn-outline text-sm py-2 px-3 sm:px-4 min-h-[40px] inline-flex items-center">Lihat Analisis</a>
                <a href="{{ route('kuesioner', ['step' => 1]) }}" class="btn-primary text-sm py-2 px-3 sm:px-4 min-h-[40px] inline-flex items-center">Isi Ulang</a>
            </div>
        </div>
    </div>

    {{-- SUS Evaluation --}}
    @if($hasFilledSUS)
    {{-- Already filled — thank you message --}}
    <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-6 flex items-center gap-3 animate-fade-in-up">
        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        </div>
        <p class="text-sm font-medium text-green-800">Terima kasih telah mengisi evaluasi kepuasan penggunaan sistem!</p>
    </div>
    @else
    {{-- Not filled yet — show prompt --}}
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 animate-fade-in-up">
        <div class="flex items-center gap-3">
            <span class="text-2xl">📋</span>
            <div>
                <p class="text-sm font-medium text-blue-800">Bantu kami meningkatkan MASSIVE!</p>
                <p class="text-xs text-blue-600">Isi evaluasi kepuasan penggunaan sistem (10 pertanyaan singkat).</p>
            </div>
        </div>
        <button onclick="document.getElementById('sus-modal')?.classList.remove('hidden')" class="btn-primary text-sm py-2 px-4 inline-flex items-center gap-1 min-h-[40px] justify-center cursor-pointer shrink-0">
            Isi Evaluasi →
        </button>
    </div>
    @endif

    <h2 class="text-lg sm:text-xl font-bold text-gray-900 mb-4">Ringkasan Analisis Bisnis Anda</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 mb-6 sm:mb-8 stagger-children">
        {{-- Sentiment Score --}}
        @php
            // Normalisasi nilai sentimen: mendukung data lama biner (Positive/Negative)
            // maupun data baru 3 kelas (Positif/Netral/Negatif)
            $sentRaw = strtolower((string) $sentimen);
            if (in_array($sentRaw, ['positif', 'positive'])) {
                $sentKey = 'Positif';
            } elseif (in_array($sentRaw, ['negatif', 'negative'])) {
                $sentKey = 'Negatif';
            } else {
                $sentKey = 'Netral';
            }
            $sentStyleMap = [
                'Positif' => ['border' => 'border-green-500', 'numText' => 'text-green-500', 'badge' => 'bg-green-100 text-green-700', 'dot' => 'bg-green-500', 'msgClr' => 'text-green-600', 'label' => '● Positif', 'msg' => 'Kondisi bisnis Anda sangat baik!', 'gauge' => '#22c55e'],
                'Netral'  => ['border' => 'border-yellow-500', 'numText' => 'text-yellow-500', 'badge' => 'bg-yellow-100 text-yellow-700', 'dot' => 'bg-yellow-500', 'msgClr' => 'text-yellow-600', 'label' => '● Netral', 'msg' => 'Kondisi bisnis Anda cukup, masih ada beberapa area yang dapat ditingkatkan.', 'gauge' => '#eab308'],
                'Negatif' => ['border' => 'border-red-500', 'numText' => 'text-red-500', 'badge' => 'bg-red-100 text-red-700', 'dot' => 'bg-red-500', 'msgClr' => 'text-red-500', 'label' => '● Negatif', 'msg' => 'Ada beberapa area bisnis yang perlu diperbaiki.', 'gauge' => '#ef4444'],
            ];
            $ss = $sentStyleMap[$sentKey];
        @endphp
        <div class="card-static p-4 sm:p-6 border-l-4 {{ $ss['border'] }}">
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Skor Sentimen</h3>
            <div class="flex items-center gap-4 sm:gap-6">
                <div class="relative flex-shrink-0">
                    <canvas id="sentimenGauge" width="120" height="120"></canvas>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="text-center">
                            <span class="text-2xl sm:text-3xl font-extrabold {{ $ss['numText'] }}">{{ number_format($skorSentimen, 1) }}</span>
                            <span class="text-sm text-gray-400 block">/5</span>
                        </div>
                    </div>
                </div>
                <div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-semibold {{ $ss['badge'] }}">
                        <span class="w-2 h-2 rounded-full {{ $ss['dot'] }}"></span>
                        {{ $ss['label'] }}
                    </span>
                    <p class="text-sm mt-2 {{ $ss['msgClr'] }}">
                        {{ $ss['msg'] }}
                    </p>
                    <p class="text-xs text-gray-400 mt-1">Confidence: {{ number_format($confidence * 100, 0) }}%</p>
                </div>
            </div>
        </div>

        {{-- Satisfaction Donut --}}
        <div class="card-static p-4 sm:p-6">
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Tingkat Kondisi Bisnis</h3>
            <div class="flex items-center gap-4 sm:gap-6">
                <div class="flex-shrink-0">
                    <canvas id="kepuasanDonut" width="120" height="120"></canvas>
                </div>
                <div class="space-y-2.5">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-green-500"></span>
                        <span class="text-sm text-gray-600">Kondisi Baik: <strong>{{ $kepuasan['sangat_puas'] }}%</strong></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-yellow-400"></span>
                        <span class="text-sm text-gray-600">Cukup: <strong>{{ $kepuasan['netral'] }}%</strong></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-red-500"></span>
                        <span class="text-sm text-gray-600">Perlu Perhatian: <strong>{{ $kepuasan['tidak_puas'] }}%</strong></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Knowledge Graph --}}
    <div class="card-static p-4 sm:p-6 mb-6 sm:mb-8 animate-fade-in-up">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
            <div>
                <h2 class="text-lg sm:text-xl font-bold text-gray-900">Knowledge Graph — Semantic Web</h2>
                <p class="text-sm text-gray-500 mt-0.5">Klik node topik untuk melihat detail opini per aspek</p>
            </div>
            <div class="flex gap-2 flex-wrap">
                <span class="inline-flex items-center gap-1 text-xs px-2 py-1 rounded-full bg-[#1e293b] text-white">● UMKM</span>
                <span class="inline-flex items-center gap-1 text-xs px-2 py-1 rounded-full bg-blue-500 text-white">● Operasional</span>
                <span class="inline-flex items-center gap-1 text-xs px-2 py-1 rounded-full bg-purple-500 text-white">● Pemasaran</span>
                <span class="inline-flex items-center gap-1 text-xs px-2 py-1 rounded-full bg-yellow-500 text-white">● Keuangan</span>
                <span class="inline-flex items-center gap-1 text-xs px-2 py-1 rounded-full bg-green-500 text-white">● Teknologi</span>
                <span class="inline-flex items-center gap-1 text-xs px-2 py-1 rounded-full bg-red-500 text-white">● Tantangan</span>
                <span class="inline-flex items-center gap-1 text-xs px-2 py-1 rounded-full bg-red-100 text-red-800 border border-red-300">□ Isu</span>
            </div>
        </div>
        {{-- Interactive Similarity Filter — User dapat enable/disable per kategori --}}
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 mb-3">
            <div class="flex items-start justify-between mb-2 gap-2 flex-wrap">
                <div>
                    <p class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Filter Keterhubungan Antar Topik</p>
                    <p class="text-[10px] text-gray-500 mt-0.5">Klik tombol untuk menampilkan/menyembunyikan kategori similarity di graph</p>
                </div>
                <button type="button" id="reset-sim-filter" class="text-[10px] text-blue-600 hover:text-blue-700 font-medium underline">Reset Filter</button>
            </div>
            <div class="flex gap-2 flex-wrap">
                {{-- Toggle 1: Strong --}}
                <button type="button"
                        data-sim-filter="strong"
                        class="sim-filter-btn group inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border-2 border-green-500 bg-green-50 text-green-800 text-xs font-semibold hover:bg-green-100 transition shadow-sm">
                    <span class="inline-block w-8 h-0.5 bg-green-500"></span>
                    <span>🟢 Sangat Terkait</span>
                    <span class="text-[9px] text-green-600">(≥ 0.5)</span>
                    <span class="sim-filter-status text-green-600" title="aktif">✓</span>
                </button>

                {{-- Toggle 2: Medium --}}
                <button type="button"
                        data-sim-filter="medium"
                        class="sim-filter-btn group inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border-2 border-yellow-500 bg-yellow-50 text-yellow-800 text-xs font-semibold hover:bg-yellow-100 transition shadow-sm">
                    <span class="inline-block w-8 h-0.5 bg-yellow-500"></span>
                    <span>🟡 Cukup Terkait</span>
                    <span class="text-[9px] text-yellow-600">(0.2–0.5)</span>
                    <span class="sim-filter-status text-yellow-600" title="aktif">✓</span>
                </button>

                {{-- Toggle 3: Weak --}}
                <button type="button"
                        data-sim-filter="weak"
                        class="sim-filter-btn group inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border-2 border-slate-400 bg-slate-50 text-slate-700 text-xs font-semibold hover:bg-slate-100 transition shadow-sm">
                    <span class="inline-block w-8 h-0.5 bg-slate-400"></span>
                    <span>⚪ Sedikit Terkait</span>
                    <span class="text-[9px] text-slate-500">(&lt; 0.2)</span>
                    <span class="sim-filter-status text-slate-500" title="aktif">✓</span>
                </button>
            </div>
        </div>
        <div class="flex flex-col lg:flex-row gap-4">
            <div class="w-full lg:w-3/4">
                <div id="knowledge-graph" class="kg-container bg-gray-50 h-[350px] sm:h-[400px] lg:h-[550px] rounded-xl border border-gray-200"></div>
            </div>
            <div id="node-detail" class="w-full lg:w-1/4 card-static p-4 bg-gray-50 rounded-xl min-h-[200px] lg:min-h-[550px] overflow-y-auto">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Detail Topik</h3>
                <div id="node-detail-content">
                    <p class="text-sm text-gray-400 italic">Klik sebuah node pada graph untuk melihat detail opini.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Positive message below KG when no issues --}}
    @if(count($isuBisnis) === 0)
    <div class="bg-green-50 border border-green-200 rounded-xl p-4 mt-4 text-center">
        <p class="text-sm text-green-700 font-medium">✅ Semua aspek bisnis Anda dalam kondisi baik. Pertahankan performa ini!</p>
    </div>
    @endif

    {{-- Quick Links --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 stagger-children mt-6">
        <a href="{{ route('analisis') }}" class="card p-4 sm:p-5 flex items-center gap-4 group">
            <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center group-hover:scale-110 transition-transform flex-shrink-0">
                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </div>
            <div><h3 class="font-semibold text-gray-900 text-sm sm:text-base">Analisis Model</h3><p class="text-xs sm:text-sm text-gray-500">Lihat perbandingan performa ML</p></div>
        </a>
        @if(count($isuBisnis) > 0)
        <a href="{{ route('rekomendasi') }}" class="card p-4 sm:p-5 flex items-center gap-4 group">
            <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-green-100 text-green-600 flex items-center justify-center group-hover:scale-110 transition-transform flex-shrink-0">
                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
            </div>
            <div><h3 class="font-semibold text-gray-900 text-sm sm:text-base">Rekomendasi AI</h3><p class="text-xs sm:text-sm text-gray-500">Saran untuk isu bisnis Anda</p></div>
        </a>
        @else
        <div class="card-static p-4 sm:p-5 flex items-center gap-4">
            <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-green-100 text-green-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            </div>
            <div><h3 class="font-semibold text-green-700 text-sm sm:text-base">✓ Tidak ada isu kritis</h3><p class="text-xs sm:text-sm text-green-600">Bisnis Anda dalam kondisi baik!</p></div>
        </div>
        @endif
        <a href="{{ route('modul') }}" class="card p-4 sm:p-5 flex items-center gap-4 group">
            <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center group-hover:scale-110 transition-transform flex-shrink-0">
                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
            <div><h3 class="font-semibold text-gray-900 text-sm sm:text-base">Modul Materi</h3><p class="text-xs sm:text-sm text-gray-500">Pelajari strategi bisnis</p></div>
        </a>
    </div>
    @endif
</div>

{{-- SUS Modal --}}
@if($hasKuesioner)
<div id="sus-modal" class="hidden fixed inset-0 bg-black/60 z-[70] flex items-center justify-center px-4 py-6 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] flex flex-col overflow-hidden animate-fade-in-up">
        {{-- Header --}}
        <div class="px-6 py-4 border-b flex items-center justify-between flex-shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-blue-100 rounded-full flex items-center justify-center"><span class="text-lg">📋</span></div>
                <div>
                    <h2 class="font-bold text-gray-800 text-base">Evaluasi Penggunaan Sistem</h2>
                    <p class="text-xs text-gray-500">System Usability Scale (SUS)</p>
                </div>
            </div>
            <button onclick="closeSUS()" class="w-8 h-8 rounded-full hover:bg-gray-100 flex items-center justify-center text-gray-400 hover:text-gray-600 text-lg cursor-pointer border-none bg-transparent">✕</button>
        </div>
        {{-- Progress --}}
        <div class="px-6 py-3 bg-gray-50 border-b flex-shrink-0">
            <div class="flex items-center justify-between mb-1">
                <span class="text-xs text-gray-500">Progress pengisian</span>
                <span id="progress-text" class="text-xs font-semibold text-blue-600">0 dari 10 pertanyaan</span>
            </div>
            <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                <div id="progress-bar" class="h-full bg-blue-500 rounded-full transition-all duration-300" style="width:0%"></div>
            </div>
        </div>
        {{-- Scale legend --}}
        <div class="px-6 py-2 bg-blue-50 border-b flex-shrink-0">
            <div class="flex justify-between text-xs text-gray-500">
                <span>1 = Sangat Tidak Setuju</span><span>3 = Netral</span><span>5 = Sangat Setuju</span>
            </div>
        </div>
        {{-- Questions --}}
        @php
        $susQuestions = [
            'Saya berpikir akan menggunakan sistem ini lagi.',
            'Saya merasa sistem ini rumit untuk digunakan.',
            'Saya merasa sistem ini mudah digunakan.',
            'Saya membutuhkan bantuan dari orang lain atau teknisi dalam menggunakan sistem ini.',
            'Saya merasa fitur-fitur sistem ini berjalan dengan semestinya.',
            'Saya merasa ada banyak hal yang tidak konsisten dalam sistem ini.',
            'Saya merasa orang lain akan memahami cara menggunakan sistem ini dengan cepat.',
            'Saya merasa sistem ini membingungkan.',
            'Saya merasa tidak ada hambatan dalam menggunakan sistem ini.',
            'Saya perlu membiasakan diri terlebih dahulu sebelum menggunakan sistem ini.',
        ];
        @endphp
        <div class="overflow-y-auto flex-1 px-6 py-4 space-y-3">
            @foreach($susQuestions as $i => $q)
            <div class="border border-gray-200 rounded-xl p-4 transition-all duration-200 hover:border-blue-300 hover:bg-blue-50/30" id="sus-card-{{ $i+1 }}">
                <p class="text-sm font-medium text-gray-800 mb-4 leading-relaxed">{{ $i+1 }}. {{ $q }}</p>
                <div class="flex justify-between items-center px-2">
                    @for($s = 1; $s <= 5; $s++)
                    <label class="flex flex-col items-center gap-2 cursor-pointer group">
                        <input type="radio" name="sus_{{ $i+1 }}" value="{{ $s }}" class="cursor-pointer" style="width:20px;height:20px;accent-color:#2563eb" onchange="updateSUSProgress()">
                        <span class="text-sm font-semibold text-gray-600 group-hover:text-blue-600">{{ $s }}</span>
                    </label>
                    @endfor
                </div>
                <div class="flex justify-between mt-2 px-1">
                    <span style="font-size:10px;color:#9ca3af">Sangat Tidak Setuju</span>
                    <span style="font-size:10px;color:#9ca3af">Sangat Setuju</span>
                </div>
            </div>
            @endforeach
        </div>
        {{-- Footer --}}
        <div class="px-6 py-4 border-t bg-gray-50 flex gap-3 flex-shrink-0">
            <button onclick="closeSUS()" class="flex-1 py-2.5 border border-gray-300 rounded-xl text-sm text-gray-600 hover:bg-gray-100 font-medium transition cursor-pointer bg-white">Lewati</button>
            <button onclick="submitSUS()" class="flex-1 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-semibold hover:bg-blue-700 transition cursor-pointer border-none">Kirim Evaluasi</button>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
@if($hasKuesioner)

// Wrap Chart.js dalam try-catch supaya error chart tidak menghentikan KG
try {
    const gaugeColor = '{{ $ss['gauge'] }}';
    const gaugeEl = document.getElementById('sentimenGauge');
    if (gaugeEl && typeof Chart !== 'undefined') {
        new Chart(gaugeEl.getContext('2d'), {
            type: 'doughnut',
            data: { datasets: [{ data: [{{ $skorSentimen }}, 5 - {{ $skorSentimen }}], backgroundColor: [gaugeColor, '#f1f5f9'], borderWidth: 0, borderRadius: 8 }] },
            options: { cutout: '75%', responsive: false, plugins: { legend: { display: false }, tooltip: { enabled: false } }, rotation: -90, circumference: 360 }
        });
    }
} catch (err) { console.error('Sentimen gauge error:', err); }

try {
    const donutEl = document.getElementById('kepuasanDonut');
    if (donutEl && typeof Chart !== 'undefined') {
        new Chart(donutEl.getContext('2d'), {
            type: 'doughnut',
            data: { labels: ['Kondisi Baik', 'Cukup', 'Perlu Perhatian'], datasets: [{ data: [{{ $kepuasan['sangat_puas'] }}, {{ $kepuasan['netral'] }}, {{ $kepuasan['tidak_puas'] }}], backgroundColor: ['#22c55e', '#eab308', '#ef4444'], borderWidth: 2, borderColor: '#fff', borderRadius: 4 }] },
            options: { cutout: '60%', responsive: false, plugins: { legend: { display: false }, tooltip: { enabled: true } } }
        });
    }
} catch (err) { console.error('Kepuasan donut error:', err); }

// Knowledge Graph — Semantic Web
const kgData = @json($kgData);
const kgContainer = document.getElementById('knowledge-graph');
console.log('[KG] vis:', typeof vis, '| nodes:', kgData?.nodes?.length);

if (typeof vis === 'undefined') {
    if (kgContainer) kgContainer.innerHTML = '<div class="flex items-center justify-center h-full p-6 text-center text-red-600"><p class="text-sm">⚠ Library vis-network gagal dimuat. Coba Ctrl+Shift+R.</p></div>';
} else if (!kgData || !kgData.nodes || kgData.nodes.length === 0) {
    if (kgContainer) kgContainer.innerHTML = '<div class="flex items-center justify-center h-full p-6 text-center text-gray-500"><p class="text-sm">Belum ada data Knowledge Graph.</p></div>';
} else if (kgData && kgData.nodes.length > 0) {
    // Build nodes with server-provided styling
    const nodes = new vis.DataSet(kgData.nodes.map(n => {
        const node = {
            id: n.id,
            label: n.label,
            group: n.group,
            title: n.title,
            info: n.info || {},
            size: n.size || 25,
            shape: n.shape || 'dot',
            borderWidth: 2,
            shadow: { enabled: true, color: 'rgba(0,0,0,0.12)', size: 5, x: 1, y: 2 },
        };
        if (n.color) node.color = n.color;
        if (n.font) node.font = { ...n.font, face: 'Inter, sans-serif', strokeWidth: 3, strokeColor: n.color?.background || '#333' };
        return node;
    }));

// Build edges with server-provided styling
    const edges = new vis.DataSet(kgData.edges.map(e => {
        const edge = {
            from: e.from,
            to: e.to,
            label: e.label || '',
            width: e.width || 1.5,
            // Custom fields untuk edge detail panel
            shared_keywords: e.shared_keywords || [],
            source_aspek:    e.source_aspek,
            target_aspek:    e.target_aspek,
            similarity:      e.similarity,
        };
        if (e.title) edge.title = e.title;
        if (e.color) edge.color = e.color;
        if (e.dashes !== undefined) edge.dashes = e.dashes;
        if (e.smooth) edge.smooth = e.smooth;
        else edge.smooth = { type: 'curvedCW', roundness: 0.1 };
        if (e.font) edge.font = e.font;
        else edge.font = { size: 10, color: '#374151', face: 'Inter, sans-serif', strokeWidth: 2, strokeColor: '#ffffff', align: 'middle' };
        // Only show arrows for ISU edges
        if (e.label === 'ISU') edge.arrows = { to: { enabled: true, scaleFactor: 0.6 } };
        return edge;
    }));

    const network = new vis.Network(document.getElementById('knowledge-graph'), { nodes, edges }, {
        physics: {
            solver: 'forceAtlas2Based',
            forceAtlas2Based: { gravitationalConstant: -80, springLength: 180 },
            stabilization: { iterations: 200 },
        },
        interaction: { hover: true, tooltipDelay: 200, zoomView: true, dragView: true },
    });

    // State: tracking kategori mana yang aktif (default semua aktif)
    const simFilterState = { strong: true, medium: true, weak: true };
    const originalEdges = JSON.parse(JSON.stringify(kgData.edges));
    console.log('[KG Filter] originalEdges backup:', originalEdges.length, 'total edges');

    // Helper: classify edge similarity → kategori
    function classifyEdgeSimilarity(sim) {
        if (sim === undefined || sim === null || sim === '') return null;  // edge UMKM→Topik / ISU / PERHATIAN
        const n = Number(sim);
        if (isNaN(n)) return null;
        if (n >= 0.5) return 'strong';
        if (n >= 0.2) return 'medium';
        return 'weak';
    }

    // Apply filter: CLEAR edges, RE-ADD hanya yang visible
    // Pendekatan ini bypass bug `edge.hidden = true` di vis-network 9.x
    function applySimilarityFilter() {
        // Hitung edges yang harus visible
        const visibleEdges = [];
        let totalInterTopic = 0;
        let hiddenCount = 0;

        originalEdges.forEach(function(edge) {
            const cat = classifyEdgeSimilarity(edge.similarity);

            // Edges tanpa similarity (UMKM→Topik, ISU, PERHATIAN) → SELALU tampil
            if (cat === null) {
                visibleEdges.push(edge);
                return;
            }

            totalInterTopic++;

            // Cek apakah kategori ini aktif
            if (simFilterState[cat]) {
                visibleEdges.push(edge);
            } else {
                hiddenCount++;
            }
        });

        // CLEAR semua edges, RE-ADD hanya yang visible
        // → vis-network akan re-render fresh, no stale edge artifact
        edges.clear();
        edges.add(visibleEdges);

        // Force redraw (defensive — clear+add usually triggers ini sendiri)
        if (typeof network !== 'undefined' && network.redraw) {
            network.redraw();
        }

        console.log(
            '[KG Filter] Applied — visible:', visibleEdges.length,
            '| inter-topic:', totalInterTopic,
            '| hidden:', hiddenCount,
            '| state:', JSON.stringify(simFilterState)
        );

        // Warning kalau tidak ada edge antar topik sama sekali
        if (totalInterTopic === 0) {
            console.warn('[KG Filter] ⚠️ TIDAK ADA edge antar topik di originalEdges! Submission Anda kemungkinan tidak punya cukup opini text untuk generate similarity. Submit ulang kuesioner dengan opini di multiple aspek.');
        }
    }

    // Update UI button styling sesuai state
    function updateFilterButtonsUI() {
        document.querySelectorAll('.sim-filter-btn').forEach(function(btn) {
            const cat = btn.dataset.simFilter;
            const isActive = simFilterState[cat];
            const status = btn.querySelector('.sim-filter-status');

            if (isActive) {
                btn.classList.remove('opacity-40', 'grayscale');
                btn.setAttribute('aria-pressed', 'true');
                if (status) { status.textContent = '✓'; status.title = 'aktif (klik untuk sembunyikan)'; }
            } else {
                btn.classList.add('opacity-40', 'grayscale');
                btn.setAttribute('aria-pressed', 'false');
                if (status) { status.textContent = '✗'; status.title = 'tersembunyi (klik untuk tampilkan)'; }
            }
        });
    }

    // Bind click handler ke tiap filter button
    document.querySelectorAll('.sim-filter-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const cat = btn.dataset.simFilter;
            simFilterState[cat] = !simFilterState[cat];
            applySimilarityFilter();
            updateFilterButtonsUI();
        });
    });

    // Reset filter button — kembalikan semua ke aktif
    const resetBtn = document.getElementById('reset-sim-filter');
    if (resetBtn) {
        resetBtn.addEventListener('click', function() {
            simFilterState.strong = true;
            simFilterState.medium = true;
            simFilterState.weak  = true;
            applySimilarityFilter();
            updateFilterButtonsUI();
        });
    }

    // Initial state — semua aktif, edge visible by default
    updateFilterButtonsUI();
    console.log('[KG] Similarity filter initialized:', simFilterState);

    // Click handler — show topic detail panel
    network.on('click', function(params) {
        const contentDiv = document.getElementById('node-detail-content');
        if (params.nodes.length > 0) {
            const nodeId = params.nodes[0];
            const node = kgData.nodes.find(n => n.id === nodeId);
            if (!node || !node.info) return;

            if (node.group === 'topik') {
                // Topic detail with polarity breakdown
                const info = node.info;
                let html = `<div class="space-y-3">`;
                html += `<h3 class="font-bold text-gray-800 text-sm">Topik: ${info.aspek}</h3>`;

                // HYBRID 50/50 SENTIMENT BANNER (NEW)
                const pctPos = info.persen_positif ?? 0;
                const pctLikert = info.persen_likert ?? 0;
                const pctOpini = info.persen_opini;  // null kalau tidak ada opini
                const dominan = info.dominan || 'Netral';

                // Warna banner sesuai dominan
                const bannerClr = dominan === 'Positif' ? 'green' : (dominan === 'Negatif' ? 'red' : 'gray');

                html += `<div class="bg-${bannerClr}-50 border border-${bannerClr}-200 rounded-lg p-3">`;
                html += `<div class="flex items-baseline justify-between mb-1">`;
                html += `<span class="text-xs font-semibold text-${bannerClr}-900 uppercase">Sentimen Positif</span>`;
                html += `<span class="text-xl font-extrabold text-${bannerClr}-700">${pctPos}%</span>`;
                html += `</div>`;
                html += `<div class="text-[10px] text-${bannerClr}-700 mt-1">`;
                if (pctOpini !== null && pctOpini !== undefined) {
                    html += `<span title="50% Likert + 50% Opini">Hybrid: Likert <strong>${pctLikert}%</strong> + Opini <strong>${pctOpini}%</strong></span>`;
                } else {
                    html += `<span title="Pure Likert karena tidak ada opini text">Pure Likert <strong>${pctLikert}%</strong> (opini text tidak diisi)</span>`;
                }
                html += `</div>`;
                html += `<div class="text-[10px] text-${bannerClr}-600 mt-0.5">Label: <strong>${dominan}</strong></div>`;
                html += `</div>`;

                html += `<div class="grid grid-cols-3 gap-2 mb-3">`;
                html += `<div class="bg-green-50 rounded-lg p-2 text-center"><p class="text-lg font-bold text-green-600">${info.n_positif || 0}</p><p class="text-[10px] text-green-600">Positif</p></div>`;
                html += `<div class="bg-gray-50 rounded-lg p-2 text-center"><p class="text-lg font-bold text-gray-600">${info.n_netral || 0}</p><p class="text-[10px] text-gray-600">Netral</p></div>`;
                html += `<div class="bg-red-50 rounded-lg p-2 text-center"><p class="text-lg font-bold text-red-600">${info.n_negatif || 0}</p><p class="text-[10px] text-red-600">Negatif</p></div>`;
                html += `</div>`;
                html += `<p class="text-xs text-gray-500">Skor Likert: <strong>${(info.skor_likert || 0).toFixed ? info.skor_likert.toFixed(2) : info.skor_likert}/5</strong></p>`;

                // OPINI LIST per polarity (positif/netral/negatif)
                // Tampilkan kalimat opini lengkap yang user tulis.
                const opiniList = info.opini_list || {};
                const totalOpini = (info.n_positif || 0) + (info.n_netral || 0) + (info.n_negatif || 0);

                if (totalOpini === 0) {
                    // EMPTY STATE: user tidak isi opini text → tampilkan info edukatif
                    html += `<div class="bg-blue-50 border border-blue-200 rounded p-3 mt-3 text-xs text-blue-800">`;
                    html += `<p class="font-semibold mb-1">💡 Belum Ada Opini Text</p>`;
                    html += `<p>Anda belum mengisi opini/pendapat di kuesioner untuk aspek ini. `;
                    html += `Isi opini text saat submit kuesioner untuk dapat analisis sentimen yang lebih kaya.</p>`;
                    html += `</div>`;
                } else {
                    // Render opini per polarity dengan section header
                    html += `<div class="mt-2 space-y-2 border-t pt-2">`;
                    html += `<p class="text-[10px] font-semibold text-gray-500 uppercase tracking-wide">📝 Kalimat Opini Anda</p>`;

                    ['Positif', 'Netral', 'Negatif'].forEach(pol => {
                        const items = opiniList[pol] || [];
                        if (items.length === 0) return;
                        const clrMap = {
                            Positif: { text: 'text-green-700', bg: 'bg-green-50', border: 'border-green-400', badge: 'bg-green-100 text-green-700' },
                            Netral:  { text: 'text-gray-700',  bg: 'bg-gray-50',  border: 'border-gray-400',  badge: 'bg-gray-100 text-gray-700' },
                            Negatif: { text: 'text-red-700',   bg: 'bg-red-50',   border: 'border-red-400',   badge: 'bg-red-100 text-red-700' },
                        };
                        const clr = clrMap[pol];
                        html += `<div>`;
                        html += `<p class="text-xs font-semibold mb-1.5 flex items-center gap-2">`;
                        html += `<span class="${clr.text}">${pol === 'Positif' ? '🟢' : pol === 'Netral' ? '⚪' : '🔴'} ${pol}</span>`;
                        html += `<span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-bold ${clr.badge}">${items.length} opini</span>`;
                        html += `</p>`;
                        html += `<div class="space-y-1.5 max-h-32 overflow-y-auto pr-1">`;
                        items.forEach(item => {
                            const opiniText = item.opini || '(kosong)';
                            const rating = item.rating || '-';
                            const itemName = item.item || '';
                            html += `<div class="text-xs ${clr.bg} rounded-lg p-2 border-l-2 ${clr.border}">`;
                            html += `<p class="text-gray-700 leading-snug mb-1">"${opiniText}"</p>`;
                            html += `<div class="flex items-center justify-between text-[10px] text-gray-500">`;
                            html += `<span>${itemName}</span>`;
                            html += `<span class="${clr.text} font-bold">★ ${rating}/5</span>`;
                            html += `</div>`;
                            html += `</div>`;
                        });
                        html += `</div>`;
                        html += `</div>`;
                    });
                    html += `</div>`;
                }
                html += `</div>`;
                contentDiv.innerHTML = html;

            } else if (node.group === 'isu') {
                // Issue detail
                const info = node.info;
                let html = `<div class="space-y-3">`;
                html += `<div class="flex items-center gap-2 mb-2"><span class="w-3 h-3 rounded bg-red-400 flex-shrink-0"></span><span class="font-semibold text-gray-900 text-sm">${info.Isu || node.label}</span></div>`;
                html += `<div><span class="text-xs text-gray-400 uppercase">Aspek</span><p class="text-sm font-medium text-gray-700">${info.Aspek || '-'}</p></div>`;
                html += `<div><span class="text-xs text-gray-400 uppercase">Skor</span><p class="text-sm font-medium text-red-600">${info.Skor || '-'}/5</p></div>`;
                const isuName = encodeURIComponent(info.Isu || node.label);
                html += `<a href="/solusi-node?isu=${isuName}" class="block mt-3 text-center btn-primary text-sm py-2 rounded-lg">Lihat Rekomendasi AI</a>`;
                html += `</div>`;
                contentDiv.innerHTML = html;

            } else if (node.group === 'perhatian') {
                // "Perlu Perhatian" detail — items dengan skor = 3 (NEW)
                const info = node.info;
                let html = `<div class="space-y-3">`;
                html += `<div class="flex items-center gap-2 mb-2"><span class="w-3 h-3 rounded bg-amber-400 flex-shrink-0"></span><span class="font-semibold text-gray-900 text-sm">${info.Item || node.label}</span></div>`;
                html += `<span class="inline-block bg-amber-100 text-amber-700 text-[10px] font-bold px-2 py-0.5 rounded-full">🟡 Perlu Perhatian</span>`;
                html += `<div><span class="text-xs text-gray-400 uppercase">Aspek</span><p class="text-sm font-medium text-gray-700">${info.Aspek || '-'}</p></div>`;
                html += `<div><span class="text-xs text-gray-400 uppercase">Skor</span><p class="text-sm font-medium text-amber-600">${info.Skor || '-'}/5 (Netral)</p></div>`;
                html += `<div class="bg-amber-50 border border-amber-200 rounded p-2 text-xs text-amber-900">`;
                html += `Item ini <strong>belum kritis</strong> tapi <strong>ada ruang untuk improvement</strong>. Pertimbangkan untuk tingkatkan ke skor 4 agar masuk kategori "Baik".`;
                html += `</div>`;
                const itemName = encodeURIComponent(info.Item || node.label);
                html += `<a href="/solusi-node?isu=${itemName}" class="block mt-3 text-center text-amber-700 border border-amber-300 hover:bg-amber-50 text-sm py-2 rounded-lg font-semibold transition">Lihat Saran Peningkatan</a>`;
                html += `</div>`;
                contentDiv.innerHTML = html;

            } else {
                // UMKM center node
                let html = `<div class="space-y-3">`;
                Object.entries(node.info).forEach(([key, val]) => {
                    html += `<div><span class="text-xs text-gray-400 uppercase">${key}</span><p class="text-sm font-medium text-gray-700">${val}</p></div>`;
                });
                html += `</div>`;
                contentDiv.innerHTML = html;
            }
            return;
        }

        // EDGE CLICK — Keterhubungan antar topik 
        if (params.edges.length > 0) {
            const edgeId = params.edges[0];
            const edge = edges.get(edgeId);

            // Hanya tampilkan untuk edge antar topik (yang punya source_aspek)
            if (!edge || !edge.source_aspek) {
                contentDiv.innerHTML = '<p class="text-sm text-gray-400 italic">Klik sebuah node pada graph untuk melihat detail opini.</p>';
                return;
            }

            const sim = Number(edge.similarity);
            const pct = Math.round(sim * 1000) / 10;  // 0.485 → 48.5

            // Kategori berdasarkan persen (thresholds berbeda dari edge color)
            let kategori, katColor, katBg;
            if (pct >= 62)      { kategori = 'Kuat';   katColor = '#16a34a'; katBg = '#f0fdf4'; }
            else if (pct >= 37) { kategori = 'Sedang'; katColor = '#d97706'; katBg = '#fffbeb'; }
            else                { kategori = 'Lemah';  katColor = '#6b7280'; katBg = '#f9fafb'; }

            // Warna per aspek (sesuai topikColor di backend)
            const aspekColorMap = {
                'Operasional': '#3b82f6',
                'Pemasaran':   '#8b5cf6',
                'Keuangan':    '#f59e0b',
                'Teknologi':   '#10b981',
                'Tantangan':   '#ef4444',
            };
            const srcColor = aspekColorMap[edge.source_aspek] || '#6b7280';
            const tgtColor = aspekColorMap[edge.target_aspek] || '#6b7280';

            let html = `<div class="space-y-3">`;
            html += `<h3 class="font-bold text-gray-800 text-sm">Keterhubungan Topik</h3>`;

            // Pair aspek dengan warna sesuai aspek
            html += `<div class="flex items-center gap-2 text-sm flex-wrap">`;
            html += `  <span class="font-semibold" style="color:${srcColor}">${edge.source_aspek}</span>`;
            html += `  <span class="text-gray-400">&#8596;</span>`;
            html += `  <span class="font-semibold" style="color:${tgtColor}">${edge.target_aspek}</span>`;
            html += `</div>`;

            // Persentase besar dengan warna kategori
            html += `<div class="rounded-lg p-3 text-center" style="background:${katBg}">`;
            html += `  <p class="text-2xl font-bold" style="color:${katColor}">${pct}%</p>`;
            html += `  <p class="text-xs" style="color:${katColor}">Similarity (${kategori})</p>`;
            html += `</div>`;

            // Kata penghubung
            if (edge.shared_keywords && edge.shared_keywords.length > 0) {
                html += `<div>`;
                html += `  <p class="text-xs font-semibold text-gray-700 uppercase mb-2">&#128279; Kata Penghubung</p>`;
                html += `  <p class="text-xs text-gray-500 mb-2">Kata-kata yang muncul di opini kedua aspek dan menjadi jembatan semantik:</p>`;
                html += `  <div class="space-y-1.5">`;
                edge.shared_keywords.slice(0, 5).forEach((kw, idx) => {
                    const kata = kw.kata || '-';
                    const kontribusi = kw.kontribusi || 0;
                    html += `<div class="bg-blue-50 rounded p-2">`;
                    html += `  <div class="flex items-center justify-between">`;
                    html += `    <span class="font-medium text-blue-900 text-sm">${idx + 1}. ${kata}</span>`;
                    html += `    <span class="text-xs text-blue-600 font-mono">${kontribusi.toFixed(3)}</span>`;
                    html += `  </div>`;
                    html += `</div>`;
                });
                html += `  </div>`;
                html += `</div>`;
            } else {
                html += `<p class="text-xs text-gray-400 italic">Tidak ada kata kunci dominan yang menjembatani kedua aspek ini.</p>`;
            }

            html += `</div>`;
            contentDiv.innerHTML = html;
            return;
        }

        // Default: tidak ada yang diklik
        contentDiv.innerHTML = '<p class="text-sm text-gray-400 italic">Klik node atau garis (edge) pada graph untuk melihat detail.</p>';
    });
}

// ===== SUS Modal Logic =====
function updateSUSProgress() {
    let filled = 0;
    for (let i = 1; i <= 10; i++) {
        const sel = document.querySelector(`input[name="sus_${i}"]:checked`);
        const card = document.getElementById(`sus-card-${i}`);
        if (sel) {
            filled++;
            card.classList.add('border-green-300');
            card.classList.remove('border-gray-200', 'border-red-400');
            card.style.background = 'rgba(220,252,231,0.3)';
        }
    }
    const pct = (filled / 10) * 100;
    const bar = document.getElementById('progress-bar');
    const txt = document.getElementById('progress-text');
    bar.style.width = pct + '%';
    if (filled === 10) {
        bar.classList.remove('bg-blue-500'); bar.classList.add('bg-green-500');
        txt.textContent = '✓ Semua pertanyaan terisi';
        txt.classList.add('text-green-600'); txt.classList.remove('text-blue-600');
    } else {
        bar.classList.remove('bg-green-500'); bar.classList.add('bg-blue-500');
        txt.textContent = `${filled} dari 10 pertanyaan`;
        txt.classList.add('text-blue-600'); txt.classList.remove('text-green-600');
    }
}

// Auto-show SUS: show if session flag is set (fresh kuesioner submit) OR first time ever
const showSUSFromSession = {{ session('show_sus') ? 'true' : 'false' }};
const susAlreadyShown = localStorage.getItem('sus_shown_{{ auth()->id() }}');
const susAlreadySubmitted = localStorage.getItem('sus_submitted_{{ auth()->id() }}');

if (!{{ $hasFilledSUS ? 'true' : 'false' }} && (showSUSFromSession || (!susAlreadyShown && !susAlreadySubmitted))) {
    setTimeout(() => { document.getElementById('sus-modal')?.classList.remove('hidden'); }, 2000);
}

function closeSUS() {
    document.getElementById('sus-modal')?.classList.add('hidden');
    localStorage.setItem('sus_shown_{{ auth()->id() }}', 'true');
}

function submitSUS() {
    const answers = {};
    let allFilled = true;
    for (let i = 1; i <= 10; i++) {
        const sel = document.querySelector(`input[name="sus_${i}"]:checked`);
        const card = document.getElementById(`sus-card-${i}`);
        if (!sel) {
            allFilled = false;
            card.classList.add('border-red-400');
            card.classList.remove('border-gray-200', 'border-green-300');
            card.style.background = 'rgba(254,226,226,0.3)';
        } else {
            answers[`sus_${i}`] = parseInt(sel.value);
        }
    }
    if (!allFilled) {
        const first = document.querySelector('.border-red-400');
        if (first) first.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return;
    }
    let total = 0;
    for (let i = 1; i <= 10; i++) {
        const s = answers[`sus_${i}`];
        total += (i % 2 !== 0) ? (s - 1) : (5 - s);
    }
    const skorAkhir = total * 2.5;
    let kategori = '';
    if (skorAkhir >= 85) kategori = 'Excellent';
    else if (skorAkhir >= 72) kategori = 'Good';
    else if (skorAkhir >= 52) kategori = 'OK';
    else kategori = 'Poor';

    fetch('/api/sus/submit', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'X-User-Id': '{{ auth()->id() }}' },
        body: JSON.stringify({ answers, skor_sus: skorAkhir, user_id: {{ auth()->id() }} })
    })
    .then(r => r.json())
    .then(() => {
        closeSUS();
        const toast = document.createElement('div');
        toast.className = 'animate-slide-down fixed top-20 right-4 z-[80] bg-green-500 text-white px-5 py-3 rounded-xl shadow-2xl flex items-center gap-3 max-w-sm';
        toast.innerHTML = `<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg><span class="text-sm font-medium">Terima kasih telah mengisi evaluasi!</span>`;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 6000);
        localStorage.setItem('sus_submitted_{{ auth()->id() }}', 'true');
    })
    .catch(err => { console.error('SUS submit error:', err); closeSUS(); });
}
@endif
</script>
@endpush
@endsection
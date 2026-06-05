@extends('layouts.app')
@section('title', 'Rekomendasi AI')
@section('content')
@php
    // ════════════════════════════════════════════════════════════════════
    // Group recommendations by kategori untuk summary & visual separation
    // ════════════════════════════════════════════════════════════════════
    $kritis    = $recommendations->filter(fn($r) => ($r->kategori ?? null) === 'kritis'    || ($r->skor ?? 5) <= 2);
    $perhatian = $recommendations->filter(fn($r) => ($r->kategori ?? null) === 'perhatian' || (int)($r->skor ?? 5) === 3);

    // Style mapping per kategori (untuk konsistensi visual)
    $catStyles = [
        'sangat_kritis' => [
            'emoji'      => '🔴',
            'bgHeader'   => 'bg-red-50',
            'badgeClass' => 'bg-red-100 text-red-700',
            'badgeText'  => 'Sangat Kritis',
            'recTitle'   => '✦ REKOMENDASI DARI AI',
            'recBg'      => 'bg-blue-50/50',
            'recNumBg'   => 'bg-blue-100 text-blue-700',
        ],
        'kritis' => [
            'emoji'      => '🟠',
            'bgHeader'   => 'bg-orange-50',
            'badgeClass' => 'bg-orange-100 text-orange-700',
            'badgeText'  => 'Kritis',
            'recTitle'   => '✦ REKOMENDASI DARI AI',
            'recBg'      => 'bg-blue-50/50',
            'recNumBg'   => 'bg-blue-100 text-blue-700',
        ],
        'perhatian' => [
            'emoji'      => '🟡',
            'bgHeader'   => 'bg-amber-50',
            'badgeClass' => 'bg-amber-100 text-amber-700',
            'badgeText'  => 'Perlu Perhatian',
            'recTitle'   => '💡 SARAN PENINGKATAN',
            'recBg'      => 'bg-amber-50/50',
            'recNumBg'   => 'bg-amber-100 text-amber-700',
        ],
    ];
@endphp
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
    <div class="flex items-center gap-3 mb-6 sm:mb-8 animate-fade-in-up">
        <a href="{{ route('dashboard') }}" class="text-blue-600 hover:text-blue-700 font-medium inline-flex items-center gap-1 min-h-[44px]">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali ke Dashboard
        </a>
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Solusi/Masukan</h1>
    </div>

    @if(!$hasKuesioner)
    <div class="card-static p-8 sm:p-12 text-center">
        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
        <h3 class="text-lg font-semibold text-gray-500 mb-2">Belum Ada Rekomendasi</h3>
        <p class="text-gray-400 mb-6">Isi kuesioner terlebih dahulu untuk mendapatkan rekomendasi AI.</p>
        <a href="{{ route('kuesioner', ['step' => 1]) }}" class="btn-primary min-h-[44px] inline-flex items-center">Isi Kuesioner</a>
    </div>
    @elseif($recommendations->isEmpty())
    <div class="card-static p-8 sm:p-12 text-center bg-gradient-to-br from-green-50 to-emerald-50 border-green-200">
        <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-green-100 flex items-center justify-center">
            <svg class="w-10 h-10 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        </div>
        <h3 class="text-xl font-bold text-green-800 mb-2">Selamat! 🎉</h3>
        <p class="text-green-700">Tidak ada isu kritis teridentifikasi dari bisnis Anda. Pertahankan performa yang baik!</p>
    </div>
    @else
    {{-- Summary Cards: count kritis vs perhatian --}}
    <div class="grid grid-cols-2 gap-3 sm:gap-4 mb-6 animate-fade-in-up">
        <div class="card-static p-4 flex items-center gap-3 border-l-4 border-red-400">
            <span class="text-2xl">🔴</span>
            <div>
                <p class="text-2xl font-bold text-red-600">{{ $kritis->count() }}</p>
                <p class="text-xs text-gray-500 uppercase tracking-wide">Isu Kritis</p>
            </div>
        </div>
        <div class="card-static p-4 flex items-center gap-3 border-l-4 border-amber-400">
            <span class="text-2xl">🟡</span>
            <div>
                <p class="text-2xl font-bold text-amber-600">{{ $perhatian->count() }}</p>
                <p class="text-xs text-gray-500 uppercase tracking-wide">Perlu Perhatian</p>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- SECTION 1: ISU KRITIS (skor ≤ 2) — Prioritas Tindakan Segera   --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    @if($kritis->count() > 0)
    <div class="mb-6">
        <h2 class="text-sm font-bold text-gray-700 uppercase tracking-wide mb-3 flex items-center gap-2">
            <span class="text-red-500">🔴</span>
            Isu Kritis — Perlu Tindakan Segera
            <span class="text-xs font-normal text-gray-400 normal-case">(skor ≤ 2)</span>
        </h2>
        <div class="space-y-4 sm:space-y-6 stagger-children">
            @foreach($kritis as $rec)
                @php
                    $catKey = ($rec->skor ?? 2) <= 1 ? 'sangat_kritis' : 'kritis';
                    $s = $catStyles[$catKey];
                @endphp
                <div class="card-static overflow-hidden">
                    {{-- Issue Header --}}
                    <div class="px-4 sm:px-6 py-4 border-b border-gray-100 flex items-start sm:items-center gap-3 {{ $s['bgHeader'] }}">
                        <span class="text-xl flex-shrink-0 mt-0.5 sm:mt-0">{{ $s['emoji'] }}</span>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-gray-900 text-sm sm:text-base">{{ $rec->isu }}</h3>
                            <p class="text-xs sm:text-sm text-gray-500">Aspek: {{ $rec->aspek }} — Skor kamu: {{ $rec->skor }}/5</p>
                        </div>
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full flex-shrink-0 {{ $s['badgeClass'] }}">
                            {{ $s['badgeText'] }}
                        </span>
                    </div>

                    {{-- AI Recommendations --}}
                    <div class="px-4 sm:px-6 py-4 sm:py-5">
                        <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3 flex items-center gap-2">
                            {{ $s['recTitle'] }}
                        </h4>
                        @if(!empty($rec->rekomendasi))
                        <ol class="space-y-2 sm:space-y-3">
                            @foreach($rec->rekomendasi as $i => $saran)
                            <li class="flex gap-3 {{ $s['recBg'] }} rounded-lg p-3 sm:p-4">
                                <span class="w-6 h-6 sm:w-7 sm:h-7 rounded-full {{ $s['recNumBg'] }} flex items-center justify-center text-xs sm:text-sm font-bold flex-shrink-0">{{ $i + 1 }}</span>
                                <p class="text-sm text-gray-700 leading-relaxed">{!! nl2br(e($saran)) !!}</p>
                            </li>
                            @endforeach
                        </ol>
                        @else
                        <p class="text-sm text-gray-400 italic">Rekomendasi sedang diproses...</p>
                        @endif
                    </div>

                    {{-- Suggested Modules --}}
                    @if(!empty($rec->modul_saran))
                    <div class="px-4 sm:px-6 pb-4 sm:pb-5 border-t border-gray-100 pt-4">
                        <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3 flex items-center gap-2">
                            📚 MODUL YANG DISARANKAN
                        </h4>
                        <div class="space-y-2">
                            @foreach($rec->modul_saran as $modul)
                            <a href="{{ route('modul.show', $modul['slug']) }}"
                               class="flex items-center justify-between bg-blue-50 border-l-4 border-blue-400 rounded-r-lg px-3 sm:px-4 py-3 hover:bg-blue-100 transition group">
                                <div class="flex items-center gap-2 sm:gap-3 min-w-0">
                                    <span class="text-blue-500 flex-shrink-0 text-lg">▶</span>
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-blue-800 truncate">{{ $modul['judul'] }}</p>
                                        <p class="text-xs text-blue-500">{{ $modul['kategori'] }} · {{ $modul['durasi'] }}</p>
                                    </div>
                                </div>
                                <span class="text-xs text-blue-600 flex-shrink-0 ml-2 group-hover:underline whitespace-nowrap hidden sm:inline">Tonton Modul →</span>
                                <span class="text-blue-600 flex-shrink-0 ml-2 sm:hidden">→</span>
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- SECTION 2: PERLU PERHATIAN (skor = 3) — Ruang untuk Peningkatan --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    @if($perhatian->count() > 0)
    <div class="mt-8">
        <h2 class="text-sm font-bold text-gray-700 uppercase tracking-wide mb-3 flex items-center gap-2">
            <span class="text-amber-500">🟡</span>
            Perlu Perhatian — Ruang untuk Peningkatan
            <span class="text-xs font-normal text-gray-400 normal-case">(skor = 3)</span>
        </h2>
        <div class="space-y-4 sm:space-y-6 stagger-children">
            @foreach($perhatian as $rec)
                @php $s = $catStyles['perhatian']; @endphp
                <div class="card-static overflow-hidden">
                    {{-- Issue Header --}}
                    <div class="px-4 sm:px-6 py-4 border-b border-gray-100 flex items-start sm:items-center gap-3 {{ $s['bgHeader'] }}">
                        <span class="text-xl flex-shrink-0 mt-0.5 sm:mt-0">{{ $s['emoji'] }}</span>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-gray-900 text-sm sm:text-base">{{ $rec->isu }}</h3>
                            <p class="text-xs sm:text-sm text-gray-500">Aspek: {{ $rec->aspek }} — Skor kamu: {{ $rec->skor }}/5 (Netral)</p>
                        </div>
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full flex-shrink-0 {{ $s['badgeClass'] }}">
                            {{ $s['badgeText'] }}
                        </span>
                    </div>

                    {{-- Saran Peningkatan --}}
                    <div class="px-4 sm:px-6 py-4 sm:py-5">
                        <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3 flex items-center gap-2">
                            {{ $s['recTitle'] }}
                        </h4>
                        @if(!empty($rec->rekomendasi))
                        <ol class="space-y-2 sm:space-y-3">
                            @foreach($rec->rekomendasi as $i => $saran)
                            <li class="flex gap-3 {{ $s['recBg'] }} rounded-lg p-3 sm:p-4">
                                <span class="w-6 h-6 sm:w-7 sm:h-7 rounded-full {{ $s['recNumBg'] }} flex items-center justify-center text-xs sm:text-sm font-bold flex-shrink-0">{{ $i + 1 }}</span>
                                <p class="text-sm text-gray-700 leading-relaxed">{!! nl2br(e($saran)) !!}</p>
                            </li>
                            @endforeach
                        </ol>
                        @else
                        <p class="text-sm text-gray-400 italic">Saran sedang diproses...</p>
                        @endif
                    </div>

                    {{-- Suggested Modules --}}
                    @if(!empty($rec->modul_saran))
                    <div class="px-4 sm:px-6 pb-4 sm:pb-5 border-t border-gray-100 pt-4">
                        <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3 flex items-center gap-2">
                            📚 MODUL YANG DISARANKAN
                        </h4>
                        <div class="space-y-2">
                            @foreach($rec->modul_saran as $modul)
                            <a href="{{ route('modul.show', $modul['slug']) }}"
                               class="flex items-center justify-between bg-amber-50 border-l-4 border-amber-400 rounded-r-lg px-3 sm:px-4 py-3 hover:bg-amber-100 transition group">
                                <div class="flex items-center gap-2 sm:gap-3 min-w-0">
                                    <span class="text-amber-500 flex-shrink-0 text-lg">▶</span>
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-amber-800 truncate">{{ $modul['judul'] }}</p>
                                        <p class="text-xs text-amber-500">{{ $modul['kategori'] }} · {{ $modul['durasi'] }}</p>
                                    </div>
                                </div>
                                <span class="text-xs text-amber-600 flex-shrink-0 ml-2 group-hover:underline whitespace-nowrap hidden sm:inline">Tonton Modul →</span>
                                <span class="text-amber-600 flex-shrink-0 ml-2 sm:hidden">→</span>
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
    @endif
    @endif
</div>
@endsection
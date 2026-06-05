@extends('layouts.app')
@section('title', 'Detail Rekomendasi — ' . $recommendation->isu)
@section('content')
@php
    // Tentukan kategori (compat: support old object dengan only skor, dan new object dengan kategori)
    $kategori = $recommendation->kategori ?? null;
    if (!$kategori) {
        if ($recommendation->skor <= 2) $kategori = 'kritis';
        elseif ($recommendation->skor == 3) $kategori = 'perhatian';
        else $kategori = 'baik';
    }

    // Style mapping per kategori
    $styles = [
        'kritis' => [
            'emoji'      => '🔴',
            'bgHeader'   => 'bg-red-50',
            'borderHdr'  => 'border-b border-red-100',
            'badgeClass' => 'bg-red-100 text-red-700',
            'badgeText'  => $recommendation->skor <= 1 ? 'Sangat Kritis' : 'Kritis',
            'iconColor'  => 'text-red-500',
            'recTitle'   => '✦ REKOMENDASI DARI AI',
            'recBg'      => 'bg-blue-50/60',
            'recNumBg'   => 'bg-blue-100 text-blue-700',
        ],
        'perhatian' => [
            'emoji'      => '🟡',
            'bgHeader'   => 'bg-amber-50',
            'borderHdr'  => 'border-b border-amber-100',
            'badgeClass' => 'bg-amber-100 text-amber-700',
            'badgeText'  => 'Perlu Perhatian',
            'iconColor'  => 'text-amber-500',
            'recTitle'   => '💡 SARAN PENINGKATAN',
            'recBg'      => 'bg-amber-50/60',
            'recNumBg'   => 'bg-amber-100 text-amber-700',
        ],
        'baik' => [
            'emoji'      => '🟢',
            'bgHeader'   => 'bg-green-50',
            'borderHdr'  => 'border-b border-green-100',
            'badgeClass' => 'bg-green-100 text-green-700',
            'badgeText'  => 'Sudah Baik',
            'iconColor'  => 'text-green-500',
            'recTitle'   => '🎉 KETERANGAN',
            'recBg'      => 'bg-green-50/60',
            'recNumBg'   => 'bg-green-100 text-green-700',
        ],
    ];
    $s = $styles[$kategori];
@endphp
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
    {{-- Back to Dashboard/KG --}}
    <a href="{{ route('dashboard') }}" class="text-blue-600 hover:text-blue-700 font-medium inline-flex items-center gap-1 mb-6 min-h-[44px]">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali ke Knowledge Graph
    </a>

    <div class="card-static overflow-hidden animate-fade-in-up">
        {{-- Issue Header --}}
        <div class="px-5 sm:px-6 py-4 sm:py-5 {{ $s['bgHeader'] }} {{ $s['borderHdr'] }}">
            <div class="flex items-start gap-3">
                <span class="text-2xl flex-shrink-0 mt-0.5">{{ $s['emoji'] }}</span>
                <div class="flex-1">
                    <h1 class="text-lg sm:text-xl font-bold text-gray-900">{{ $recommendation->isu }}</h1>
                    <div class="flex flex-wrap items-center gap-3 mt-2">
                        <span class="text-sm text-gray-600">Aspek: <strong>{{ $recommendation->aspek }}</strong></span>
                        <span class="text-sm text-gray-600">Skor: <strong>{{ $recommendation->skor }}/5</strong></span>
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full {{ $s['badgeClass'] }}">
                            {{ $s['badgeText'] }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- AI Recommendations / Saran Peningkatan --}}
        <div class="px-5 sm:px-6 py-5 sm:py-6">
            <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                {{ $s['recTitle'] }}
            </h2>
            @if($recommendation->rekomendasi && (is_array($recommendation->rekomendasi) ? count($recommendation->rekomendasi) > 0 : !empty($recommendation->rekomendasi)))
            <div class="space-y-3">
                @foreach($recommendation->rekomendasi as $i => $saran)
                <div class="flex gap-3 {{ $s['recBg'] }} rounded-xl p-4">
                    <span class="w-7 h-7 rounded-full {{ $s['recNumBg'] }} flex items-center justify-center text-sm font-bold flex-shrink-0">{{ $i + 1 }}</span>
                    <p class="text-sm text-gray-700 leading-relaxed">{!! nl2br(e($saran)) !!}</p>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-sm text-gray-400 italic">Belum ada saran tersedia untuk item ini.</p>
            @endif
        </div>

        {{-- Suggested Modules --}}
        @if(!empty($recommendation->modul_saran))
        <div class="px-5 sm:px-6 pb-5 sm:pb-6 border-t border-gray-100 pt-4">
            <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3 flex items-center gap-2">
                📚 MODUL YANG DISARANKAN
            </h2>
            <div class="space-y-2">
                @foreach($recommendation->modul_saran as $modul)
                <a href="{{ route('modul.show', $modul['slug']) }}"
                   class="flex items-center justify-between bg-blue-50 border-l-4 border-blue-400 rounded-r-lg px-4 py-3 hover:bg-blue-100 transition group">
                    <div class="flex items-center gap-3 min-w-0">
                        <span class="text-blue-500 flex-shrink-0 text-lg">▶</span>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-blue-800 truncate">{{ $modul['judul'] }}</p>
                            <p class="text-xs text-blue-500">{{ $modul['kategori'] }} · {{ $modul['durasi'] }}</p>
                        </div>
                    </div>
                    <span class="text-xs text-blue-600 flex-shrink-0 ml-2 group-hover:underline whitespace-nowrap">Tonton Modul →</span>
                </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- Link to see all recommendations --}}
    <div class="mt-6 text-center">
        <a href="{{ route('rekomendasi') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium inline-flex items-center gap-1">
            Lihat semua rekomendasi AI →
        </a>
    </div>
</div>
@endsection
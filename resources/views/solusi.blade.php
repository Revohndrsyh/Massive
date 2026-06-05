@extends('layouts.app')
@section('title', 'Solusi/Masukan')
@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center gap-4 mb-8 animate-fade-in-up">
        <a href="{{ route('rekomendasi') }}" class="text-blue-600 hover:text-blue-700 font-medium inline-flex items-center gap-1">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Solusi/Masukan</h1>
    </div>

    <div class="card-static p-8 animate-fade-in-up">
        <div class="flex items-center gap-3 mb-6 pb-6 border-b border-gray-100">
            <span class="text-2xl">{{ $recommendation->skor <= 1 ? '🔴' : '🟠' }}</span>
            <div>
                <h2 class="text-xl font-bold text-gray-900">{{ $recommendation->isu }}</h2>
                <p class="text-sm text-gray-500">Aspek: {{ $recommendation->aspek }} — Skor: {{ $recommendation->skor }}/5</p>
            </div>
        </div>

        <div class="prose prose-sm max-w-none">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Rekomendasi Strategis:</h3>
            @if($recommendation->rekomendasi)
            @foreach($recommendation->rekomendasi as $i => $saran)
            <div class="bg-gray-50 rounded-xl p-5 mb-4">
                <div class="flex gap-3">
                    <span class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center text-sm font-bold flex-shrink-0">{{ $i + 1 }}</span>
                    <p class="text-gray-700 leading-relaxed">{{ $saran }}</p>
                </div>
            </div>
            @endforeach
            @endif
        </div>
    </div>
</div>
@endsection

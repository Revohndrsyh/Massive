@extends('layouts.app')
@section('title', 'Welcome')
@section('content')

{{-- Hero Section --}}
<section class="gradient-hero relative overflow-hidden">
    {{-- Animated Background --}}
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-20 left-10 w-72 h-72 bg-blue-500 rounded-full blur-3xl animate-float"></div>
        <div class="absolute bottom-10 right-20 w-96 h-96 bg-teal-400 rounded-full blur-3xl animate-float" style="animation-delay: 1.5s;"></div>
        <div class="absolute top-40 right-40 w-48 h-48 bg-purple-500 rounded-full blur-3xl animate-float" style="animation-delay: 3s;"></div>
    </div>

    <div class="absolute inset-0 opacity-5" style="background-image: url('data:image/svg+xml,%3Csvg width=%2240%22 height=%2240%22 viewBox=%220 0 40 40%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cg fill=%22%23fff%22 fill-opacity=%221%22%3E%3Cpath d=%22M0 0h1v1H0zM20 0h1v1h-1zM0 20h1v1H0zM20 20h1v1h-1z%22/%3E%3C/g%3E%3C/svg%3E');"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-28 lg:py-36">
        <div class="max-w-3xl mx-auto text-center animate-fade-in-up">
            <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm rounded-full px-4 py-2 mb-8 border border-white/20">
                <div class="w-6 h-6 rounded-md bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <rect x="2" y="14" width="3" height="8" rx="1"/><rect x="7" y="10" width="3" height="12" rx="1"/><rect x="12" y="6" width="3" height="16" rx="1"/><rect x="17" y="2" width="3" height="20" rx="1"/>
                    </svg>
                </div>
                <span class="text-blue-200 text-sm font-medium">🏪 Platform Analisis Kondisi Bisnis UMKM</span>
            </div>

            <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-extrabold text-white leading-tight mb-6">
                Analisis Kondisi Bisnis Anda dengan
                <span class="bg-gradient-to-r from-blue-400 to-teal-300 bg-clip-text text-transparent"> MASSIVE</span>
            </h1>
            <p class="text-base sm:text-lg md:text-xl text-gray-300 leading-relaxed mb-10 max-w-2xl mx-auto">
                MASSIVE adalah platform analisis bisnis yang dirancang khusus untuk pelaku UMKM, menawarkan analisis kondisi bisnis, visualisasi data yang informatif, dan modul pembelajaran interaktif untuk membantu Anda memahami dan mengembangkan usaha.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('register') }}" class="btn-primary text-base sm:text-lg px-8 py-3.5 rounded-xl inline-flex items-center justify-center gap-2 min-h-[48px]">
                    Mulai Sekarang
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </a>
                <a href="#features" class="bg-white text-navy-900 font-semibold text-base sm:text-lg px-8 py-3.5 rounded-xl inline-flex items-center justify-center gap-2 hover:bg-blue-100 transition-all shadow-lg min-h-[48px]">
                    Pelajari Lebih
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </a>
            </div>
        </div>

        {{-- Dashboard Preview --}}
        <div class="mt-12 md:mt-16 hidden sm:flex justify-center animate-fade-in" style="animation-delay: 0.5s;">
            <div class="relative w-full max-w-2xl">
                <div class="bg-white/5 backdrop-blur-sm rounded-2xl border border-white/10 p-4 sm:p-6 shadow-2xl">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-3 h-3 rounded-full bg-red-400"></div>
                        <div class="w-3 h-3 rounded-full bg-yellow-400"></div>
                        <div class="w-3 h-3 rounded-full bg-green-400"></div>
                        <span class="text-gray-400 text-xs ml-2">MASSIVE Dashboard Preview</span>
                    </div>
                    <div class="grid grid-cols-4 gap-2 sm:gap-3">
                        <div class="bg-white/10 rounded-lg p-2 sm:p-3 text-center">
                            <div class="text-xl sm:text-2xl font-bold text-blue-300">92%</div>
                            <div class="text-xs text-gray-400 mt-1">Akurasi</div>
                        </div>
                        <div class="bg-white/10 rounded-lg p-2 sm:p-3 text-center">
                            <div class="text-xl sm:text-2xl font-bold text-green-300">85%</div>
                            <div class="text-xs text-gray-400 mt-1">Kondisi Positif</div>
                        </div>
                        <div class="bg-white/10 rounded-lg p-2 sm:p-3 text-center">
                            <div class="text-xl sm:text-2xl font-bold text-yellow-300">12</div>
                            <div class="text-xs text-gray-400 mt-1">Aspek Bisnis</div>
                        </div>
                        <div class="bg-white/10 rounded-lg p-2 sm:p-3 text-center">
                            <div class="text-xl sm:text-2xl font-bold text-purple-300">3</div>
                            <div class="text-xs text-gray-400 mt-1">Algoritma ML</div>
                        </div>
                    </div>
                    <div class="mt-4 flex gap-1 sm:gap-2">
                        @for($i = 0; $i < 12; $i++)
                        <div class="flex-1 bg-gradient-to-t from-blue-500/60 to-blue-400/30 rounded-t-sm" style="height: {{ rand(30, 80) }}px;"></div>
                        @endfor
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Key Features --}}
<section id="features" class="py-16 md:py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12 md:mb-16 animate-fade-in-up">
            <span class="inline-block bg-blue-100 text-blue-700 text-sm font-semibold px-4 py-1.5 rounded-full mb-4">FITUR UNGGULAN</span>
            <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-900 mb-4">Fitur Unggulan MASSIVE</h2>
            <p class="text-base sm:text-lg text-gray-500 max-w-2xl mx-auto">MASSIVE menyediakan berbagai alat bantu untuk membantu Anda menganalisis kondisi bisnis secara efektif.</p>
        </div>

        <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-6 md:gap-8 stagger-children">
            <div class="card p-6 md:p-8 text-center group">
                <div class="w-14 h-14 md:w-16 md:h-16 mx-auto mb-5 md:mb-6 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-7 h-7 md:w-8 md:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <h3 class="text-lg md:text-xl font-bold text-gray-900 mb-3">Visualisasi Data Bisnis</h3>
                <p class="text-gray-500 leading-relaxed text-sm md:text-base">Lihat data bisnis Anda dalam bentuk grafik dan diagram interaktif, memudahkan Anda menemukan tren dan pola dalam usaha.</p>
            </div>

            <div class="card p-6 md:p-8 text-center group">
                <div class="w-14 h-14 md:w-16 md:h-16 mx-auto mb-5 md:mb-6 rounded-2xl bg-green-100 text-green-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-7 h-7 md:w-8 md:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                </div>
                <h3 class="text-lg md:text-xl font-bold text-gray-900 mb-3">Analisis Kondisi Bisnis</h3>
                <p class="text-gray-500 leading-relaxed text-sm md:text-base">Pahami kondisi bisnis Anda sendiri melalui kuesioner yang dianalisis oleh 3 algoritma machine learning untuk hasil yang akurat.</p>
            </div>

            <div class="card p-6 md:p-8 text-center group sm:col-span-2 md:col-span-1">
                <div class="w-14 h-14 md:w-16 md:h-16 mx-auto mb-5 md:mb-6 rounded-2xl bg-purple-100 text-purple-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-7 h-7 md:w-8 md:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <h3 class="text-lg md:text-xl font-bold text-gray-900 mb-3">Modul Pembelajaran</h3>
                <p class="text-gray-500 leading-relaxed text-sm md:text-base">Akses video pembelajaran dan materi interaktif untuk meningkatkan kemampuan pengelolaan bisnis Anda bersama MASSIVE.</p>
            </div>
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="py-12 md:py-16 gradient-blue">
    <div class="max-w-4xl mx-auto px-4 text-center">
        <h2 class="text-2xl md:text-3xl font-bold text-white mb-4">Siap Memulai Analisis Bisnis Anda?</h2>
        <p class="text-blue-100 text-base md:text-lg">Daftar sekarang dan dapatkan insight mendalam tentang bisnis UMKM Anda.</p>
    </div>
</section>

@endsection

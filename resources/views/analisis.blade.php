@extends('layouts.app')
@section('title', 'Analisis Model')
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
    <div class="mb-6 sm:mb-8 animate-fade-in-up">
        <a href="{{ route('dashboard') }}" class="text-blue-600 hover:text-blue-700 font-medium inline-flex items-center gap-1 mb-3 min-h-[44px]">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Dashboard
        </a>
        <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-900">Analisis Perbandingan Model</h1>
        <p class="text-gray-500 mt-1 text-sm sm:text-base">Transparansi hasil perbandingan tiga algoritma Machine Learning.</p>
    </div>

    @if(!$hasData)
    <div class="card-static p-12 text-center">
        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        <h3 class="text-lg font-semibold text-gray-500 mb-2">Belum Ada Data Analisis</h3>
        <p class="text-gray-400 mb-6">Isi kuesioner terlebih dahulu untuk mendapatkan hasil analisis model.</p>
        <a href="{{ route('kuesioner', ['step' => 1]) }}" class="btn-primary">Isi Kuesioner</a>
    </div>
    @else

    {{-- Section 1: Model Comparison Table --}}
    <div class="card-static overflow-hidden mb-8 animate-fade-in-up">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
            <h2 class="text-lg font-bold text-gray-900">Perbandingan Performa Model</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead><tr class="bg-gray-50 border-b border-gray-200">
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Model</th>
                    <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600">Akurasi</th>
                    <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600">Precision</th>
                    <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600">Recall</th>
                    <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600">F1-Score</th>
                    <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600">ROC-AUC</th>
                    <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600">Status</th>
                </tr></thead>
                <tbody>
                    @foreach($perbandinganModel as $name => $metrics)
                    <tr class="{{ $name === $modelTerbaik ? 'bg-green-50 border-l-4 border-green-500' : '' }} border-b border-gray-100 hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 font-semibold text-gray-900">{{ $name }}</td>
                        <td class="px-4 py-4 text-center text-sm">{{ number_format(($metrics['akurasi'] ?? 0) * 100, 1) }}%</td>
                        <td class="px-4 py-4 text-center text-sm">{{ number_format(($metrics['precision'] ?? 0) * 100, 1) }}%</td>
                        <td class="px-4 py-4 text-center text-sm">{{ number_format(($metrics['recall'] ?? 0) * 100, 1) }}%</td>
                        <td class="px-4 py-4 text-center text-sm font-bold">{{ number_format(($metrics['f1'] ?? 0) * 100, 1) }}%</td>
                        <td class="px-4 py-4 text-center text-sm">{{ number_format(($metrics['roc_auc'] ?? 0) * 100, 1) }}%</td>
                        <td class="px-4 py-4 text-center">
                            @if($name === $modelTerbaik)
                            <span class="inline-flex items-center gap-1 bg-green-100 text-green-700 text-xs font-bold px-2.5 py-1 rounded-full" title="Primary model untuk inferensi sentimen">⭐ Primary Model</span>
                            @else
                            <span class="text-xs text-gray-400">Comparison</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Section 2 & 3: Bar Chart + Best Model Card --}}
    <div class="grid md:grid-cols-2 gap-6 mb-8 stagger-children">
        <div class="card-static p-6">
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Perbandingan F1-Score</h3>
            <canvas id="f1Chart" height="200"></canvas>
        </div>
        <div class="card-static p-6 flex flex-col items-center justify-center text-center bg-gradient-to-br from-green-50 to-emerald-50 border-green-200">
            <div class="w-16 h-16 rounded-full bg-green-500 text-white flex items-center justify-center mb-4 shadow-lg">
                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            </div>
            <span class="bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full mb-2">⭐ Primary Model</span>
            <h3 class="text-2xl font-extrabold text-gray-900 mb-1">{{ $modelTerbaik }}</h3>
            <p class="text-3xl font-extrabold text-green-600 mb-2">F1: {{ number_format(($perbandinganModel[$modelTerbaik]['f1'] ?? 0) * 100, 1) }}%</p>
            <p class="text-sm text-gray-500 max-w-xs">Dipilih karena <strong>cepat, interpretable, dan stabil</strong> di dataset kecil. Cocok untuk inferensi real-time pada sentimen UMKM.</p>
        </div>
    </div>

    {{-- Section 4: Radar Chart --}}
    @if($skorPerAspek)
    <div class="card-static p-6 mb-8 animate-fade-in-up">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Hasil Analisis Aspek Bisnis Anda</h2>
        <div class="max-w-lg mx-auto">
            <canvas id="radarChart" height="300"></canvas>
        </div>
    </div>
    @endif

    {{-- Section 5: Issues Table --}}
    @if(count($isuTeridentifikasi) > 0)
    <div class="card-static overflow-hidden animate-fade-in-up">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
            <h2 class="text-lg font-bold text-gray-900">Isu Bisnis Teridentifikasi</h2>
            <p class="text-sm text-gray-500">Item dengan skor ≤ 2 yang memerlukan perhatian</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead><tr class="bg-gray-50 border-b border-gray-200">
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Aspek</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Isu</th>
                    <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600">Skor</th>
                    <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600">Tingkat Kritis</th>
                </tr></thead>
                <tbody>
                    @foreach($isuTeridentifikasi as $isu)
                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-700">{{ $isu['aspek'] }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $isu['item'] }}</td>
                        <td class="px-4 py-4 text-center text-sm font-bold">{{ $isu['skor'] }}/5</td>
                        <td class="px-4 py-4 text-center">
                            @if($isu['skor'] <= 1)
                            <span class="text-sm">🔴 Sangat Kritis</span>
                            @else
                            <span class="text-sm">🟠 Kritis</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
            <a href="{{ route('rekomendasi') }}" class="btn-primary text-sm inline-flex items-center gap-2">
                Lihat Rekomendasi AI →
            </a>
        </div>
    </div>
    @endif
    @endif
</div>

@push('scripts')
<script>
@if($hasData)

// Helper — handle key dengan huruf kapital ATAU lowercase
// (Flask kirim 'Operasional', fallback PHP kirim 'operasional')
function getAspekScore(data, key) {
    if (!data) return 0;
    const lower = key.toLowerCase();
    const upper = key.charAt(0).toUpperCase() + key.slice(1).toLowerCase();
    return data[upper] ?? data[lower] ?? data[key] ?? 0;
}

// F1-Score Bar Chart (dengan try-catch)
@if($perbandinganModel)
try {
    const f1Canvas = document.getElementById('f1Chart');
    if (!f1Canvas) {
        console.warn('[Analisis] Canvas #f1Chart tidak ditemukan');
    } else if (typeof Chart === 'undefined') {
        console.error('[Analisis] Chart.js library tidak ter-load');
        f1Canvas.parentElement.innerHTML += '<p class="text-red-600 text-sm">⚠ Chart.js gagal dimuat</p>';
    } else {
        const models = @json(array_keys($perbandinganModel));
        const f1Scores = @json(array_map(fn($m) => round(($m['f1'] ?? 0) * 100, 1), $perbandinganModel));
        const bestModel = '{{ $modelTerbaik }}';
        const barColors = models.map(m => m === bestModel ? '#22c55e' : '#94a3b8');

        new Chart(f1Canvas, {
            type: 'bar',
            data: {
                labels: models,
                datasets: [{
                    label: 'F1-Score (%)',
                    data: Object.values(f1Scores),
                    backgroundColor: barColors,
                    borderRadius: 8,
                    barThickness: 50
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: ctx => ctx.raw + '%' } }
                },
                scales: {
                    y: { beginAtZero: true, max: 100, ticks: { callback: v => v + '%' } }
                }
            }
        });
        console.log('[Analisis] F1 chart rendered');
    }
} catch (err) {
    console.error('[Analisis] F1 chart error:', err);
}
@endif

// Radar Chart Aspek (dengan try-catch + key normalization)
@if($skorPerAspek)
try {
    const radarCanvas = document.getElementById('radarChart');
    if (!radarCanvas) {
        console.warn('[Analisis] Canvas #radarChart tidak ditemukan');
    } else if (typeof Chart === 'undefined') {
        console.error('[Analisis] Chart.js library tidak ter-load');
    } else {
        const skorPerAspek = @json($skorPerAspek);
        console.log('[Analisis] skorPerAspek raw:', skorPerAspek);

        // Normalisasi key — terima 'Operasional' atau 'operasional'
        const radarData = [
            getAspekScore(skorPerAspek, 'operasional'),
            getAspekScore(skorPerAspek, 'pemasaran'),
            getAspekScore(skorPerAspek, 'keuangan'),
            getAspekScore(skorPerAspek, 'teknologi'),
            getAspekScore(skorPerAspek, 'tantangan'),
        ];
        console.log('[Analisis] radarData:', radarData);

        new Chart(radarCanvas, {
            type: 'radar',
            data: {
                labels: ['Operasional', 'Pemasaran', 'Keuangan', 'Teknologi', 'Tantangan'],
                datasets: [{
                    label: 'Skor Aspek Bisnis',
                    data: radarData,
                    backgroundColor: 'rgba(59, 130, 246, 0.15)',
                    borderColor: '#3b82f6',
                    borderWidth: 2,
                    pointBackgroundColor: '#3b82f6',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                }]
            },
            options: {
                responsive: true,
                scales: { r: { beginAtZero: true, max: 5, ticks: { stepSize: 1 } } },
                plugins: { legend: { position: 'bottom' } }
            }
        });
        console.log('[Analisis] Radar chart rendered');
    }
} catch (err) {
    console.error('[Analisis] Radar chart error:', err);
}
@endif

@endif
</script>
@endpush
@endsection
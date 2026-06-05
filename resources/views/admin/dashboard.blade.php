@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-10">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between mb-2">
        <div>
            <p class="text-sm font-semibold text-blue-600 uppercase tracking-wide">Admin</p>
            <h1 class="text-3xl font-bold text-gray-900">Admin Dashboard</h1>
            <p class="text-gray-500 mt-1">Monitor data SUS dan kuesioner lintas user.</p>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.dashboard') }}" class="card p-5 sm:p-6 grid grid-cols-1 md:grid-cols-4 gap-5 mb-8">
        <div class="md:col-span-2">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Search</label>
            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Nama, email, nama usaha, kategori" class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Dari tanggal</label>
            <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Sampai tanggal</label>
            <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div class="md:col-span-4 flex flex-wrap gap-2">
            <button type="submit" class="btn-primary px-4 py-2">Filter</button>
            <a href="{{ route('admin.dashboard') }}" class="btn-outline px-4 py-2">Reset</a>
        </div>
    </form>

    @php($query = request()->only(['search', 'from', 'to']))

    <section class="card overflow-hidden mb-8">
        <div class="p-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between border-b border-gray-100">
            <div>
                <h2 class="text-xl font-bold text-gray-900">SUS Responses</h2>
                <p class="text-sm text-gray-500">{{ $susResponses->total() }} data ditemukan</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.sus.export', ['format' => 'csv'] + $query) }}" class="btn-outline px-3 py-2 text-sm">Export CSV</a>
                <a href="{{ route('admin.sus.export', ['format' => 'xlsx'] + $query) }}" class="btn-primary px-3 py-2 text-sm">Export XLSX</a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 text-sm">
                <thead class="bg-gray-50 text-left text-xs font-bold uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="px-4 py-3">User</th>
                        <th class="px-4 py-3">Skor</th>
                        <th class="px-4 py-3">Grade</th>
                        @for($i = 1; $i <= 10; $i++)<th class="px-3 py-3">S{{ $i }}</th>@endfor
                        <th class="px-4 py-3">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($susResponses as $response)
                        <tr>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="font-semibold text-gray-900">{{ $response->user?->name ?? 'User #' . $response->user_id }}</div>
                                <div class="text-gray-500">{{ $response->user?->email ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-3 font-semibold">{{ number_format((float) $response->skor_sus, 1) }}</td>
                            <td class="px-4 py-3">{{ $response->grade }}<div class="text-gray-500">{{ $response->grade_label }}</div></td>
                            @for($i = 1; $i <= 10; $i++)<td class="px-3 py-3 text-center">{{ $response->{'sus_' . $i} }}</td>@endfor
                            <td class="px-4 py-3 whitespace-nowrap">{{ $response->created_at?->timezone('Asia/Jakarta')->format('d/m/Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="14" class="px-4 py-8 text-center text-gray-500">Belum ada data SUS.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">{{ $susResponses->links() }}</div>
    </section>

    <section class="card overflow-hidden">
        <div class="p-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between border-b border-gray-100">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Kuesioner Responses</h2>
                <p class="text-sm text-gray-500">{{ $kuesionerResponses->total() }} data ditemukan</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.kuesioner.export', ['format' => 'csv'] + $query) }}" class="btn-outline px-3 py-2 text-sm">Export CSV</a>
                <a href="{{ route('admin.kuesioner.export', ['format' => 'xlsx'] + $query) }}" class="btn-primary px-3 py-2 text-sm">Export XLSX</a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 text-sm">
                <thead class="bg-gray-50 text-left text-xs font-bold uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="px-4 py-3">User</th>
                        <th class="px-4 py-3">Usaha</th>
                        <th class="px-4 py-3">Aspek</th>
                        <th class="px-4 py-3">Sentimen</th>
                        <th class="px-4 py-3">Confidence</th>
                        <th class="px-4 py-3">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($kuesionerResponses as $response)
                        @php($scores = $response->getSkorPerAspek())
                        <tr>
                            <td class="px-4 py-3 whitespace-nowrap"><div class="font-semibold text-gray-900">{{ $response->user?->name ?? 'User #' . $response->user_id }}</div><div class="text-gray-500">{{ $response->user?->email ?? '-' }}</div></td>
                            <td class="px-4 py-3"><div>{{ $response->user?->nama_usaha ?? '-' }}</div><div class="text-gray-500">{{ $response->user?->kategori_usaha ?? '-' }}</div></td>
                            <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-600">Ops {{ $scores['operasional'] }} · Pem {{ $scores['pemasaran'] }} · Keu {{ $scores['keuangan'] }} · Tek {{ $scores['teknologi'] }} · Tan {{ $scores['tantangan'] }}</td>
                            <td class="px-4 py-3">{{ $response->sentimen ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $response->confidence !== null ? number_format((float) $response->confidence, 2) : '-' }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">{{ $response->created_at?->timezone('Asia/Jakarta')->format('d/m/Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">Belum ada data kuesioner.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">{{ $kuesionerResponses->links() }}</div>
    </section>
</div>
@endsection

@extends('layouts.app')
@section('title', $module['judul'])
@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
    {{-- Back button --}}
    <a href="{{ route('modul') }}" class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-700 font-medium text-sm mb-4 sm:mb-6 min-h-[44px]">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali ke Modul
    </a>

    <div class="flex flex-col lg:flex-row gap-6">
        {{-- Video Column --}}
        <div class="w-full lg:w-3/5">
            <div id="video-container" class="aspect-video w-full rounded-xl overflow-hidden bg-black shadow-lg">
                <iframe id="youtube-player"
                    src="https://www.youtube.com/embed/{{ $module['youtube_id'] }}?enablejsapi=1&origin={{ url('/') }}"
                    class="w-full h-full" allowfullscreen
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"></iframe>
            </div>
            <div class="mt-4">
                <h1 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-900">{{ $module['judul'] }}</h1>
                <div class="flex items-center gap-3 mt-2 flex-wrap">
                    @php
                        $catColors = [
                            'Operasional' => 'bg-blue-100 text-blue-700',
                            'Pemasaran'   => 'bg-green-100 text-green-700',
                            'Keuangan'    => 'bg-yellow-100 text-yellow-700',
                            'Teknologi'   => 'bg-purple-100 text-purple-700',
                            'SDM'         => 'bg-pink-100 text-pink-700',
                        ];
                        $tingkatColors = [
                            'Pemula'   => 'bg-green-50 text-green-600',
                            'Menengah' => 'bg-yellow-50 text-yellow-600',
                            'Lanjutan' => 'bg-red-50 text-red-600',
                        ];
                    @endphp
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $catColors[$module['kategori']] ?? 'bg-gray-100 text-gray-700' }}">{{ $module['kategori'] }}</span>
                    @if(!empty($module['tingkat']))
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $tingkatColors[$module['tingkat']] ?? 'bg-gray-100 text-gray-700' }}">{{ $module['tingkat'] }}</span>
                    @endif
                    @if(!empty($module['durasi']))
                    <span class="text-xs text-gray-500 inline-flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ $module['durasi'] }}
                    </span>
                    @endif
                    @if(!empty($module['creator']))
                    <span class="text-xs text-gray-500">oleh {{ $module['creator'] }}</span>
                    @endif
                </div>
                @if(!empty($module['deskripsi']))
                <p class="text-sm text-gray-600 mt-3 leading-relaxed">{{ $module['deskripsi'] }}</p>
                @endif
            </div>
        </div>

        {{-- Transcript Column --}}
        <div class="w-full lg:w-2/5">
            <div class="card-static overflow-hidden">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="font-bold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Transcript
                    </h2>
                    @if(!empty($module['transcript']))
                        @php $src = $module['transcript_source'] ?? 'curated_outline'; @endphp
                        @if($src === 'official_chapter')
                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-green-100 text-green-700 inline-flex items-center gap-1" title="Chapter resmi dari creator video">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                Chapter Resmi
                            </span>
                        @else
                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-blue-100 text-blue-700" title="Outline pembagian topik per bagian video">Outline Materi</span>
                        @endif
                    @endif
                </div>
                <div id="transcript-container" class="overflow-y-auto max-h-[300px] lg:max-h-[500px] p-4">
                    @if(!empty($module['transcript']) && is_array($module['transcript']))
                        {{-- Render transcript langsung dari config (tidak ada AJAX) --}}
                        <div class="space-y-1">
                            @foreach($module['transcript'] as $line)
                                <div class="transcript-row flex gap-3 px-2 py-2 rounded-lg cursor-pointer hover:bg-gray-100 transition group"
                                     data-time="{{ $line['time'] }}"
                                     onclick="seekTo('{{ $line['time'] }}')">
                                    <button type="button"
                                            class="text-blue-500 text-xs font-mono flex-shrink-0 mt-0.5 hover:text-blue-700 hover:underline font-semibold min-w-[40px] bg-transparent border-none cursor-pointer p-0"
                                            onclick="seekTo('{{ $line['time'] }}'); event.stopPropagation()"
                                            title="Klik untuk loncat ke waktu ini">
                                        {{ $line['time'] }}
                                    </button>
                                    <p class="text-sm text-gray-700 leading-relaxed group-hover:text-gray-900">{{ $line['text'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        {{-- Empty state — transcript tidak tersedia untuk modul ini --}}
                        <div class="text-center py-8">
                            <svg class="w-10 h-10 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                            <p class="text-sm text-gray-400">Transcript tidak tersedia untuk modul ini.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.transcript-row.active-row {
    background: #eff6ff;
    border-left: 2px solid #3b82f6;
}
</style>

{{-- YouTube IFrame API untuk seek-to-time saat klik timestamp --}}
<script src="https://www.youtube.com/iframe_api"></script>

@push('scripts')
<script>

// YouTube Player API — seek + auto-highlight saat playing

let player;
let trackingInterval = null;

function onYouTubeIframeAPIReady() {
    player = new YT.Player('youtube-player', {
        events: {
            'onReady': function() { console.log('[Modul] YouTube Player ready'); },
            'onStateChange': onPlayerStateChange,
        }
    });
}

function timeToSeconds(timeStr) {
    const parts = timeStr.split(':').map(Number);
    if (parts.length === 3) return parts[0] * 3600 + parts[1] * 60 + parts[2];
    if (parts.length === 2) return parts[0] * 60 + parts[1];
    return 0;
}

function seekTo(timeStr) {
    if (player && typeof player.seekTo === 'function') {
        const seconds = timeToSeconds(timeStr);
        player.seekTo(seconds, true);
        if (typeof player.playVideo === 'function') player.playVideo();
        document.getElementById('video-container').scrollIntoView({ behavior: 'smooth', block: 'start' });
        highlightTranscript(timeStr);
    } else {
        console.warn('[Modul] YouTube Player belum ready');
    }
}

function highlightTranscript(activeTime) {
    document.querySelectorAll('.transcript-row').forEach(row => {
        row.classList.remove('active-row');
    });
    const activeRow = document.querySelector(`.transcript-row[data-time="${activeTime}"]`);
    if (activeRow) {
        activeRow.classList.add('active-row');
        activeRow.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
}

function onPlayerStateChange(event) {
    if (event.data === YT.PlayerState.PLAYING) {
        // Auto-highlight transcript row sesuai posisi video
        if (trackingInterval) clearInterval(trackingInterval);
        trackingInterval = setInterval(() => {
            if (!player || typeof player.getCurrentTime !== 'function') return;
            const currentTime = player.getCurrentTime();
            const rows = document.querySelectorAll('.transcript-row');
            let activeRow = null;
            rows.forEach(row => {
                const rowTime = timeToSeconds(row.dataset.time);
                if (rowTime <= currentTime) activeRow = row;
            });
            if (activeRow) {
                rows.forEach(r => r.classList.remove('active-row'));
                activeRow.classList.add('active-row');
            }
        }, 1000);
    } else if (event.data === YT.PlayerState.PAUSED || event.data === YT.PlayerState.ENDED) {
        if (trackingInterval) {
            clearInterval(trackingInterval);
            trackingInterval = null;
        }
    }
}
</script>
@endpush
@endsection
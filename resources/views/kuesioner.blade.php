@extends('layouts.app')
@section('title', 'Kuesioner - Halaman ' . $step)
@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
    {{-- Back to Dashboard with confirmation --}}
    <button type="button" id="backToDashboardBtn" class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-700 font-medium text-sm mb-3 min-h-[44px] cursor-pointer bg-transparent border-none">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali ke Dashboard
    </button>

    <div class="mb-5 sm:mb-6 animate-fade-in-up">
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Isi Kuesioner Kondisi Bisnis Anda</h1>
        <p class="text-gray-500 mt-1 text-sm sm:text-base">Silakan nilai kondisi bisnis Anda secara jujur untuk mendapatkan analisis yang akurat.</p>
    </div>

    {{-- Progress Steps --}}
    <div class="mb-6 sm:mb-8">
        <div class="flex items-center justify-between mb-2 overflow-x-auto pb-1">
            @php $stepLabels = ['Operasional','Pemasaran','Keuangan','Teknologi','Tantangan']; @endphp
            @for($i = 1; $i <= 5; $i++)
            <div class="flex items-center {{ $i < 5 ? 'flex-1' : '' }}">
                <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full flex items-center justify-center text-xs sm:text-sm font-bold transition-all duration-300 flex-shrink-0
                    {{ $i < $step ? 'bg-green-500 text-white' : ($i == $step ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-500') }}">
                    @if($i < $step)
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    @else
                        {{ $i }}
                    @endif
                </div>
                @if($i < 5)
                <div class="flex-1 h-1 mx-1 sm:mx-2 rounded {{ $i < $step ? 'bg-green-500' : 'bg-gray-200' }}"></div>
                @endif
            </div>
            @endfor
        </div>
        <div class="flex justify-between text-[10px] sm:text-xs text-gray-400 px-1">
            @foreach($stepLabels as $label)
            <span class="text-center min-w-[50px] sm:min-w-0">{{ $label }}</span>
            @endforeach
        </div>
    </div>

    {{-- Questionnaire Card --}}
    <div class="card-static overflow-hidden animate-fade-in-up">
        <div class="bg-gradient-to-r from-[#1e2d4a] to-[#2a3f5f] px-4 sm:px-6 py-4 sm:py-5">
            <h2 class="text-base sm:text-lg font-bold text-white">{{ $currentStep['title'] }}</h2>
            <p class="text-blue-200 text-sm mt-1">Halaman {{ $step }} dari 5</p>
        </div>

        <div class="bg-blue-50 border-b border-blue-100 px-4 sm:px-6 py-3 sm:py-4">
            <h3 class="text-sm font-semibold text-blue-800 mb-2">Tingkat Kondisi Bisnis (Skala 1-5) + Opini</h3>
            <p class="text-xs sm:text-sm text-blue-700 mb-2">Berikan penilaian skala 1-5 <strong>dan</strong> tuliskan opini Anda untuk setiap pertanyaan.</p>
            <div class="grid grid-cols-5 gap-1 sm:gap-2 text-[10px] sm:text-xs text-blue-600">
                <span>1 = Sangat Buruk</span><span>2 = Buruk</span><span>3 = Cukup</span><span>4 = Baik</span><span>5 = Sangat Baik</span>
            </div>
        </div>

        {{-- Toast error --}}
        <div id="validationToast" class="hidden mx-4 sm:mx-6 mt-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm flex items-center gap-2">
            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
            <span>Mohon isi semua pertanyaan sebelum melanjutkan.</span>
        </div>
        
        @if($errors->any())
        <div class="mx-4 sm:mx-6 mt-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
            <div class="flex items-start gap-2">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <div class="flex-1">
                    <p class="font-semibold mb-2">⚠ Ada {{ count($errors->all()) }} field yang belum lengkap:</p>
                    <ul class="list-disc list-inside space-y-1 text-xs">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif

        <form method="POST" action="{{ route('kuesioner.store', ['step' => $step]) }}" id="kuesionerForm">
            @csrf

            {{--
                ═══════════════════════════════════════════════════════
                ROOT CAUSE FIX:
                Hidden input sebelumnya hanya di-render di dalam blok
                `.hidden.md:block` (desktop-only). Di layar mobile,
                elemen itu tidak ter-render sama sekali sehingga:
                  1. Nilai rating tidak ikut di-POST → validasi PHP gagal
                  2. JS validasi `getElementById('input_'+qName)` return null
                     → `hasRating` selalu false → e.preventDefault() dipanggil
                     → form tidak pernah submit.

                FIX: Pindahkan hidden input KE LUAR kedua layout agar
                selalu ada di DOM, satu kali, tidak peduli ukuran layar.
                ═══════════════════════════════════════════════════════
            --}}
            @foreach($currentStep['questions'] as $key => $question)
                <input type="hidden"
                       name="{{ $key }}"
                       id="input_{{ $key }}"
                       value="{{ old($key, $savedAnswers[$key] ?? '') }}">
            @endforeach

            {{-- Desktop: Card per question --}}
            <div class="hidden md:block px-6 py-4 space-y-4">
                @foreach($currentStep['questions'] as $key => $question)
                <div data-question="{{ $key }}" id="q-{{ $key }}" class="question-row border rounded-xl p-5 bg-white transition-all hover:shadow-sm">
                    <p class="text-sm font-medium text-gray-800 mb-3">
                        <span class="text-gray-400 mr-2">{{ $loop->iteration }}.</span>{{ $question }}
                    </p>
                    {{-- Radio visual only — nilai disimpan ke hidden input di atas --}}
                    <div class="flex justify-between items-center px-4 mb-2">
                        @for($val = 1; $val <= 5; $val++)
                        <div class="custom-radio-btn" data-name="{{ $key }}" data-value="{{ $val }}"
                             style="display:flex;flex-direction:column;align-items:center;gap:4px;cursor:pointer;">
                            <span class="radio-circle {{ (old($key, $savedAnswers[$key] ?? '') == $val) ? 'selected' : '' }}"
                                  style="display:inline-block;width:22px;height:22px;border-radius:50%;border:2px solid #d1d5db;transition:all 0.15s ease;{{ (old($key, $savedAnswers[$key] ?? '') == $val) ? 'border-color:#2563eb;background:radial-gradient(circle,#2563eb 40%,transparent 41%);' : '' }}"></span>
                            <span style="font-size:12px;color:#6b7280;font-weight:500;">{{ $val }}</span>
                        </div>
                        @endfor
                    </div>
                    <div class="flex justify-between text-[10px] text-gray-400 mb-4 px-3">
                        <span>Sangat Buruk</span><span>Sangat Baik</span>
                    </div>
                    <label class="text-sm text-blue-700 font-medium flex items-center gap-1 mb-1">
                        💬 Tuliskan pendapat Anda:
                        <span class="text-red-500 text-xs">(wajib)</span>
                    </label>
                    <textarea name="opini_{{ $key }}" rows="2" minlength="10"
                        placeholder="Ceritakan kondisi bisnis Anda terkait hal ini..."
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm resize-none focus:border-blue-400 focus:outline-none focus:ring-1 focus:ring-blue-200 transition-colors">{{ old("opini_{$key}", $savedOpini[$key] ?? '') }}</textarea>
                    <p class="text-xs text-gray-400 mt-1">Minimal 10 karakter</p>
                </div>
                @endforeach
            </div>

            {{-- Mobile: Card per question --}}
            <div class="block md:hidden px-4 py-4 space-y-3">
                @foreach($currentStep['questions'] as $key => $question)
                <div data-question="{{ $key }}" id="q-mobile-{{ $key }}" class="question-row bg-gray-50 rounded-xl p-4 transition-all">
                    <p class="text-sm font-medium text-gray-800 mb-3">
                        <span class="text-gray-400 mr-1">{{ $loop->iteration }}.</span> {{ $question }}
                    </p>
                    <div class="flex justify-between items-center px-2">
                        @for($val = 1; $val <= 5; $val++)
                        <div class="custom-radio-btn" data-name="{{ $key }}" data-value="{{ $val }}"
                             style="display:flex;flex-direction:column;align-items:center;gap:4px;cursor:pointer;">
                            <span class="radio-circle {{ (old($key, $savedAnswers[$key] ?? '') == $val) ? 'selected' : '' }}"
                                  style="display:inline-block;width:22px;height:22px;border-radius:50%;border:2px solid #d1d5db;transition:all 0.15s ease;{{ (old($key, $savedAnswers[$key] ?? '') == $val) ? 'border-color:#2563eb;background:radial-gradient(circle,#2563eb 40%,transparent 41%);' : '' }}"></span>
                            <span style="font-size:12px;color:#6b7280;font-weight:500;">{{ $val }}</span>
                        </div>
                        @endfor
                    </div>
                    <div class="flex justify-between text-[10px] text-gray-400 mt-2 mb-3 px-1">
                        <span>Sangat Buruk</span><span>Sangat Baik</span>
                    </div>
                    <label class="text-xs text-blue-700 font-medium flex items-center gap-1 mb-1">
                        💬 Pendapat Anda: <span class="text-red-500 text-xs">(wajib)</span>
                    </label>
                    <textarea name="opini_{{ $key }}" rows="2" minlength="10"
                        placeholder="Ceritakan kondisi bisnis Anda..."
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm resize-none focus:border-blue-400 focus:outline-none">{{ old("opini_{$key}", $savedOpini[$key] ?? '') }}</textarea>
                    <p class="text-xs text-gray-400 mt-1">Minimal 10 karakter</p>
                </div>
                @endforeach
            </div>

            {{-- Navigation --}}
            <div class="flex items-center justify-between px-4 sm:px-6 py-4 sm:py-5 bg-gray-50 border-t border-gray-100">
                @if($step > 1)
                <a href="{{ route('kuesioner', ['step' => $step - 1]) }}" class="btn-outline inline-flex items-center gap-2 min-h-[44px]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Kembali
                </a>
                @else
                <div></div>
                @endif

                @if($step < 5)
                <button type="submit" id="btn-next" class="btn-primary inline-flex items-center gap-2 min-h-[44px]">
                    Lanjutkan
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
                @else
                <button type="submit" id="btn-submit" class="btn-primary inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 min-h-[44px]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Kirim Kuesioner
                </button>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- Confirm Leave Dialog --}}
<div id="confirmLeaveDialog" class="hidden fixed inset-0 z-[70] flex items-center justify-center bg-black/50 backdrop-blur-sm">
    <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-2xl max-w-sm mx-4 animate-scale-in">
        <div class="text-center mb-5">
            <div class="w-14 h-14 mx-auto mb-3 rounded-full bg-yellow-100 flex items-center justify-center">
                <svg class="w-7 h-7 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-1">Keluar dari Kuesioner?</h3>
            <p class="text-sm text-gray-500">Apakah Anda yakin ingin keluar? Jawaban yang sudah diisi akan hilang.</p>
        </div>
        <div class="flex gap-3">
            <button id="cancelLeaveBtn" type="button" class="flex-1 btn-outline text-sm py-2.5 min-h-[44px]">Batal</button>
            <a href="{{ route('dashboard') }}" class="flex-1 btn-primary text-sm py-2.5 min-h-[44px] text-center">Ya, Kembali</a>
        </div>
    </div>
</div>

{{-- Loading Overlay --}}
<div id="loadingOverlay" class="loading-overlay">
    <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-2xl text-center max-w-sm mx-4">
        <div class="loading-spinner mx-auto mb-4"></div>
        <h3 class="text-lg font-bold text-gray-900 mb-2">Memproses Analisis...</h3>
        <p class="text-sm text-gray-500">Penilaian bisnis Anda berhasil disimpan! Sistem sedang menganalisis kondisi bisnis Anda...</p>
        <p class="text-xs text-gray-400 mt-2">Estimasi 3-10 detik</p>
    </div>
</div>

@push('styles')
<style>
@keyframes shake {
    0%, 100% { transform: translateX(0); }
    10%, 30%, 50%, 70%, 90% { transform: translateX(-4px); }
    20%, 40%, 60%, 80% { transform: translateX(4px); }
}
.shake-error { animation: shake 0.5s ease-in-out; }
.char-counter { transition: color 0.2s; }
.char-counter.ok { color: #22c55e; }
.char-counter.warn { color: #ef4444; }
</style>
@endpush

@push('scripts')
<script>
// Custom Radio Button Handler 
document.querySelectorAll('.custom-radio-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const name  = this.dataset.name;
        const value = this.dataset.value;

        // Update satu hidden input tunggal (selalu ada, di luar layout)
        const hiddenInput = document.getElementById('input_' + name);
        if (hiddenInput) hiddenInput.value = value;

        // Update visual semua circle dengan name yang sama (desktop + mobile)
        document.querySelectorAll(`.custom-radio-btn[data-name="${name}"]`).forEach(sibling => {
            const circle = sibling.querySelector('.radio-circle');
            if (circle) {
                circle.classList.remove('selected');
                circle.style.borderColor = '#d1d5db';
                circle.style.background  = 'none';
            }
        });
        const myCircle = this.querySelector('.radio-circle');
        if (myCircle) {
            myCircle.classList.add('selected');
            myCircle.style.borderColor = '#2563eb';
            myCircle.style.background  = 'radial-gradient(circle, #2563eb 40%, transparent 41%)';
        }

        // Clear error state untuk question ini
        document.querySelectorAll(`.question-row[data-question="${name}"]`).forEach(row => {
            row.classList.remove('border-red-400', 'bg-red-50', 'shake-error');
            row.querySelectorAll('.error-msg-rating').forEach(msg => msg.remove());
        });
    });
});

// Textarea character counter + sinkronisasi desktop↔mobile 
document.querySelectorAll('textarea[name^="opini_"]').forEach(ta => {
    // Cegah counter ganda (textarea muncul 2x: desktop + mobile)
    if (ta.dataset.counterAttached) return;
    ta.dataset.counterAttached = 'true';

    const counter   = document.createElement('span');
    const initLen   = ta.value.trim().length;
    counter.className   = 'char-counter text-xs ' + (initLen >= 10 ? 'ok' : 'warn');
    counter.textContent = `${initLen}/10`;
    const helpText = ta.nextElementSibling;
    if (helpText && helpText.classList.contains('text-gray-400')) {
        helpText.appendChild(document.createTextNode(' — '));
        helpText.appendChild(counter);
    }

    ta.addEventListener('input', function() {
        // Sinkronkan nilai ke textarea pasangannya (desktop ↔ mobile)
        document.querySelectorAll(`textarea[name="${this.name}"]`).forEach(other => {
            if (other !== this) other.value = this.value;
        });

        const len = this.value.trim().length;
        counter.textContent = `${len}/10`;
        counter.className   = 'char-counter text-xs ' + (len >= 10 ? 'ok' : 'warn');

        if (len >= 10) {
            const block = this.closest('.question-row');
            if (block) {
                block.querySelectorAll('.error-msg-opini').forEach(msg => msg.remove());
                const qName = block.getAttribute('data-question');
                const inp   = document.getElementById('input_' + qName);
                if (inp && inp.value !== '') {
                    block.classList.remove('border-red-400', 'bg-red-50', 'shake-error');
                }
            }
        }
    });
});

// Form Submit Validation 
document.getElementById('kuesionerForm')?.addEventListener('submit', function(e) {
    const step  = {{ $step }};
    const toast = document.getElementById('validationToast');
    let isValid    = true;
    let errorCount = 0;

    // Daftar question keys dari server (bukan dari DOM → tidak bergantung layout)
    const questionNames = @json(array_keys($currentStep['questions']));

    // Clear error sebelumnya
    document.querySelectorAll('.question-row').forEach(row => {
        row.classList.remove('border-red-400', 'bg-red-50', 'shake-error');
        row.querySelectorAll('.error-msg-rating, .error-msg-opini').forEach(msg => msg.remove());
    });

    questionNames.forEach(qName => {
        // Baca dari hidden input tunggal yang selalu ada
        const hiddenInp = document.getElementById('input_' + qName);
        const hasRating = hiddenInp && hiddenInp.value !== '';

        // Ambil nilai opini terpanjang dari semua textarea dengan name ini
        let opiniValue = '';
        document.querySelectorAll(`textarea[name="opini_${qName}"]`).forEach(ta => {
            if (ta.value.trim().length > opiniValue.length) opiniValue = ta.value.trim();
        });
        const hasOpini = opiniValue.length >= 10;

        if (!hasRating || !hasOpini) {
            isValid = false;
            errorCount++;

            // Highlight row yang sedang visible
            document.querySelectorAll(`.question-row[data-question="${qName}"]`).forEach(row => {
                if (row.offsetParent === null) return; // skip yang hidden oleh CSS

                row.classList.add('border-red-400', 'bg-red-50', 'shake-error');
                setTimeout(() => row.classList.remove('shake-error'), 600);

                if (!hasRating && !row.querySelector('.error-msg-rating')) {
                    const msg = document.createElement('p');
                    msg.className   = 'error-msg-rating text-red-500 text-xs mt-1 pl-2';
                    msg.textContent = '⚠ Pilih skala penilaian (1-5)';
                    const labelEl = row.querySelector('label');
                    if (labelEl) row.insertBefore(msg, labelEl);
                    else row.prepend(msg);
                }
                if (!hasOpini && !row.querySelector('.error-msg-opini')) {
                    const msg = document.createElement('p');
                    msg.className   = 'error-msg-opini text-red-500 text-xs mt-1';
                    msg.textContent = `⚠ Isi opini minimal 10 karakter (${opiniValue.length}/10)`;
                    const textareaHelp = row.querySelector('p.text-gray-400');
                    if (textareaHelp) textareaHelp.after(msg);
                    else row.appendChild(msg);
                }
            });
        }
    });

    if (!isValid) {
        e.preventDefault();
        toast.classList.remove('hidden');
        toast.querySelector('span').textContent = `Mohon lengkapi ${errorCount} pertanyaan yang belum terisi.`;
        const firstErr = document.querySelector('.question-row.border-red-400');
        if (firstErr) firstErr.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return;
    }

    toast.classList.add('hidden');

    // Loading overlay hanya di step terakhir
    if (step >= 5) {
        document.getElementById('loadingOverlay').classList.add('active');
    }
});

// Back to Dashboard confirmation 
document.getElementById('backToDashboardBtn')?.addEventListener('click', function() {
    document.getElementById('confirmLeaveDialog').classList.remove('hidden');
});
document.getElementById('cancelLeaveBtn')?.addEventListener('click', function() {
    document.getElementById('confirmLeaveDialog').classList.add('hidden');
});
document.getElementById('confirmLeaveDialog')?.addEventListener('click', function(e) {
    if (e.target === this) this.classList.add('hidden');
});
</script>
@endpush
@endsection
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\KuesionerResponse;
use App\Models\MlResult;
use App\Models\AiRecommendation;

class KuesionerController extends Controller
{
    private function getSteps(): array
    {
        return [
            1 => [
                'title' => 'Bagian A: Operasional & Produk',
                'questions' => [
                    'kualitas_produk' => 'Seberapa baik kualitas produk atau layanan yang Anda hasilkan saat ini?',
                    'efisiensi_operasional' => 'Seberapa lancar proses operasional usaha Anda sehari-hari?',
                    'penuhi_permintaan' => 'Seberapa mampu usaha Anda memenuhi permintaan atau pesanan dari pelanggan?',
                    'kualitas_sdm' => 'Seberapa terampil karyawan atau tim Anda dalam menjalankan usaha?',
                ],
            ],
            2 => [
                'title' => 'Bagian B: Pemasaran & Hubungan Pelanggan',
                'questions' => [
                    'efektivitas_pemasaran' => 'Seberapa efektif cara pemasaran yang Anda lakukan saat ini?',
                    'pemasaran_digital' => 'Seberapa mampu Anda memasarkan produk secara online (media sosial, marketplace, dll)?',
                    'kepuasan_pelanggan' => 'Seberapa baik tingkat repeat customer dan minimnya komplain dari pelanggan?',
                    'jangkauan_pasar' => 'Seberapa luas jangkauan pasar dan dikenalnya usaha Anda oleh masyarakat?',
                ],
            ],
            3 => [
                'title' => 'Bagian C: Keuangan & Akses Modal',
                'questions' => [
                    'kelola_cashflow' => 'Seberapa baik Anda dalam mengelola uang masuk dan keluar usaha?',
                    'akses_modal' => 'Seberapa mudah Anda mendapatkan tambahan modal usaha jika dibutuhkan?',
                    'harga_keuntungan' => 'Seberapa baik Anda dalam menentukan harga jual agar tetap mendapat keuntungan?',
                ],
            ],
            4 => [
                'title' => 'Bagian D: Teknologi & Digitalisasi',
                'questions' => [
                    'teknologi_operasional' => 'Seberapa sering Anda menggunakan teknologi (HP, komputer, aplikasi) untuk menjalankan usaha?',
                    'aplikasi_bisnis' => 'Seberapa mampu Anda menggunakan aplikasi pencatatan keuangan atau stok barang?',
                    'kesiapan_teknologi' => 'Seberapa siap usaha Anda untuk beralih menggunakan teknologi digital?',
                ],
            ],
            5 => [
                'title' => 'Bagian E: Tantangan & Kebutuhan UMKM',
                'questions' => [
                    'kesulitan_usaha' => 'Seberapa besar kesulitan yang Anda rasakan dalam menjalankan usaha sehari-hari?',
                    'kebutuhan_pelatihan' => 'Seberapa besar kebutuhan Anda terhadap pelatihan atau bimbingan usaha?',
                    'strategi_jangka_panjang' => 'Seberapa jelas rencana dan arah pengembangan usaha Anda ke depan?',
                ],
            ],
        ];
    }

    public function show(Request $request, $step = 1)
    {
        $step = (int) $step;
        if ($step < 1 || $step > 5) {
            return redirect()->route('kuesioner', ['step' => 1]);
        }

        $steps = $this->getSteps();
        $currentStep = $steps[$step];
        $savedAnswers = $request->session()->get('kuesioner', []);
        $savedOpini = $request->session()->get('kuesioner_opini', []);

        return view('kuesioner', compact('step', 'currentStep', 'savedAnswers', 'savedOpini'));
    }

    public function storeStep(Request $request, $step)
    {
        $step = (int) $step;
        $steps = $this->getSteps();

        if (!isset($steps[$step])) {
            return redirect()->route('kuesioner', ['step' => 1]);
        }

        $questions = array_keys($steps[$step]['questions']);
        $rules = [];
        $messages = [];

        foreach ($questions as $q) {
            $rules[$q] = 'required|integer|min:1|max:5';
            $rules["opini_{$q}"] = 'required|string|min:10';

            $label = $steps[$step]['questions'][$q];
            $messages["{$q}.required"] = "Mohon pilih nilai untuk: {$label}";
            $messages["{$q}.integer"] = "Jawaban harus berupa angka 1-5 untuk: {$label}";
            $messages["{$q}.min"] = "Jawaban minimal 1 untuk: {$label}";
            $messages["{$q}.max"] = "Jawaban maksimal 5 untuk: {$label}";
            $messages["opini_{$q}.required"] = "Mohon isi opini untuk: {$label}";
            $messages["opini_{$q}.min"] = "Opini minimal 10 karakter untuk: {$label}";
        }

        $request->validate($rules, $messages);

        $kuesioner = $request->session()->get('kuesioner', []);
        foreach ($questions as $q) {
            $kuesioner[$q] = (int) $request->input($q);
        }
        $request->session()->put('kuesioner', $kuesioner);

        $opini = $request->session()->get('kuesioner_opini', []);
        foreach ($questions as $q) {
            $opini[$q] = $request->input("opini_{$q}", '');
        }
        $request->session()->put('kuesioner_opini', $opini);

        if ($step >= 5) {
            return $this->submit($request);
        }

        return redirect()->route('kuesioner', ['step' => $step + 1]);
    }

    private function submit(Request $request)
    {
        $user = Auth::user();
        $answers = $request->session()->get('kuesioner', []);
        $opiniTexts = $request->session()->get('kuesioner_opini', []);

        // Calculate average Likert
        $allScores = array_values($answers);
        $rataRata = count($allScores) > 0 ? round(array_sum($allScores) / count($allScores), 2) : 0;

        // Fallback sentimen 3 kelas, selaras dengan ambang pelabelan model (2.7 / 3.7)
        $sentimen = $rataRata >= 3.7 ? 'Positif' : ($rataRata >= 2.7 ? 'Netral' : 'Negatif');
        $confidence = $rataRata >= 4 ? 0.92 : ($rataRata >= 3 ? 0.78 : ($rataRata >= 2 ? 0.85 : 0.91));

        // Try calling Flask ML service (both pipelines)
        $mlResponse = $this->callFlaskML($user->id, $answers, $opiniTexts);

        if ($mlResponse) {
            $sentimen = $mlResponse['sentimen'];
            $confidence = $mlResponse['confidence'];
            $rataRata = $mlResponse['rata_rata_likert'];
        }

        // Build data array — ratings + opini
        $saveData = array_merge($answers, [
            'user_id' => $user->id,
            'rata_rata_likert' => $rataRata,
            'sentimen' => $sentimen,
            'confidence' => $confidence,
        ]);

        foreach ($opiniTexts as $key => $text) {
            $saveData["opini_{$key}"] = $text;
        }

        if ($mlResponse) {
            $saveData['nlp_topics'] = $mlResponse['integrated'] ?? null;
            $saveData['nlp_edges'] = $mlResponse['pipeline_b']['edges'] ?? null;
            $saveData['integrated_result'] = $mlResponse['integrated'] ?? null;
        }

        $kuesioner = KuesionerResponse::create($saveData);

        $perbandinganModel = $mlResponse['perbandingan_model'] ?? $this->getDemoModelComparison();
        $skorPerAspek = $mlResponse['skor_per_aspek'] ?? $kuesioner->getSkorPerAspek();
        $isuTeridentifikasi = $mlResponse['isu_teridentifikasi'] ?? $kuesioner->getIsuTeridentifikasi();
        $modelTerbaik = $mlResponse['model_terbaik'] ?? 'Logistic Regression';

        MlResult::create([
            'user_id' => $user->id,
            'kuesioner_response_id' => $kuesioner->id,
            'model_terbaik' => $modelTerbaik,
            'perbandingan_model' => $perbandinganModel,
            'skor_per_aspek' => $skorPerAspek,
            'isu_teridentifikasi' => $isuTeridentifikasi,
        ]);

        AiRecommendation::where('user_id', $user->id)->delete();

        $labelToOpiniKey = [
            'Kualitas Produk/Layanan' => 'kualitas_produk',
            'Efisiensi Proses Operasional' => 'efisiensi_operasional',
            'Kemampuan Memenuhi Permintaan' => 'penuhi_permintaan',
            'Kualitas SDM/Karyawan' => 'kualitas_sdm',
            'Efektivitas Strategi Pemasaran' => 'efektivitas_pemasaran',
            'Kemampuan Pemasaran Digital' => 'pemasaran_digital',
            'Kepuasan Interaksi Pelanggan' => 'kepuasan_pelanggan',
            'Jangkauan Pasar & Visibilitas' => 'jangkauan_pasar',
            'Kemampuan Kelola Arus Kas' => 'kelola_cashflow',
            'Akses Permodalan' => 'akses_modal',
            'Penentuan Harga & Keuntungan' => 'harga_keuntungan',
            'Pemanfaatan Teknologi' => 'teknologi_operasional',
            'Kemampuan Aplikasi Bisnis' => 'aplikasi_bisnis',
            'Kesiapan Adaptasi Teknologi' => 'kesiapan_teknologi',
            'Tingkat Kesulitan Usaha' => 'kesulitan_usaha',
            'Kebutuhan Pelatihan' => 'kebutuhan_pelatihan',
            'Kejelasan Strategi Jangka Panjang' => 'strategi_jangka_panjang',
        ];

        foreach ($isuTeridentifikasi as $isu) {
            $opiniKey = $labelToOpiniKey[$isu['item']] ?? '';
            $opiniContext = $opiniTexts[$opiniKey] ?? '';

            AiRecommendation::create([
                'user_id' => $user->id,
                'kuesioner_response_id' => $kuesioner->id, 
                'isu' => $isu['item'],
                'aspek' => $isu['aspek'],
                'skor' => $isu['skor'],
                'rekomendasi' => $this->generateRecommendations($isu, $opiniContext),
            ]);
        }

        // Clear session
        $request->session()->forget('kuesioner');
        $request->session()->forget('kuesioner_opini');

        return redirect()->route('dashboard')->with([
            'success' => 'Kuesioner berhasil disubmit! Hasil analisis Anda sudah diperbarui.',
            'show_sus' => true,
        ]);
    }

    private function callFlaskML($userId, $answers, $opiniTexts = []): ?array
    {
        // Key mapping: form keys → Flask ML API keys
        $keyMap = [
            'kualitas_produk' => 'kualitas_produk_layanan',
            'efisiensi_operasional' => 'efisiensi_proses_operasional',
            'penuhi_permintaan' => 'kemampuan_memenuhi_permintaan',
            'kualitas_sdm' => 'kualitas_sdm_operasional',
            'efektivitas_pemasaran' => 'efektivitas_strategi_pemasaran',
            'pemasaran_digital' => 'kemampuan_pemasaran_digital',
            'kepuasan_pelanggan' => 'kepuasan_interaksi_pelanggan',
            'jangkauan_pasar' => 'jangkauan_pasar_visibilitas',
            'kelola_cashflow' => 'kemampuan_kelola_cash_flow',
            'akses_modal' => 'kemudahan_akses_permodalan',
            'harga_keuntungan' => 'kemampuan_tentukan_harga_keuntungan',
            'teknologi_operasional' => 'pemanfaatan_teknologi_operasional',
            'aplikasi_bisnis' => 'kemampuan_aplikasi_bisnis',
            'kesiapan_teknologi' => 'tingkat_kesiapan_adaptasi_teknologi',
            'kesulitan_usaha' => 'tingkat_kesulitan_menjalankan_usaha',
            'kebutuhan_pelatihan' => 'kebutuhan_pelatihan_pembelajaran',
            'strategi_jangka_panjang' => 'kejelasan_strategi_jangka_panjang',
        ];

        try {
            $flaskUrl = config('app.flask_ml_url', env('FLASK_ML_URL', 'http://localhost:5000'));

            // Build jawaban with ML API keys
            $jawaban = [];
            foreach ($keyMap as $formKey => $mlKey) {
                $jawaban[$mlKey] = $answers[$formKey] ?? 3;
            }

            // Build opini with ML API keys
            $opini = [];
            foreach ($keyMap as $formKey => $mlKey) {
                $opini[$mlKey] = $opiniTexts[$formKey] ?? '';
            }

            $response = \Illuminate\Support\Facades\Http::timeout(60)->post("{$flaskUrl}/api/predict", [
                'user_id' => $userId,
                'jawaban' => $jawaban,
                'opini' => $opini,
            ]);

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Exception $e) {
            \Log::warning('Flask ML service unavailable: ' . $e->getMessage());
        }

        return null;
    }

    private function getDemoModelComparison(): array
    {
        return [
            'Logistic Regression' => ['akurasi' => 0.82, 'precision' => 0.81, 'recall' => 0.82, 'f1' => 0.81, 'roc_auc' => 0.88],
            'XGBoost'             => ['akurasi' => 0.89, 'precision' => 0.88, 'recall' => 0.89, 'f1' => 0.88, 'roc_auc' => 0.93],
            'Decision Tree'       => ['akurasi' => 0.84, 'precision' => 0.83, 'recall' => 0.84, 'f1' => 0.83, 'roc_auc' => 0.90],
        ];
    }

    private function generateRecommendations($isu, $opiniContext = ''): array
    {
        $recs = $this->callGeminiAI($isu, $opiniContext);
        if ($recs) return $recs;

        $staticRecs = [
            'Pemasaran' => [
                'Manfaatkan platform media sosial seperti Instagram, TikTok, dan Facebook untuk memperluas jangkauan pemasaran secara digital dengan konten yang menarik dan konsisten.',
                'Ikuti pelatihan pemasaran digital dan buat strategi konten yang terencana untuk meningkatkan visibilitas brand usaha Anda.',
            ],
            'Keuangan' => [
                'Gunakan aplikasi pencatatan keuangan sederhana untuk memantau arus kas harian dan buat laporan keuangan bulanan secara rutin.',
                'Jelajahi program pembiayaan UMKM dari bank atau lembaga keuangan mikro, serta manfaatkan program bantuan modal dari pemerintah.',
            ],
            'Teknologi' => [
                'Mulai adopsi teknologi digital secara bertahap, dimulai dari penggunaan aplikasi kasir digital dan sistem inventori sederhana.',
                'Ikuti workshop atau pelatihan digitalisasi UMKM yang diselenggarakan oleh pemerintah daerah atau lembaga pendamping UMKM.',
            ],
            'Operasional' => [
                'Evaluasi dan optimasi proses operasional dengan membuat SOP (Standard Operating Procedure) untuk setiap tahapan kerja.',
                'Investasikan pada pelatihan karyawan secara berkala untuk meningkatkan kualitas SDM dan efisiensi operasional.',
            ],
            'Tantangan' => [
                'Buat perencanaan bisnis jangka panjang (3-5 tahun) dengan target yang terukur dan strategi yang jelas.',
                'Bergabung dengan komunitas UMKM atau asosiasi bisnis untuk berbagi pengalaman dan mendapatkan mentoring.',
            ],
        ];

        return $staticRecs[$isu['aspek']] ?? [
            'Lakukan evaluasi menyeluruh terhadap aspek ini dan identifikasi area yang perlu diperbaiki.',
            'Cari referensi dan pelatihan terkait untuk meningkatkan kapabilitas di bidang ini.',
        ];
    }

    private function callGeminiAI($isu, $opiniContext = ''): ?array
    {
        $apiKey = env('GEMINI_API_KEY');
        if (empty($apiKey)) return null;

        try {
            $opiniSection = '';
            if (!empty($opiniContext) && strlen($opiniContext) > 5) {
                $opiniSection = "\nOpini langsung dari pelaku UMKM:\n\"{$opiniContext}\"\n";
            }

            $prompt = "Kamu adalah konsultan bisnis UMKM di Indonesia. Berikan 2 rekomendasi strategis yang konkret dan bisa langsung diterapkan untuk mengatasi masalah berikut:\n\nIsu: {$isu['item']}\nAspek Bisnis: {$isu['aspek']}\nSkor Kepuasan: {$isu['skor']}/5 (sangat rendah){$opiniSection}\nINSTRUKSI PENTING:\n1. Rekomendasi HARUS spesifik untuk isu \"{$isu['item']}\"\n2. Jangan berikan saran umum yang tidak terkait isu ini\n3. Saran harus bisa langsung diterapkan oleh UMKM kecil\n4. Gunakan bahasa Indonesia yang mudah dipahami\n\nBerikan rekomendasi dalam bahasa Indonesia, masing-masing 1-2 kalimat, praktis dan actionable.";

            $response = \Illuminate\Support\Facades\Http::timeout(15)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key={$apiKey}", [
                    'contents' => [['parts' => [['text' => $prompt]]]],
                ]);

            if ($response->successful()) {
                $text = $response->json('candidates.0.content.parts.0.text', '');
                $lines = array_filter(array_map('trim', explode("\n", $text)), fn($l) => strlen($l) > 10);
                $recs = array_values(array_map(fn($l) => preg_replace('/^\d+[\.)\]]\s*/', '', $l), $lines));
                return array_slice($recs, 0, 2);
            }
        } catch (\Exception $e) {
            \Log::warning('Gemini API unavailable: ' . $e->getMessage());
        }

        return null;
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AiRecommendation;
use App\Models\KuesionerResponse;

class RekomendasiController extends Controller
{
    private function getModulMapping(): array
    {
        return [
            'Kualitas Produk/Layanan' => [
                'membuat-sop-kuat-konsisten-umkm',
                '10-kesalahan-bisnis-cara-hindarinya',
            ],
            'Efisiensi Proses Operasional' => [
                'mengelola-bisnis-secara-efisien',
                'membuat-sop-kuat-konsisten-umkm',
            ],
            'Kemampuan Memenuhi Permintaan' => [
                'mengelola-bisnis-secara-efisien',
                '10-kesalahan-bisnis-cara-hindarinya',
            ],
            'Kualitas SDM/Karyawan' => [
                'tips-merekrut-karyawan-pertama-umkm',
                'cara-hitung-gaji-karyawan',
            ],

            'Efektivitas Strategi Pemasaran' => [
                'strategi-pemasaran-7p-umkm',
                '10-kesalahan-bisnis-cara-hindarinya',
            ],
            'Kemampuan Pemasaran Digital' => [
                'digital-marketing-pemula-umkm',
                'kenapa-bisnis-umkm-gagal',
            ],
            'Kepuasan Interaksi Pelanggan' => [
                'strategi-pemasaran-7p-umkm',
                '10-kesalahan-bisnis-cara-hindarinya',
            ],
            'Jangkauan Pasar & Visibilitas' => [
                'strategi-pemasaran-lokal-umkm',
                'digital-marketing-pemula-umkm',
            ],

            'Kemampuan Kelola Arus Kas' => [
                'cara-kelola-uang-usaha-kecil',
                'ngerti-keuangan-bisnis-10-menit',
            ],
            'Akses Permodalan' => [
                'cara-kelola-uang-usaha-kecil',
                'infrastruktur-digital-umkm-cnbc',
            ],
            'Penentuan Harga & Keuntungan' => [
                'ngerti-keuangan-bisnis-10-menit',
                'cara-hitung-gaji-karyawan',
            ],

            'Pemanfaatan Teknologi' => [
                'infrastruktur-digital-umkm-cnbc',
                'kenapa-bisnis-umkm-gagal',
            ],
            'Kemampuan Aplikasi Bisnis' => [
                'infrastruktur-digital-umkm-cnbc',
                'kenapa-bisnis-umkm-gagal',
            ],
            'Kesiapan Adaptasi Teknologi' => [
                'infrastruktur-digital-umkm-cnbc',
                'kenapa-bisnis-umkm-gagal',
            ],

            'Tingkat Kesulitan Usaha' => [
                'strategi-mindset-bisnis-hermanto-tanoko',
                '10-kesalahan-bisnis-cara-hindarinya',
            ],
            'Kebutuhan Pelatihan' => [
                'strategi-mindset-bisnis-hermanto-tanoko',
                'membuat-sop-kuat-konsisten-umkm',
            ],
            'Kejelasan Strategi Jangka Panjang' => [
                'strategi-mindset-bisnis-hermanto-tanoko',
            ],
        ];
    }

    private function getSuggestedModules(string $isuName): array
    {
        $mapping = $this->getModulMapping();
        $allModules = config('modules', []);
        $suggestedSlugs = $mapping[$isuName] ?? [];

        $result = [];
        foreach ($suggestedSlugs as $slug) {
            $module = collect($allModules)->firstWhere('slug', $slug);
            if ($module) {
                $result[] = [
                    'slug' => $module['slug'],
                    'judul' => $module['judul'],
                    'kategori' => $module['kategori'],
                    'durasi' => $module['durasi'],
                ];
            }
        }
        return array_slice($result, 0, 2); // Max 2 modules per issue
    }

    public function index()
    {
        $user = Auth::user();
        $hasKuesioner = KuesionerResponse::where('user_id', $user->id)->exists();
        $aiRecommendations = AiRecommendation::where('user_id', $user->id)->get();
        $existingIsuNames = $aiRecommendations->pluck('isu')->toArray();
        $aiRecommendations->each(function ($rec) {
            $rec->modul_saran = $this->getSuggestedModules($rec->isu ?? '');
            if (!isset($rec->kategori)) {
                $rec->kategori = ($rec->skor ?? 2) <= 2 ? 'kritis' : 'perhatian';
            }
        });

        $latestKuesioner = KuesionerResponse::where('user_id', $user->id)->latest()->first();
        $fallbackRecommendations = collect();

        if ($latestKuesioner) {
            foreach ($latestKuesioner->getIsuTeridentifikasi() as $isu) {
                if (in_array($isu['item'], $existingIsuNames)) continue;
                $fallback = $this->buildFallbackRecommendation(
                    $isu['item'],
                    $isu['aspek'],
                    (int) $isu['skor'],
                    'kritis'
                );
                $fallbackRecommendations->push($fallback);
            }

            foreach ($latestKuesioner->getItemPerluPerhatian() as $item) {
                if (in_array($item['item'], $existingIsuNames)) continue;
                $fallback = $this->buildFallbackRecommendation(
                    $item['item'],
                    $item['aspek'],
                    (int) $item['skor'],
                    'perhatian'
                );
                $fallbackRecommendations->push($fallback);
            }
        }

        $recommendations = $aiRecommendations->concat($fallbackRecommendations)
            ->sortBy(function ($r) {
                $catPriority = ($r->kategori ?? 'kritis') === 'kritis' ? 0 : 1;
                return sprintf('%d-%02d', $catPriority, $r->skor ?? 5);
            })
            ->values();

        return view('rekomendasi', compact('recommendations', 'hasKuesioner'));
    }

    public function show($id)
    {
        $recommendation = AiRecommendation::findOrFail($id);
        $recommendation->modul_saran = $this->getSuggestedModules($recommendation->isu ?? '');
        return view('solusi', compact('recommendation'));
    }

    public function showByIsu(Request $request)
    {
        $user = Auth::user();
        $isu = $request->query('isu', '');

        if (empty($isu)) {
            return redirect()->route('rekomendasi');
        }

        $latestKuesioner = KuesionerResponse::where('user_id', $user->id)->latest()->first();
        $skorItem = null;
        $aspekItem = null;

        if ($latestKuesioner) {
            $skorPerAspek = $this->findItemScore($latestKuesioner, $isu);
            $skorItem = $skorPerAspek['skor'] ?? null;
            $aspekItem = $skorPerAspek['aspek'] ?? null;
        }

        if ($skorItem !== null && $skorItem <= 2) {
            $recommendation = AiRecommendation::where('user_id', $user->id)
                ->where('isu', $isu)
                ->first();

            if ($recommendation) {
                $recommendation->modul_saran = $this->getSuggestedModules($recommendation->isu ?? '');
                return view('solusi-node', compact('recommendation'));
            }

            $recommendation = $this->buildFallbackRecommendation($isu, $aspekItem, $skorItem, 'kritis');
            return view('solusi-node', compact('recommendation'));
        }

        if ($skorItem === 3) {
            $recommendation = $this->buildFallbackRecommendation($isu, $aspekItem, 3, 'perhatian');
            return view('solusi-node', compact('recommendation'));
        }

        if ($skorItem !== null && $skorItem >= 4) {
            $recommendation = $this->buildFallbackRecommendation($isu, $aspekItem, $skorItem, 'baik');
            return view('solusi-node', compact('recommendation'));
        }

        return redirect()->route('rekomendasi')->with('error', 'Item "' . $isu . '" tidak ditemukan di kuesioner Anda.');
    }

    private function findItemScore(KuesionerResponse $kuesioner, string $itemName): array
    {
        $mapping = [
            'Kualitas Produk/Layanan'             => ['col' => 'kualitas_produk', 'aspek' => 'Operasional'],
            'Efisiensi Proses Operasional'        => ['col' => 'efisiensi_operasional', 'aspek' => 'Operasional'],
            'Kemampuan Memenuhi Permintaan'       => ['col' => 'penuhi_permintaan', 'aspek' => 'Operasional'],
            'Kualitas SDM/Karyawan'               => ['col' => 'kualitas_sdm', 'aspek' => 'Operasional'],
            'Efektivitas Strategi Pemasaran'      => ['col' => 'efektivitas_pemasaran', 'aspek' => 'Pemasaran'],
            'Kemampuan Pemasaran Digital'         => ['col' => 'pemasaran_digital', 'aspek' => 'Pemasaran'],
            'Kepuasan Interaksi Pelanggan'        => ['col' => 'kepuasan_pelanggan', 'aspek' => 'Pemasaran'],
            'Jangkauan Pasar & Visibilitas'       => ['col' => 'jangkauan_pasar', 'aspek' => 'Pemasaran'],
            'Kemampuan Kelola Arus Kas'           => ['col' => 'kelola_cashflow', 'aspek' => 'Keuangan'],
            'Akses Permodalan'                    => ['col' => 'akses_modal', 'aspek' => 'Keuangan'],
            'Penentuan Harga & Keuntungan'        => ['col' => 'harga_keuntungan', 'aspek' => 'Keuangan'],
            'Pemanfaatan Teknologi'               => ['col' => 'teknologi_operasional', 'aspek' => 'Teknologi'],
            'Kemampuan Aplikasi Bisnis'           => ['col' => 'aplikasi_bisnis', 'aspek' => 'Teknologi'],
            'Kesiapan Adaptasi Teknologi'         => ['col' => 'kesiapan_teknologi', 'aspek' => 'Teknologi'],
            'Tingkat Kesulitan Usaha'             => ['col' => 'kesulitan_usaha', 'aspek' => 'Tantangan'],
            'Kebutuhan Pelatihan'                 => ['col' => 'kebutuhan_pelatihan', 'aspek' => 'Tantangan'],
            'Kejelasan Strategi Jangka Panjang'   => ['col' => 'strategi_jangka_panjang', 'aspek' => 'Tantangan'],
        ];

        $info = $mapping[$itemName] ?? null;
        if (!$info) return [];

        return [
            'skor'  => $kuesioner->{$info['col']} ?? null,
            'aspek' => $info['aspek'],
        ];
    }

    private function buildFallbackRecommendation(string $isu, ?string $aspek, int $skor, string $kategori): object
    {
        $rec = new \stdClass();
        $rec->isu = $isu;
        $rec->aspek = $aspek ?? 'Umum';
        $rec->skor = $skor;
        $rec->kategori = $kategori; 

        // Generate saran berdasarkan kategori
        if ($kategori === 'kritis') {
            $rec->rekomendasi = $this->generateSaranKritis($isu, $aspek);
        } elseif ($kategori === 'perhatian') {
            $rec->rekomendasi = $this->generateSaranPerhatian($isu, $aspek);
        } else {
            // baik
            $rec->rekomendasi = [
                "Selamat! Item \"{$isu}\" Anda sudah berada di kategori BAIK (skor {$skor}/5).",
                "Pertahankan kualitas dengan terus melakukan evaluasi rutin.",
                "Bagikan praktik baik Anda kepada UMKM lain untuk memperkuat komunitas bisnis.",
            ];
        }

        $rec->modul_saran = $this->getSuggestedModules($isu);
        return $rec;
    }

    private function generateSaranKritis(string $isu, ?string $aspek): array
    {
        return [
            "Item \"{$isu}\" di aspek {$aspek} membutuhkan **perhatian segera** karena masuk kategori kritis.",
            "Identifikasi akar masalah: apakah karena kurangnya pengetahuan, sumber daya, atau sistem? Buatlah daftar 3 penyebab utama.",
            "Tonton modul pembelajaran yang disarankan di bawah ini untuk mendapatkan strategi konkret.",
            "Buat action plan **30 hari** dengan target spesifik untuk meningkatkan skor item ini dari {$isu} ke minimal 3-4.",
            "Konsultasikan dengan komunitas UMKM atau mentor untuk mendapatkan feedback eksternal.",
        ];
    }

    private function generateSaranPerhatian(string $isu, ?string $aspek): array
    {
        $saranPerAspek = [
            'Operasional' => [
                "Item \"{$isu}\" Anda saat ini di level **Netral (skor 3/5)** — ada ruang untuk peningkatan ke kategori Baik.",
                "Identifikasi 1-2 area spesifik dari aspek Operasional yang bisa diperbaiki dengan effort minimal.",
                "Mulai dokumentasikan proses kerja harian — ini akan membantu konsistensi dan memudahkan delegasi.",
                "Coba terapkan 1 perbaikan kecil per minggu (mis. sistem inventory, SOP layanan, dll).",
                "Pantau efek perubahan selama 2-4 minggu sebelum lanjut ke perbaikan berikutnya.",
            ],
            'Pemasaran' => [
                "Item \"{$isu}\" di aspek Pemasaran masih di level **Netral** — peluang besar untuk tingkatkan ke Baik.",
                "Identifikasi 1 channel pemasaran (mis. WhatsApp, Instagram, atau word-of-mouth) yang paling sesuai dengan target pasar Anda.",
                "Konsisten posting konten 2-3x per minggu di channel pilihan untuk membangun engagement.",
                "Eksperimen dengan strategi pemasaran baru selama 2 minggu, ukur hasilnya.",
                "Pelajari modul Digital Marketing untuk taktik konkret yang bisa langsung diterapkan.",
            ],
            'Keuangan' => [
                "Item \"{$isu}\" di aspek Keuangan masih di level **Netral** — ini area krusial untuk dikuatkan.",
                "Mulai pencatatan keuangan harian (pemasukan dan pengeluaran) dengan aplikasi sederhana atau buku tulis.",
                "Pisahkan rekening pribadi dan bisnis untuk memudahkan tracking arus kas.",
                "Review keuangan mingguan: identifikasi pos pengeluaran yang bisa dihemat.",
                "Pelajari modul Keuangan UMKM untuk pemahaman dasar yang lebih kuat.",
            ],
            'Teknologi' => [
                "Item \"{$isu}\" di aspek Teknologi masih di level **Netral** — adopsi teknologi bisa mendorong efisiensi.",
                "Coba 1 tool digital sederhana yang bisa membantu operasional (mis. Google Sheets untuk inventory, WhatsApp Business untuk komunikasi pelanggan).",
                "Alokasikan waktu 30 menit/minggu untuk belajar fitur baru dari tool yang dipakai.",
                "Mulai jualan di marketplace (Tokopedia, Shopee, dll) sebagai tambahan channel — tidak perlu langsung besar.",
                "Pelajari modul Infrastruktur Digital UMKM untuk gambaran lengkap.",
            ],
            'Tantangan' => [
                "Item \"{$isu}\" di aspek Tantangan masih di level **Netral** — ini bagian dari mindset dan strategi jangka panjang.",
                "Tulis visi bisnis 1-3 tahun ke depan secara spesifik (mis. omzet, jumlah karyawan, lokasi).",
                "Identifikasi 3 keterampilan utama yang perlu dipelajari untuk capai visi tersebut.",
                "Cari mentor atau komunitas UMKM yang bisa memberikan perspektif dan support.",
                "Pelajari modul Mindset & Strategi Bisnis dari Hermanto Tanoko untuk inspirasi praktis.",
            ],
        ];

        $generic = [
            "Item \"{$isu}\" Anda saat ini di level **Netral (skor 3/5)** — ada ruang untuk peningkatan.",
            "Pertimbangkan untuk tingkatkan ke skor 4 dengan menerapkan praktik terbaik di bidang ini.",
            "Tonton modul pembelajaran yang disarankan untuk mendapatkan strategi konkret.",
            "Lakukan evaluasi mingguan untuk track progress peningkatan Anda.",
        ];

        return $saranPerAspek[$aspek] ?? $generic;
    }
}
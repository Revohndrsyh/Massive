<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KuesionerResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'kualitas_produk', 'efisiensi_operasional', 'penuhi_permintaan', 'kualitas_sdm',
        'efektivitas_pemasaran', 'pemasaran_digital', 'kepuasan_pelanggan', 'jangkauan_pasar',
        'kelola_cashflow', 'akses_modal', 'harga_keuntungan',
        'teknologi_operasional', 'aplikasi_bisnis', 'kesiapan_teknologi',
        'kesulitan_usaha', 'kebutuhan_pelatihan', 'strategi_jangka_panjang',
        // Opini teks per item (Pipeline B: NLP)
        'opini_kualitas_produk', 'opini_efisiensi_operasional', 'opini_penuhi_permintaan', 'opini_kualitas_sdm',
        'opini_efektivitas_pemasaran', 'opini_pemasaran_digital', 'opini_kepuasan_pelanggan', 'opini_jangkauan_pasar',
        'opini_kelola_cashflow', 'opini_akses_modal', 'opini_harga_keuntungan',
        'opini_teknologi_operasional', 'opini_aplikasi_bisnis', 'opini_kesiapan_teknologi',
        'opini_kesulitan_usaha', 'opini_kebutuhan_pelatihan', 'opini_strategi_jangka_panjang',
        // Hasil analisis
        'rata_rata_likert', 'sentimen', 'confidence',
        'nlp_topics', 'nlp_edges', 'integrated_result',
    ];

    protected $casts = [
        'nlp_topics' => 'array',
        'nlp_edges' => 'array',
        'integrated_result' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mlResult()
    {
        return $this->hasOne(MlResult::class);
    }

    public function getSkorPerAspek(): array
    {
        return [
            'operasional' => round(collect([$this->kualitas_produk, $this->efisiensi_operasional, $this->penuhi_permintaan, $this->kualitas_sdm])->avg(), 2),
            'pemasaran' => round(collect([$this->efektivitas_pemasaran, $this->pemasaran_digital, $this->kepuasan_pelanggan, $this->jangkauan_pasar])->avg(), 2),
            'keuangan' => round(collect([$this->kelola_cashflow, $this->akses_modal, $this->harga_keuntungan])->avg(), 2),
            'teknologi' => round(collect([$this->teknologi_operasional, $this->aplikasi_bisnis, $this->kesiapan_teknologi])->avg(), 2),
            'tantangan' => round(collect([$this->kesulitan_usaha, $this->kebutuhan_pelatihan, $this->strategi_jangka_panjang])->avg(), 2),
        ];
    }

    private function getItemMapping(): array
    {
        return [
            'kualitas_produk' => ['item' => 'Kualitas Produk/Layanan', 'aspek' => 'Operasional'],
            'efisiensi_operasional' => ['item' => 'Efisiensi Proses Operasional', 'aspek' => 'Operasional'],
            'penuhi_permintaan' => ['item' => 'Kemampuan Memenuhi Permintaan', 'aspek' => 'Operasional'],
            'kualitas_sdm' => ['item' => 'Kualitas SDM/Karyawan', 'aspek' => 'Operasional'],
            'efektivitas_pemasaran' => ['item' => 'Efektivitas Strategi Pemasaran', 'aspek' => 'Pemasaran'],
            'pemasaran_digital' => ['item' => 'Kemampuan Pemasaran Digital', 'aspek' => 'Pemasaran'],
            'kepuasan_pelanggan' => ['item' => 'Kepuasan Interaksi Pelanggan', 'aspek' => 'Pemasaran'],
            'jangkauan_pasar' => ['item' => 'Jangkauan Pasar & Visibilitas', 'aspek' => 'Pemasaran'],
            'kelola_cashflow' => ['item' => 'Kemampuan Kelola Arus Kas', 'aspek' => 'Keuangan'],
            'akses_modal' => ['item' => 'Akses Permodalan', 'aspek' => 'Keuangan'],
            'harga_keuntungan' => ['item' => 'Penentuan Harga & Keuntungan', 'aspek' => 'Keuangan'],
            'teknologi_operasional' => ['item' => 'Pemanfaatan Teknologi', 'aspek' => 'Teknologi'],
            'aplikasi_bisnis' => ['item' => 'Kemampuan Aplikasi Bisnis', 'aspek' => 'Teknologi'],
            'kesiapan_teknologi' => ['item' => 'Kesiapan Adaptasi Teknologi', 'aspek' => 'Teknologi'],
            'kesulitan_usaha' => ['item' => 'Tingkat Kesulitan Usaha', 'aspek' => 'Tantangan'],
            'kebutuhan_pelatihan' => ['item' => 'Kebutuhan Pelatihan', 'aspek' => 'Tantangan'],
            'strategi_jangka_panjang' => ['item' => 'Kejelasan Strategi Jangka Panjang', 'aspek' => 'Tantangan'],
        ];
    }

    public function getIsuTeridentifikasi(): array
    {
        $issues = [];
        foreach ($this->getItemMapping() as $col => $info) {
            if ($this->$col !== null && $this->$col <= 2) {
                $issues[] = [
                    'item' => $info['item'],
                    'aspek' => $info['aspek'],
                    'skor' => $this->$col,
                ];
            }
        }
        return $issues;
    }

    public function getItemPerluPerhatian(): array
    {
        $items = [];
        foreach ($this->getItemMapping() as $col => $info) {
            if ($this->$col !== null && (int) $this->$col === 3) {
                $items[] = [
                    'item' => $info['item'],
                    'aspek' => $info['aspek'],
                    'skor' => (int) $this->$col,
                ];
            }
        }
        return $items;
    }
}
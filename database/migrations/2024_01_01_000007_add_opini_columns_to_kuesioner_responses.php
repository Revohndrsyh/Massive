<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kuesioner_responses', function (Blueprint $table) {
            // Opini teks per item kuesioner (Pipeline B: NLP)
            $table->text('opini_kualitas_produk')->nullable()->after('kualitas_produk');
            $table->text('opini_efisiensi_operasional')->nullable()->after('efisiensi_operasional');
            $table->text('opini_penuhi_permintaan')->nullable()->after('penuhi_permintaan');
            $table->text('opini_kualitas_sdm')->nullable()->after('kualitas_sdm');
            $table->text('opini_efektivitas_pemasaran')->nullable()->after('efektivitas_pemasaran');
            $table->text('opini_pemasaran_digital')->nullable()->after('pemasaran_digital');
            $table->text('opini_kepuasan_pelanggan')->nullable()->after('kepuasan_pelanggan');
            $table->text('opini_jangkauan_pasar')->nullable()->after('jangkauan_pasar');
            $table->text('opini_kelola_cashflow')->nullable()->after('kelola_cashflow');
            $table->text('opini_akses_modal')->nullable()->after('akses_modal');
            $table->text('opini_harga_keuntungan')->nullable()->after('harga_keuntungan');
            $table->text('opini_teknologi_operasional')->nullable()->after('teknologi_operasional');
            $table->text('opini_aplikasi_bisnis')->nullable()->after('aplikasi_bisnis');
            $table->text('opini_kesiapan_teknologi')->nullable()->after('kesiapan_teknologi');
            $table->text('opini_kesulitan_usaha')->nullable()->after('kesulitan_usaha');
            $table->text('opini_kebutuhan_pelatihan')->nullable()->after('kebutuhan_pelatihan');
            $table->text('opini_strategi_jangka_panjang')->nullable()->after('strategi_jangka_panjang');

            // Pipeline B results
            $table->json('nlp_topics')->nullable()->after('confidence');
            $table->json('nlp_edges')->nullable()->after('nlp_topics');
            $table->json('integrated_result')->nullable()->after('nlp_edges');
        });
    }

    public function down(): void
    {
        Schema::table('kuesioner_responses', function (Blueprint $table) {
            $table->dropColumn([
                'opini_kualitas_produk', 'opini_efisiensi_operasional',
                'opini_penuhi_permintaan', 'opini_kualitas_sdm',
                'opini_efektivitas_pemasaran', 'opini_pemasaran_digital',
                'opini_kepuasan_pelanggan', 'opini_jangkauan_pasar',
                'opini_kelola_cashflow', 'opini_akses_modal',
                'opini_harga_keuntungan', 'opini_teknologi_operasional',
                'opini_aplikasi_bisnis', 'opini_kesiapan_teknologi',
                'opini_kesulitan_usaha', 'opini_kebutuhan_pelatihan',
                'opini_strategi_jangka_panjang',
                'nlp_topics', 'nlp_edges', 'integrated_result',
            ]);
        });
    }
};

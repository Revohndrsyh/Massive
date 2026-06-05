<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kuesioner_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Bagian A: Operasional & Produk
            $table->tinyInteger('kualitas_produk')->nullable();
            $table->tinyInteger('efisiensi_operasional')->nullable();
            $table->tinyInteger('penuhi_permintaan')->nullable();
            $table->tinyInteger('kualitas_sdm')->nullable();

            // Bagian B: Pemasaran & Hubungan Pelanggan
            $table->tinyInteger('efektivitas_pemasaran')->nullable();
            $table->tinyInteger('pemasaran_digital')->nullable();
            $table->tinyInteger('kepuasan_pelanggan')->nullable();
            $table->tinyInteger('jangkauan_pasar')->nullable();

            // Bagian C: Keuangan & Akses Modal
            $table->tinyInteger('kelola_cashflow')->nullable();
            $table->tinyInteger('akses_modal')->nullable();
            $table->tinyInteger('harga_keuntungan')->nullable();

            // Bagian D: Teknologi dan Digitalisasi
            $table->tinyInteger('teknologi_operasional')->nullable();
            $table->tinyInteger('aplikasi_bisnis')->nullable();
            $table->tinyInteger('kesiapan_teknologi')->nullable();

            // Bagian E: Tantangan & Kebutuhan UMKM
            $table->tinyInteger('kesulitan_usaha')->nullable();
            $table->tinyInteger('kebutuhan_pelatihan')->nullable();
            $table->tinyInteger('strategi_jangka_panjang')->nullable();

            // Hasil analisis
            $table->decimal('rata_rata_likert', 4, 2)->nullable();
            $table->string('sentimen')->nullable(); // Positive / Negative
            $table->decimal('confidence', 5, 4)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kuesioner_responses');
    }
};

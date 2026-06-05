<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ml_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('kuesioner_response_id')->constrained('kuesioner_responses')->onDelete('cascade');
            $table->string('model_terbaik')->nullable();
            $table->json('perbandingan_model')->nullable();
            $table->json('skor_per_aspek')->nullable();
            $table->json('isu_teridentifikasi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ml_results');
    }
};

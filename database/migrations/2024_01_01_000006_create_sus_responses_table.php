<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sus_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->tinyInteger('sus_1');
            $table->tinyInteger('sus_2');
            $table->tinyInteger('sus_3');
            $table->tinyInteger('sus_4');
            $table->tinyInteger('sus_5');
            $table->tinyInteger('sus_6');
            $table->tinyInteger('sus_7');
            $table->tinyInteger('sus_8');
            $table->tinyInteger('sus_9');
            $table->tinyInteger('sus_10');
            $table->decimal('skor_sus', 5, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sus_responses');
    }
};

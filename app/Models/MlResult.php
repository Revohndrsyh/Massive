<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MlResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'kuesioner_response_id', 'model_terbaik',
        'perbandingan_model', 'skor_per_aspek', 'isu_teridentifikasi',
    ];

    protected $casts = [
        'perbandingan_model' => 'array',
        'skor_per_aspek' => 'array',
        'isu_teridentifikasi' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kuesionerResponse()
    {
        return $this->belongsTo(KuesionerResponse::class);
    }
}

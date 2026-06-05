<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiRecommendation extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'isu', 'aspek', 'skor', 'rekomendasi'];

    protected $casts = [
        'rekomendasi' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SusResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'sus_1', 'sus_2', 'sus_3', 'sus_4', 'sus_5',
        'sus_6', 'sus_7', 'sus_8', 'sus_9', 'sus_10',
        'skor_sus',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getGradeAttribute(): string
    {
        if ($this->skor_sus >= 85) return 'Excellent';
        if ($this->skor_sus >= 72) return 'Good';
        if ($this->skor_sus >= 52) return 'OK';
        return 'Poor';
    }

    public function getGradeLabelAttribute(): string
    {
        if ($this->skor_sus >= 85) return 'Sangat Baik';
        if ($this->skor_sus >= 72) return 'Baik';
        if ($this->skor_sus >= 52) return 'Cukup';
        return 'Perlu Perbaikan';
    }
}

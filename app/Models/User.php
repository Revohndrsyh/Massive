<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const KATEGORI_USAHA = [
        'Kuliner & Makanan',
        'Fashion & Pakaian',
        'Kerajinan Tangan',
        'Jasa & Layanan',
        'Perdagangan & Retail',
        'Pertanian & Perkebunan',
        'Teknologi & Digital',
        'Kecantikan & Kesehatan',
        'Pendidikan & Pelatihan',
        'Lainnya',
    ];

    protected $fillable = [
        'name',
        'phone',
        'nama_usaha',
        'kategori_usaha',
        'email',
        'password',
        'photo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function kuesionerResponses()
    {
        return $this->hasMany(KuesionerResponse::class);
    }

    public function latestKuesioner()
    {
        return $this->hasOne(KuesionerResponse::class)->latestOfMany();
    }

    public function mlResults()
    {
        return $this->hasMany(MlResult::class);
    }

    public function latestMlResult()
    {
        return $this->hasOne(MlResult::class)->latestOfMany();
    }

    public function aiRecommendations()
    {
        return $this->hasMany(AiRecommendation::class);
    }
}

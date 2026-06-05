<?php

namespace Database\Seeders;

use App\Models\KuesionerResponse;
use App\Models\MlResult;
use App\Models\SusResponse;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MockSurveyDataSeeder extends Seeder
{
    public function run(): void
    {
        for ($index = 1; $index <= 10; $index++) {
            $user = User::updateOrCreate(
                ['email' => "mock.user{$index}@example.com"],
                [
                    'name' => "Mock User {$index}",
                    'phone' => '0812345678' . str_pad((string) $index, 2, '0', STR_PAD_LEFT),
                    'nama_usaha' => "UMKM Mock {$index}",
                    'kategori_usaha' => User::KATEGORI_USAHA[($index - 1) % count(User::KATEGORI_USAHA)],
                    'password' => Hash::make('password'),
                    'role' => 'user',
                    'email_verified_at' => now(),
                ]
            );

            $susAnswers = $this->susAnswers($index);
            SusResponse::updateOrCreate(
                ['user_id' => $user->id, 'created_at' => now()->subDays(10 - $index)->startOfMinute()],
                [...$susAnswers, 'skor_sus' => $this->susScore($susAnswers)]
            );

            $kuesioner = KuesionerResponse::updateOrCreate(
                ['user_id' => $user->id, 'created_at' => now()->subDays(10 - $index)->startOfMinute()],
                $this->kuesionerPayload($user->id, $index)
            );

            MlResult::updateOrCreate(
                ['kuesioner_response_id' => $kuesioner->id],
                [
                    'user_id' => $user->id,
                    'model_terbaik' => 'Logistic Regression',
                    'perbandingan_model' => [
                        'Logistic Regression' => ['akurasi' => 0.84, 'precision' => 0.82, 'recall' => 0.83, 'f1' => 0.82, 'roc_auc' => 0.88],
                        'Decision Tree' => ['akurasi' => 0.78, 'precision' => 0.76, 'recall' => 0.77, 'f1' => 0.76, 'roc_auc' => 0.81],
                    ],
                    'skor_per_aspek' => $kuesioner->getSkorPerAspek(),
                    'isu_teridentifikasi' => $kuesioner->getIsuTeridentifikasi(),
                ]
            );
        }

        $this->command?->info('Mock survey data ready: 10 SUS responses and 10 kuesioner responses.');
    }

    private function susAnswers(int $seed): array
    {
        $answers = [];
        for ($index = 1; $index <= 10; $index++) {
            $answers["sus_{$index}"] = (($seed + $index) % 5) + 1;
        }

        return $answers;
    }

    private function susScore(array $answers): float
    {
        $total = 0;
        for ($index = 1; $index <= 10; $index++) {
            $score = $answers["sus_{$index}"];
            $total += $index % 2 !== 0 ? $score - 1 : 5 - $score;
        }

        return $total * 2.5;
    }

    private function kuesionerPayload(int $userId, int $seed): array
    {
        $columns = [
            'kualitas_produk', 'efisiensi_operasional', 'penuhi_permintaan', 'kualitas_sdm',
            'efektivitas_pemasaran', 'pemasaran_digital', 'kepuasan_pelanggan', 'jangkauan_pasar',
            'kelola_cashflow', 'akses_modal', 'harga_keuntungan',
            'teknologi_operasional', 'aplikasi_bisnis', 'kesiapan_teknologi',
            'kesulitan_usaha', 'kebutuhan_pelatihan', 'strategi_jangka_panjang',
        ];

        $payload = ['user_id' => $userId];
        foreach ($columns as $index => $column) {
            $score = (($seed + $index) % 5) + 1;
            $payload[$column] = $score;
            $payload["opini_{$column}"] = "Opini mock untuk {$column} dengan skor {$score}.";
        }

        $scores = collect($columns)->map(fn (string $column) => $payload[$column]);
        $payload['rata_rata_likert'] = round($scores->avg(), 2);
        $payload['sentimen'] = $payload['rata_rata_likert'] >= 3 ? 'Positif' : 'Negatif';
        $payload['confidence'] = 0.70 + ($seed % 20) / 100;

        return $payload;
    }
}

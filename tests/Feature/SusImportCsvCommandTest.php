<?php

namespace Tests\Feature;

use App\Models\SusResponse;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SusImportCsvCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_imports_legacy_sus_csv_idempotently(): void
    {
        $user = User::factory()->create(['id' => 7]);
        Storage::fake('local');
        Storage::disk('local')->put('sus_responses.csv', implode("\n", [
            'user_id;nama;email;sus_1;sus_2;sus_3;sus_4;sus_5;sus_6;sus_7;sus_8;sus_9;sus_10;skor_sus;grade;keterangan;tanggal',
            '7;Legacy User;legacy@example.com;4;2;4;2;4;2;4;2;4;2;80.0;Good;Baik;05/06/2026 20:00:00',
        ]));

        $this->artisan('sus:import-csv')
            ->expectsOutputToContain('Imported: 1')
            ->assertSuccessful();

        $this->artisan('sus:import-csv')
            ->expectsOutputToContain('Skipped: 1')
            ->assertSuccessful();

        $this->assertDatabaseCount('sus_responses', 1);
        $this->assertDatabaseHas('sus_responses', [
            'user_id' => $user->id,
            'sus_1' => 4,
            'sus_10' => 2,
            'skor_sus' => 80,
        ]);
    }
}

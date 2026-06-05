<?php

namespace Tests\Feature;

use App\Models\KuesionerResponse;
use App\Models\SusResponse;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_sus_and_kuesioner_lists(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create([
            'name' => 'Alya Tester',
            'email' => 'alya@example.com',
            'nama_usaha' => 'Toko Alya',
            'kategori_usaha' => 'Kuliner & Makanan',
        ]);

        SusResponse::create($this->susPayload($user->id));
        KuesionerResponse::create($this->kuesionerPayload($user->id));

        $response = $this->actingAs($admin)->get('/admin?search=alya');

        $response->assertOk();
        $response->assertSee('Admin Dashboard');
        $response->assertSee('Alya Tester');
        $response->assertSee('Toko Alya');
        $response->assertSee('SUS Responses');
        $response->assertSee('Kuesioner Responses');
    }

    public function test_non_admin_cannot_view_admin_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)->get('/admin')->assertForbidden();
    }

    public function test_admin_can_export_sus_csv_and_xlsx(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['name' => 'Export SUS User', 'email' => 'sus@example.com']);
        SusResponse::create($this->susPayload($user->id));

        $this->actingAs($admin)
            ->get('/admin/sus/export/csv?search=sus@example.com')
            ->assertOk()
            ->assertHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->assertSee('Export SUS User');

        $this->actingAs($admin)
            ->get('/admin/sus/export/xlsx?search=sus@example.com')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_admin_can_export_kuesioner_csv_and_xlsx(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['name' => 'Export Kuesioner User', 'email' => 'kuesioner@example.com']);
        KuesionerResponse::create($this->kuesionerPayload($user->id));

        $this->actingAs($admin)
            ->get('/admin/kuesioner/export/csv?search=kuesioner@example.com')
            ->assertOk()
            ->assertHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->assertSee('Export Kuesioner User');

        $this->actingAs($admin)
            ->get('/admin/kuesioner/export/xlsx?search=kuesioner@example.com')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    private function susPayload(int $userId): array
    {
        return [
            'user_id' => $userId,
            'sus_1' => 4,
            'sus_2' => 2,
            'sus_3' => 4,
            'sus_4' => 2,
            'sus_5' => 4,
            'sus_6' => 2,
            'sus_7' => 4,
            'sus_8' => 2,
            'sus_9' => 4,
            'sus_10' => 2,
            'skor_sus' => 80,
        ];
    }

    private function kuesionerPayload(int $userId): array
    {
        return [
            'user_id' => $userId,
            'kualitas_produk' => 4,
            'efisiensi_operasional' => 4,
            'penuhi_permintaan' => 3,
            'kualitas_sdm' => 4,
            'efektivitas_pemasaran' => 3,
            'pemasaran_digital' => 3,
            'kepuasan_pelanggan' => 4,
            'jangkauan_pasar' => 3,
            'kelola_cashflow' => 3,
            'akses_modal' => 2,
            'harga_keuntungan' => 4,
            'teknologi_operasional' => 3,
            'aplikasi_bisnis' => 3,
            'kesiapan_teknologi' => 3,
            'kesulitan_usaha' => 2,
            'kebutuhan_pelatihan' => 3,
            'strategi_jangka_panjang' => 4,
            'rata_rata_likert' => 3.35,
            'sentimen' => 'Positif',
            'confidence' => 0.8500,
        ];
    }
}

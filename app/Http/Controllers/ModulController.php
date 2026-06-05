<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KuesionerResponse;
use Illuminate\Support\Facades\Auth;

class ModulController extends Controller
{
    private function getModules(): array
    {
        return config('modules', []);
    }

    private function getAspekModulMapping(): array
    {
        return [
            'Keuangan' => [
                'cara-kelola-uang-usaha-kecil',
                'ngerti-keuangan-bisnis-10-menit',
                'cara-hitung-gaji-karyawan',
                '10-kesalahan-bisnis-cara-hindarinya',
            ],
            'Operasional' => [
                'kenapa-bisnis-umkm-gagal',
                'cara-membuat-sop-sederhana',
                'cara-mudah-membuat-sop-umkm',
                'mengelola-bisnis-secara-efisien',
                'membuat-sop-kuat-konsisten-umkm',
                'tips-merekrut-karyawan-pertama-umkm',
                '10-kesalahan-bisnis-cara-hindarinya',
            ],
            'Pemasaran' => [
                'belajar-digital-marketing-dari-0',
            ],
            'Teknologi' => [
                'kenapa-bisnis-umkm-gagal',
                'belajar-digital-marketing-dari-0',
            ],
            'Tantangan' => [
                'kenapa-bisnis-umkm-gagal',
                'strategi-mindset-bisnis-hermanto-tanoko',
            ],
        ];
    }

    public function index()
    {
        $modules = $this->getModules();
        $featured = array_slice($modules, 0, 3);
        $rekomendasiModul = [];
        $hasKuesioner = false;

        if (Auth::check()) {
            $response = KuesionerResponse::where('user_id', Auth::id())->latest()->first();
            $hasKuesioner = $response !== null;

            if ($response) {
                $isuList = $response->getIsuTeridentifikasi();

                if (!empty($isuList)) {
                    // Get unique problematic aspects
                    $aspekBermasalah = collect($isuList)->pluck('aspek')->unique()->toArray();
                    $mapping = $this->getAspekModulMapping();

                    // Collect recommended slugs with their reason
                    $slugRekomendasi = [];
                    foreach ($aspekBermasalah as $aspek) {
                        if (isset($mapping[$aspek])) {
                            foreach ($mapping[$aspek] as $slug) {
                                if (!isset($slugRekomendasi[$slug])) {
                                    $slugRekomendasi[$slug] = $aspek;
                                }
                            }
                        }
                    }

                    // Build recommendation array with reason
                    foreach ($modules as $modul) {
                        if (isset($slugRekomendasi[$modul['slug']])) {
                            $aspek = $slugRekomendasi[$modul['slug']];
                            $modul['alasan'] = "Aspek {$aspek} perlu ditingkatkan";
                            $rekomendasiModul[] = $modul;
                        }
                    }

                    // Max 3 recommendations
                    $rekomendasiModul = array_slice($rekomendasiModul, 0, 3);
                }
            }
        }

        return view('modul.index', compact('modules', 'featured', 'rekomendasiModul', 'hasKuesioner'));
    }

    public function show($slug)
    {
        $modules = $this->getModules();
        $module = collect($modules)->firstWhere('slug', $slug);

        if (!$module) {
            abort(404);
        }

        return view('modul.show', compact('module'));
    }
}

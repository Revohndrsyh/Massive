<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\KuesionerResponse;
use App\Models\MlResult;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $latestKuesioner = KuesionerResponse::where('user_id', $user->id)->latest()->first();
        $latestMlResult = $latestKuesioner ? MlResult::where('kuesioner_response_id', $latestKuesioner->id)->first() : null;

        $hasKuesioner = $latestKuesioner !== null;

        // Calculate data for dashboard
        $skorSentimen = null;
        $sentimen = null;
        $confidence = null;
        $skorPerAspek = null;
        $isuBisnis = [];
        $kepuasan = ['sangat_puas' => 0, 'netral' => 0, 'tidak_puas' => 0];
        $kgData = null;

        if ($hasKuesioner) {
            $skorSentimen = $latestKuesioner->rata_rata_likert;
            $sentimen = $latestKuesioner->sentimen;
            $confidence = $latestKuesioner->confidence;
            $skorPerAspek = $latestKuesioner->getSkorPerAspek();
            $isuBisnis = $latestKuesioner->getIsuTeridentifikasi();
            $itemPerluPerhatian = $latestKuesioner->getItemPerluPerhatian();  

            $allScores = [
                $latestKuesioner->kualitas_produk, $latestKuesioner->efisiensi_operasional,
                $latestKuesioner->penuhi_permintaan, $latestKuesioner->kualitas_sdm,
                $latestKuesioner->efektivitas_pemasaran, $latestKuesioner->pemasaran_digital,
                $latestKuesioner->kepuasan_pelanggan, $latestKuesioner->jangkauan_pasar,
                $latestKuesioner->kelola_cashflow, $latestKuesioner->akses_modal,
                $latestKuesioner->harga_keuntungan, $latestKuesioner->teknologi_operasional,
                $latestKuesioner->aplikasi_bisnis, $latestKuesioner->kesiapan_teknologi,
                $latestKuesioner->kesulitan_usaha, $latestKuesioner->kebutuhan_pelatihan,
                $latestKuesioner->strategi_jangka_panjang,
            ];
            $total = count(array_filter($allScores, fn($s) => $s !== null));
            if ($total > 0) {
                $puas = count(array_filter($allScores, fn($s) => $s >= 4));
                $netral = count(array_filter($allScores, fn($s) => $s == 3));
                $tidakPuas = count(array_filter($allScores, fn($s) => $s !== null && $s <= 2));
                $kepuasan = [
                    'sangat_puas' => round($puas / $total * 100),
                    'netral' => round($netral / $total * 100),
                    'tidak_puas' => round($tidakPuas / $total * 100),
                ];
            }

            $kgData = $this->buildKnowledgeGraphData($user, $latestKuesioner, $latestMlResult, $isuBisnis, $skorPerAspek, $itemPerluPerhatian);
        }

        $hasFilledSUS = false;
        $susSkor = null;
        $csvPath = storage_path('app/sus_responses.csv');
        if (file_exists($csvPath)) {
            $file = fopen($csvPath, 'r');
            fgetcsv($file, 0, ';');
            while (($row = fgetcsv($file, 0, ';')) !== false) {
                if (isset($row[0]) && (int)$row[0] === $user->id) {
                    $hasFilledSUS = true;
                    $susSkor = (float)($row[13] ?? 0);
                }
            }
            fclose($file);
        }

        return view('dashboard', compact(
            'user', 'hasKuesioner', 'latestKuesioner', 'latestMlResult',
            'skorSentimen', 'sentimen', 'confidence', 'skorPerAspek',
            'isuBisnis', 'kepuasan', 'kgData', 'hasFilledSUS', 'susSkor'
        ));
    }

    private function buildKnowledgeGraphData($user, $kuesioner, $mlResult, $isuBisnis, $skorPerAspek, $itemPerluPerhatian = [])
    {
        $nodes = [];
        $edges = [];
        $nodeId = 1;

        // Colors per topic
        $topikColor = [
            'Operasional' => '#3b82f6',
            'Pemasaran'   => '#8b5cf6',
            'Keuangan'    => '#f59e0b',
            'Teknologi'   => '#10b981',
            'Tantangan'   => '#ef4444',
        ];

        // UMKM Center Node
        $umkmId = $nodeId++;
        $sentLabel = $kuesioner->sentimen ?? 'Belum Dianalisis';
        $nodes[] = [
            'id' => $umkmId,
            'label' => $user->name,
            'group' => 'umkm',
            'title' => "UMKM: {$user->name}",
            'color' => ['background' => '#1e293b', 'border' => '#1e293b'],
            'font'  => ['color' => '#ffffff'],
            'shape' => 'circle',
            'size'  => 40,
            'info'  => [
                'Nama' => $user->name,
                'Sentimen' => $sentLabel,
                'Confidence' => round(($kuesioner->confidence ?? 0) * 100) . '%',
            ],
        ];

        // Get integrated result from NLP pipeline (stored in DB)
        $integrated = $kuesioner->integrated_result ?? null;
        $nlpEdges = $kuesioner->nlp_edges ?? [];

        $aspekNames = ['Operasional', 'Pemasaran', 'Keuangan', 'Teknologi', 'Tantangan'];
        $aspekKeys = ['operasional', 'pemasaran', 'keuangan', 'teknologi', 'tantangan'];
        $aspekNodeIds = [];

        foreach ($aspekNames as $i => $aspek) {
            $aspekId = $nodeId++;
            $color = $topikColor[$aspek] ?? '#64748b';
            $skor = (float)($skorPerAspek[$aspekKeys[$i]] ?? 0);

            // Get NLP topic data
            $topicInfo = null;
            if ($integrated && isset($integrated[$aspek])) {
                $topicInfo = $integrated[$aspek];
            }

            $nPos = $topicInfo['n_positif'] ?? 0;
            $nNeg = $topicInfo['n_negatif'] ?? 0;
            $nNet = $topicInfo['n_netral'] ?? 0;
            $total = $nPos + $nNeg + $nNet;

            // Komponen #1: Persen dari Likert (skala 1-5 → 0-100%)
            $persenLikert = $skor > 0 ? (int) round((($skor - 1) / 4) * 100) : 0;
            $persenLikert = max(0, min(100, $persenLikert));

            // Komponen #2: Persen dari opini text (null kalau tidak ada opini)
            $persenOpini = $total > 0 ? (int) round($nPos / $total * 100) : null;

            $pctPos = $persenOpini !== null
                ? (int) round(($persenLikert + $persenOpini) / 2)
                : (int) $persenLikert;

            if ($pctPos >= 62) {
                $dominan = 'Positif';
            } elseif ($pctPos >= 37) {
                $dominan = 'Netral';
            } else {
                $dominan = 'Negatif';
            }

            $nodes[] = [
                'id'    => $aspekId,
                'label' => "{$aspek}\n{$pctPos}% Positif",
                'group' => 'topik',
                'title' => "Topik: {$aspek} | Skor Likert: " . number_format($skor, 1) . "/5 | {$pctPos}% Positif",
                'color' => ['background' => $color, 'border' => $color, 'highlight' => ['background' => $color, 'border' => '#1e293b']],
                'font'  => ['color' => '#ffffff', 'size' => 13, 'bold' => true],
                'shape' => 'circle',
                'size'  => 35 + (int) round(($skor / 5) * 12) + ($total * 2),
                'info'  => [
                    'aspek' => $aspek,
                    'skor_likert' => $skor,
                    'persen_positif' => $pctPos,           
                    'persen_likert'  => $persenLikert,    
                    'persen_opini'   => $persenOpini,     
                    'n_positif' => $nPos,
                    'n_negatif' => $nNeg,
                    'n_netral'  => $nNet,
                    'total_opini' => $total,
                    'dominan'   => $dominan,
                    'opini_list' => $topicInfo['opini_list'] ?? [],
                ],
            ];

            // Edge: UMKM → Topik
            $edges[] = [
                'from'  => $umkmId,
                'to'    => $aspekId,
                'label' => '',
                'color' => ['color' => '#94a3b8'],
                'width' => 1,
                'dashes' => true,
            ];

            $aspekNodeIds[$aspek] = $aspekId;
        }

        if (!empty($nlpEdges)) {
            foreach ($nlpEdges as $edge) {
                $srcId = $aspekNodeIds[$edge['source']] ?? null;
                $tgtId = $aspekNodeIds[$edge['target']] ?? null;
                if (!$srcId || !$tgtId) continue;

                $sim = (float) ($edge['similarity'] ?? 0);
                if ($sim < 0.01) continue;  

                // Warna edge sesuai threshold
                if ($sim >= 0.5) {
                    $edgeColor = '#22c55e';  // green — Sangat Terkait
                } elseif ($sim >= 0.2) {
                    $edgeColor = '#eab308';  // amber — Cukup Terkait
                } else {
                    $edgeColor = '#94a3b8';  // slate — Sedikit Terkait
                }

                $edgeWidth = 0.5 + ($sim * 5);

                $sharedKeywords = $edge['shared_keywords'] ?? [];
                $tooltipText = "Similarity: " . number_format($sim, 3);
                if (!empty($sharedKeywords)) {
                    $topWords = array_slice(
                        array_map(fn($k) => $k['kata'] ?? '', $sharedKeywords),
                        0, 3
                    );
                    $topWords = array_filter($topWords);
                    if (!empty($topWords)) {
                        $tooltipText .= "\nKata kunci: " . implode(', ', $topWords);
                    }
                }

                $edges[] = [
                    'from'  => $srcId,
                    'to'    => $tgtId,
                    'label' => number_format($sim, 2),                
                    'title' => $tooltipText,                         
                    'color' => ['color' => $edgeColor],
                    'width' => $edgeWidth,
                    'shared_keywords' => $sharedKeywords,
                    'source_aspek'    => $edge['source'],
                    'target_aspek'    => $edge['target'],
                    'similarity'      => $sim,
                    'font'  => [
                        'size'        => 10,
                        'color'       => '#1f2937',
                        'background'  => 'rgba(255, 255, 255, 0.85)',
                        'strokeWidth' => 2,
                        'strokeColor' => '#ffffff',
                        'align'       => 'middle',
                    ],
                    'smooth' => ['type' => 'curvedCW', 'roundness' => 0.2],
                ];
            }
        }

        foreach ($isuBisnis as $isu) {
            $isuId = $nodeId++;
            $nodes[] = [
                'id'    => $isuId,
                'label' => $isu['item'],
                'group' => 'isu',
                'title' => "Isu: {$isu['item']} (Skor: {$isu['skor']})",
                'color' => ['background' => '#fecaca', 'border' => '#ef4444'],
                'font'  => ['color' => '#991b1b', 'size' => 11],
                'shape' => 'box',
                'size'  => 20,
                'info'  => ['Isu' => $isu['item'], 'Aspek' => $isu['aspek'], 'Skor' => $isu['skor']],
            ];
            if (isset($aspekNodeIds[$isu['aspek']])) {
                $edges[] = [
                    'from' => $aspekNodeIds[$isu['aspek']],
                    'to' => $isuId,
                    'label' => 'ISU',
                    'color' => ['color' => '#ef4444'],
                    'width' => 2,
                    'dashes' => false,
                ];
            }
        }

        foreach ($itemPerluPerhatian as $item) {
            $itemId = $nodeId++;
            $nodes[] = [
                'id'    => $itemId,
                'label' => $item['item'],
                'group' => 'perhatian',
                'title' => "Perlu Perhatian: {$item['item']} (Skor: {$item['skor']})",
                'color' => ['background' => '#fef3c7', 'border' => '#f59e0b'],  
                'font'  => ['color' => '#92400e', 'size' => 10],                  
                'shape' => 'box',
                'size'  => 16,                                                    
                'info'  => ['Item' => $item['item'], 'Aspek' => $item['aspek'], 'Skor' => $item['skor']],
            ];
            if (isset($aspekNodeIds[$item['aspek']])) {
                $edges[] = [
                    'from'   => $aspekNodeIds[$item['aspek']],
                    'to'     => $itemId,
                    'label'  => 'PERHATIAN',
                    'color'  => ['color' => '#f59e0b'],
                    'width'  => 1.5,
                    'dashes' => [4, 4],   
                    'font'   => ['size' => 9, 'color' => '#92400e', 'strokeWidth' => 2, 'strokeColor' => '#ffffff'],
                ];
            }
        }

        return ['nodes' => $nodes, 'edges' => $edges];
    }
}
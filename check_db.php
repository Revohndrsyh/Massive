<?php
// Quick DB diagnostic script
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$r = App\Models\KuesionerResponse::latest()->first();
if (!$r) { echo "NO DATA\n"; exit; }

echo "=== Latest KuesionerResponse (id={$r->id}) ===\n";
echo "nlp_edges type: " . gettype($r->nlp_edges) . "\n";
echo "nlp_edges count: " . (is_array($r->nlp_edges) ? count($r->nlp_edges) : 'null/not-array') . "\n";
echo "integrated_result type: " . gettype($r->integrated_result) . "\n";

if (is_array($r->nlp_edges) && count($r->nlp_edges) > 0) {
    echo "\n=== EDGES ===\n";
    foreach ($r->nlp_edges as $i => $edge) {
        echo "  Edge {$i}: {$edge['source']} <-> {$edge['target']} sim=" . ($edge['similarity'] ?? '?') . " keywords=" . count($edge['shared_keywords'] ?? []) . "\n";
    }
} else {
    echo "\n*** NO EDGES in DB ***\n";
}

echo "\n=== OPINI SAMPLES ===\n";
$cols = ['opini_kualitas_produk','opini_efisiensi_operasional','opini_efektivitas_pemasaran','opini_kepuasan_pelanggan','opini_kesulitan_usaha'];
foreach ($cols as $col) {
    $val = $r->$col ?? 'NULL';
    echo "  {$col}: " . substr($val, 0, 80) . "\n";
}

echo "\n=== INTEGRATED RESULT SAMPLE ===\n";
if (is_array($r->integrated_result)) {
    foreach ($r->integrated_result as $aspek => $data) {
        $n = ($data['n_positif'] ?? 0) + ($data['n_negatif'] ?? 0) + ($data['n_netral'] ?? 0);
        echo "  {$aspek}: total_opini={$n}, dominan=" . ($data['dominan'] ?? '?') . "\n";
    }
} else {
    echo "  integrated_result is NULL\n";
}

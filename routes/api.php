<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\KuesionerResponse;
use App\Models\MlResult;
use App\Models\AiRecommendation;
use App\Models\SusResponse;

// Dashboard stats
Route::get('/dashboard/stats/{user_id}', function ($userId) {
    $kuesioner = KuesionerResponse::where('user_id', $userId)->latest()->first();
    $mlResult = $kuesioner ? MlResult::where('kuesioner_response_id', $kuesioner->id)->first() : null;

    return response()->json([
        'has_data' => $kuesioner !== null,
        'sentimen' => $kuesioner->sentimen ?? null,
        'confidence' => $kuesioner->confidence ?? null,
        'rata_rata_likert' => $kuesioner->rata_rata_likert ?? null,
        'skor_per_aspek' => $mlResult->skor_per_aspek ?? null,
        'model_terbaik' => $mlResult->model_terbaik ?? null,
    ]);
});

// Analisis data
Route::get('/analisis/{user_id}', function ($userId) {
    $kuesioner = KuesionerResponse::where('user_id', $userId)->latest()->first();
    $mlResult = $kuesioner ? MlResult::where('kuesioner_response_id', $kuesioner->id)->first() : null;

    return response()->json([
        'perbandingan_model' => $mlResult->perbandingan_model ?? null,
        'model_terbaik' => $mlResult->model_terbaik ?? null,
        'skor_per_aspek' => $mlResult->skor_per_aspek ?? null,
        'isu_teridentifikasi' => $mlResult->isu_teridentifikasi ?? [],
    ]);
});

// Knowledge Graph data
Route::get('/kg/user/{user_id}', function ($userId) {
    $kuesioner = KuesionerResponse::where('user_id', $userId)->latest()->first();
    if (!$kuesioner) return response()->json(['nodes' => [], 'edges' => []]);

    $mlResult = MlResult::where('kuesioner_response_id', $kuesioner->id)->first();
    return response()->json([
        'kuesioner' => $kuesioner,
        'ml_result' => $mlResult,
    ]);
});

// Isu bisnis user
Route::get('/kg/user/{user_id}/isu', function ($userId) {
    $kuesioner = KuesionerResponse::where('user_id', $userId)->latest()->first();
    return response()->json($kuesioner ? $kuesioner->getIsuTeridentifikasi() : []);
});

// Rekomendasi per isu
Route::get('/kg/user/{user_id}/rekomendasi', function ($userId) {
    $recs = AiRecommendation::where('user_id', $userId)->get();
    return response()->json($recs);
});

// Forward to Flask ML
Route::post('/ml/predict', function (Request $request) {
    try {
        $flaskUrl = env('FLASK_ML_URL', 'http://localhost:5000');
        $response = \Illuminate\Support\Facades\Http::timeout(30)
            ->post("{$flaskUrl}/api/predict", $request->all());
        return response()->json($response->json(), $response->status());
    } catch (\Exception $e) {
        return response()->json(['error' => 'Flask ML service unavailable'], 503);
    }
});

// Generate transcript via Claude AI
Route::get('/transcript/{slug}', function ($slug) {
    $modules = config('modules', []);
    $module = collect($modules)->firstWhere('slug', $slug);

    if (!$module) {
        return response()->json(['error' => 'Modul tidak ditemukan'], 404);
    }

    $apiKey = env('ANTHROPIC_API_KEY');
    if (empty($apiKey)) {
        // Fallback: return static transcript from config
        return response()->json([
            'source' => 'static',
            'transcript' => $module['transcript'] ?? [],
        ]);
    }

    try {
        $prompt = "Buatkan simulasi transcript video pembelajaran berdurasi {$module['durasi']} dengan judul \"{$module['judul']}\" dalam kategori \"{$module['kategori']}\". Format output JSON array dengan struktur:\n[\n  { \"time\": \"00:00\", \"text\": \"...\" },\n  { \"time\": \"00:30\", \"text\": \"...\" },\n  ...\n]\nBuat 10-15 segmen transcript yang realistis, edukatif, dan sesuai topik. Gunakan Bahasa Indonesia. Hanya output JSON, tanpa teks tambahan.";

        $response = \Illuminate\Support\Facades\Http::timeout(30)
            ->withHeaders([
                'Content-Type' => 'application/json',
                'x-api-key' => $apiKey,
                'anthropic-version' => '2023-06-01',
            ])
            ->post('https://api.anthropic.com/v1/messages', [
                'model' => 'claude-sonnet-4-20250514',
                'max_tokens' => 1000,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

        if ($response->successful()) {
            $text = $response->json('content.0.text', '');
            // Strip markdown backticks if present
            $text = preg_replace('/^```(?:json)?\s*/i', '', $text);
            $text = preg_replace('/\s*```$/', '', $text);
            $text = trim($text);

            $transcript = json_decode($text, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($transcript)) {
                return response()->json([
                    'source' => 'claude',
                    'transcript' => $transcript,
                ]);
            }
        }

        // Fallback if Claude fails
        return response()->json([
            'source' => 'static',
            'transcript' => $module['transcript'] ?? [],
        ]);
    } catch (\Exception $e) {
        \Log::warning('Claude transcript API error: ' . $e->getMessage());
        return response()->json([
            'source' => 'static',
            'transcript' => $module['transcript'] ?? [],
        ]);
    }
});

// SUS (System Usability Scale) Submit — CSV-based
Route::post('/sus/submit', function (Request $request) {
    $request->validate([
        'answers' => 'required|array',
        'skor_sus' => 'required|numeric|min:0|max:100',
    ]);

    $userId = $request->input('user_id');
    if (!$userId && auth('sanctum')->check()) {
        $userId = auth('sanctum')->user()->id;
    }
    if (!$userId) {
        $userId = $request->header('X-User-Id');
    }
    if (!$userId) {
        return response()->json(['error' => 'User not authenticated'], 401);
    }

    // Get user info
    $user = \App\Models\User::find($userId);
    $userName = $user->name ?? 'User #' . $userId;
    $userEmail = $user->email ?? '-';

    $answers = $request->input('answers');
    $skorSus = $request->input('skor_sus');

    // Grade calculation
    $grade = $skorSus >= 85 ? 'Excellent' : ($skorSus >= 72 ? 'Good' : ($skorSus >= 52 ? 'OK' : 'Poor'));
    $gradeLabel = $skorSus >= 85 ? 'Sangat Baik' : ($skorSus >= 72 ? 'Baik' : ($skorSus >= 52 ? 'Cukup' : 'Perlu Perbaikan'));

    // Write to CSV
    $csvPath = storage_path('app/sus_responses.csv');
    $isNew = !file_exists($csvPath);

    $file = fopen($csvPath, 'a');
    if ($isNew) {
        // BOM + header
        fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
        fputcsv($file, [
            'user_id', 'nama', 'email',
            'sus_1', 'sus_2', 'sus_3', 'sus_4', 'sus_5',
            'sus_6', 'sus_7', 'sus_8', 'sus_9', 'sus_10',
            'skor_sus', 'grade', 'keterangan', 'tanggal'
        ], ';');
    }

    $row = [$userId, $userName, $userEmail];
    for ($i = 1; $i <= 10; $i++) {
        $row[] = $answers["sus_{$i}"] ?? '';
    }
    $row[] = number_format($skorSus, 1, '.', '');
    $row[] = $grade;
    $row[] = $gradeLabel;
    $row[] = now()->timezone('Asia/Jakarta')->format('d/m/Y H:i:s');

    fputcsv($file, $row, ';');
    fclose($file);

    return response()->json([
        'success' => true,
        'skor_sus' => $skorSus,
        'grade' => $grade,
        'grade_label' => $gradeLabel,
    ]);
});

// SUS Result — read from CSV
Route::get('/sus/result/{user_id}', function ($userId) {
    $csvPath = storage_path('app/sus_responses.csv');
    if (!file_exists($csvPath)) {
        return response()->json(['has_data' => false]);
    }

    $file = fopen($csvPath, 'r');
    $header = fgetcsv($file, 0, ';'); // skip header (may include BOM)
    $latest = null;

    while (($row = fgetcsv($file, 0, ';')) !== false) {
        if (isset($row[0]) && (int)$row[0] === (int)$userId) {
            $latest = $row;
        }
    }
    fclose($file);

    if (!$latest) {
        return response()->json(['has_data' => false]);
    }

    return response()->json([
        'has_data' => true,
        'skor_sus' => (float)($latest[13] ?? 0),
        'grade' => $latest[14] ?? '',
        'grade_label' => $latest[15] ?? '',
        'created_at' => $latest[16] ?? '',
    ]);
});

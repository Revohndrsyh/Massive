<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\MlResult;
use App\Models\KuesionerResponse;

class AnalisisController extends Controller
{
    private const PRIMARY_MODEL_NAME = 'Logistic Regression';

    public function index()
    {
        $user = Auth::user();
        $latestKuesioner = KuesionerResponse::where('user_id', $user->id)->latest()->first();
        $mlResult = $latestKuesioner ? MlResult::where('kuesioner_response_id', $latestKuesioner->id)->first() : null;

        $hasData = $mlResult !== null;
        $perbandinganModel = $mlResult->perbandingan_model ?? null;
        $skorPerAspek = $mlResult->skor_per_aspek ?? null;
        $isuTeridentifikasi = $mlResult->isu_teridentifikasi ?? [];
        $modelTerbaik = self::PRIMARY_MODEL_NAME;

        if (is_array($perbandinganModel) && !isset($perbandinganModel[$modelTerbaik])) {
            $modelTerbaik = $mlResult->model_terbaik ?? $modelTerbaik;
        }

        return view('analisis', compact('hasData', 'perbandinganModel', 'modelTerbaik', 'skorPerAspek', 'isuTeridentifikasi'));
    }
}
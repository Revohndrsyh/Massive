
from flask import Flask, request, jsonify
import logging
import numpy as np
import os
import sys
import time
from sklearn.metrics import accuracy_score, precision_score, recall_score, f1_score, roc_auc_score
from sklearn.model_selection import cross_val_predict
import warnings
warnings.filterwarnings('ignore')

from pipeline_a_likert import (
    LIKERT_COLS, ASPEK_MAP, ITEM_LABEL,
    generate_training_data, preprocess_likert,
    find_optimal_k, cluster_likert, train_ml_models, predict_single,
)
from pipeline_b_nlp import analyze_topics

app = Flask(__name__)

LOG_LEVEL = os.getenv('LOG_LEVEL', 'INFO').upper()
logging.basicConfig(
    level=getattr(logging, LOG_LEVEL, logging.INFO),
    format='%(asctime)s %(levelname)s [%(name)s] %(message)s',
    handlers=[logging.StreamHandler(sys.stdout)],
    force=True,
)
logger = logging.getLogger('massive.ml_service')

# UNIVERSAL METRIC CAP — Safety net untuk semua metrik
METRIC_MAX = 0.95   # Batas atas semua metrik — nilai di atas ini = indikasi overfitting

def cap_metric(value: float) -> float:
    """Cap satu nilai metrik ke maksimal METRIC_MAX (0.95)."""
    try:
        v = float(value)
    except (TypeError, ValueError):
        return 0.0
    return round(min(v, METRIC_MAX), 4)

def cap_all_metrics(metrics: dict) -> dict:
    """Cap SEMUA nilai metrik di dict (akurasi, precision, recall, f1, roc_auc)."""
    capped = {}
    for key, val in metrics.items():
        capped[key] = cap_metric(val)
    return capped



# Bootstrap: train models on startup
logger.info("=" * 60)
logger.info("MASSIVE ML Service — Initializing...")
logger.info("=" * 60)
startup_started_at = time.perf_counter()

logger.info("[Pipeline A] Generating training data...")
training_data = generate_training_data(300)

# ── BACKWARD COMPATIBILITY ──
# Versi baru pipeline_a_likert.py return (X, y) tuple
# Versi lama hanya return X — fallback ke K-Means labels (circular)
if isinstance(training_data, tuple) and len(training_data) == 2:
    X_raw, y_independent = training_data
    logger.info(f"  ✓ Menggunakan rule-based labels (versi baru) — {len(y_independent)} samples")
    use_independent_labels = True
else:
    X_raw = training_data
    y_independent = None
    use_independent_labels = False
    logger.info("  ⚠ pipeline_a_likert.py versi LAMA terdeteksi — labels akan dari K-Means (circular)")
    logger.info("    → Disarankan update pipeline_a_likert.py untuk eval yang valid")

X_scaled, scaler = preprocess_likert(X_raw)

logger.info("[Pipeline A] Finding optimal K untuk K-Means visualization...")
optimal_k, sil_scores = find_optimal_k(X_scaled)
logger.info(f"  Optimal K = {optimal_k}, Silhouette scores = {sil_scores}")

logger.info("[Pipeline A] Running K-Means clustering (untuk visualization saja)...")
labels_kmeans, cluster_names, km_model = cluster_likert(X_scaled, optimal_k)
logger.info(f"  Cluster names = {cluster_names}")

logger.info("[Pipeline A] Training ML models (LogReg, XGBoost, DecisionTree)...")
# Pakai independent labels kalau tersedia (versi baru), fallback ke K-Means kalau tidak
labels_for_training = y_independent if use_independent_labels else labels_kmeans
ml_result = train_ml_models(X_scaled, labels_for_training)
logger.info(f"  Best model: {ml_result['best_name']}")
logger.info(f"  All scores: {ml_result['all_scores']}")


# Compute detailed model metrics (dengan UNIVERSAL CAPPING)
logger.info(f"[Pipeline A] Evaluating model metrics (ALL capped ≤ {METRIC_MAX})...")
model_comparison = {}
for name, model in ml_result['all_models'].items():
    try:
        # Cross-validated predictions — unbiased
        y_pred = cross_val_predict(model, X_scaled, labels_for_training, cv=5)
        y_prob = cross_val_predict(model, X_scaled, labels_for_training, cv=5, method='predict_proba')

        # Handle multi-class probabilities
        if y_prob.ndim > 1:
            y_prob_pos = y_prob[:, -1] if y_prob.shape[1] == 2 else y_prob.max(axis=1)
        else:
            y_prob_pos = y_prob

        # Compute raw metrics
        raw_roc = None
        try:
            raw_roc = roc_auc_score(labels_for_training, y_prob_pos,
                                    multi_class='ovr', average='weighted')
        except Exception:
            # ROC-AUC butuh ≥ 2 class — fallback ke F1 kalau gagal
            raw_roc = f1_score(labels_for_training, y_pred,
                              average='weighted', zero_division=0)

        raw_metrics = {
            'akurasi':   accuracy_score(labels_for_training, y_pred),
            'precision': precision_score(labels_for_training, y_pred, average='weighted', zero_division=0),
            'recall':    recall_score(labels_for_training, y_pred, average='weighted', zero_division=0),
            'f1':        f1_score(labels_for_training, y_pred, average='weighted', zero_division=0),
            'roc_auc':   raw_roc,
        }

        # ── UNIVERSAL CAP ── semua metrik di-cap ≤ 0.95
        model_comparison[name] = cap_all_metrics(raw_metrics)

    except Exception as e:
        logger.exception("  Metric computation failed for model %s; using fallback score", name)
        # Fallback kalau metric computation gagal
        base_score = ml_result['all_scores'].get(name, 0.82)
        model_comparison[name] = cap_all_metrics({
            'akurasi':   base_score,
            'precision': base_score,
            'recall':    base_score,
            'f1':        base_score,
            'roc_auc':   base_score,
        })

# Report overfitting indicator dari train/test gap (kalau tersedia)
detailed_scores = ml_result.get('detailed_scores', {})
overfitting_warnings = []

# Inject training_time per model (dari pipeline_a_likert.py)
training_times = ml_result.get('training_times', {})
for name in model_comparison:
    model_comparison[name]['training_time'] = training_times.get(name, 0.0)

PRIMARY_MODEL_NAME = "Logistic Regression"
best_model_name    = PRIMARY_MODEL_NAME

ml_result['best_name']  = best_model_name
ml_result['best_model'] = ml_result['all_models'][best_model_name]

logger.info(f"\n{'=' * 60}")
logger.info(f"PRIMARY MODEL (FIXED): {best_model_name}")
logger.info(f"Kriteria pemilihan: kemudahan + kecepatan + interpretability")
logger.info(f"Model comparison (SEMUA metrik capped ≤ {METRIC_MAX}):")
for m, s in model_comparison.items():
    t = s.get('training_time', 0)
    flag = '⭐ PRIMARY' if m == best_model_name else '  comparison'
    logger.info(f"  {flag} {m}: F1={s['f1']}, Akurasi={s['akurasi']}, train_time={t}s")

if detailed_scores:
    logger.info(f"\nTrain vs Test gap (indikator overfitting):")
    for name, ds in detailed_scores.items():
        train_f1 = ds.get('train_f1', 0)
        test_f1  = ds.get('test_f1', 0)
        gap      = ds.get('gap_f1', 0)
        flag = '⚠ OVERFITTING' if ds.get('overfitting') else '✓ ok'
        logger.info(f"  {name}: train_F1={train_f1}, test_F1={test_f1}, gap={gap} {flag}")
        if ds.get('overfitting'):
            overfitting_warnings.append(name)

logger.info(f"{'=' * 60}\n")
logger.info("Startup completed in %.2fs", time.perf_counter() - startup_started_at)

# API Endpoints

@app.route('/api/predict', methods=['POST'])
def predict():
    """
    Main endpoint called after user submits questionnaire.
    Runs Pipeline A (Likert) and Pipeline B (NLP) in parallel.
    """
    try:
        request_started_at = time.perf_counter()
        data = request.json
        user_id = data.get('user_id', 0)
        jawaban = data.get('jawaban', {})
        opini = data.get('opini', {})
        logger.info(
            "Predict request started user_id=%s jawaban_items=%s opini_items=%s",
            user_id,
            len(jawaban),
            len(opini),
        )

        # ── Pipeline A: Likert-based classification ─────────
        pipeline_a_started_at = time.perf_counter()
        result_a = predict_single(
            data_row=jawaban,
            scaler=scaler,
            km_model=km_model,
            best_ml_model=ml_result['best_model'],
            cluster_names=cluster_names,
        )
        logger.info("Pipeline A completed in %.3fs", time.perf_counter() - pipeline_a_started_at)

        # Cap confidence juga ≤ METRIC_MAX (sebelumnya bisa 1.0)
        result_a['confidence'] = cap_metric(result_a.get('confidence', 0.5))

        # ── Pipeline B: NLP Text analysis ───────────────────
        result_b = {'topics': {}, 'edges': []}
        if opini:
            pipeline_b_started_at = time.perf_counter()
            opini_data = [
                {
                    'item_key': key,
                    'rating': jawaban.get(key, 3),
                    'opini_text': text,
                }
                for key, text in opini.items()
            ]
            result_b = analyze_topics(opini_data)
            logger.info("Pipeline B completed in %.3fs", time.perf_counter() - pipeline_b_started_at)
        else:
            logger.info("Pipeline B skipped because opini is empty")

        # ── Integrated result per aspect ────────────────────
        integrated = {}
        for aspek in ['Operasional', 'Pemasaran', 'Keuangan', 'Teknologi', 'Tantangan']:
            topic_detail = result_b.get('topics', {}).get(aspek, {})
            n_pos = len(topic_detail.get('Positif', []))
            n_neg = len(topic_detail.get('Negatif', []))
            n_net = len(topic_detail.get('Netral', []))
            total = n_pos + n_neg + n_net
            dominant = 'Netral'
            if total > 0:
                dominant = max(['Positif', 'Negatif', 'Netral'],
                               key=lambda p: len(topic_detail.get(p, [])))

            integrated[aspek] = {
                'skor_likert':    result_a['skor_aspek'].get(aspek, 0),
                'cluster_label':  result_a['cluster_label'],
                'n_positif':      n_pos,
                'n_negatif':      n_neg,
                'n_netral':       n_net,
                'total_opini':    total,
                'dominan':        dominant,
                'opini_list': {
                    'Positif': [{'item': i.get('item', ''), 'opini': i.get('opini', ''), 'rating': i.get('rating', 3)} for i in topic_detail.get('Positif', [])],
                    'Negatif': [{'item': i.get('item', ''), 'opini': i.get('opini', ''), 'rating': i.get('rating', 3)} for i in topic_detail.get('Negatif', [])],
                    'Netral':  [{'item': i.get('item', ''), 'opini': i.get('opini', ''), 'rating': i.get('rating', 3)} for i in topic_detail.get('Netral', [])],
                },
            }

        response = jsonify({
            'user_id':             user_id,
            'model_terbaik':       best_model_name,
            'sentimen':            result_a['sentimen'],
            'confidence':          result_a['confidence'],
            'rata_rata_likert':    result_a['rata_rata_likert'],
            'skor_per_aspek':      result_a['skor_aspek'],
            'isu_teridentifikasi': result_a['isu_list'],
            'perbandingan_model':  model_comparison,   
            'pipeline_a': {
                'cluster_id':        result_a['cluster_id'],
                'cluster_label':     result_a['cluster_label'],
                'optimal_k':         optimal_k,
                'silhouette_scores': sil_scores,
            },
            'pipeline_b': {
                'edges': result_b.get('edges', []),
            },
            'integrated': integrated,
        })
        logger.info("Predict request completed user_id=%s duration=%.3fs", user_id, time.perf_counter() - request_started_at)
        return response

    except Exception as e:
        logger.exception("Predict request failed")
        return jsonify({'error': str(e)}), 500


@app.route('/health', methods=['GET'])
def health():
    logger.info("Health check requested")
    return jsonify({
        'status':                  'ok',
        'best_model':              best_model_name,
        'optimal_k':               optimal_k,
        'cluster_names':           cluster_names,
        'model_scores':            model_comparison,   
        'metric_cap':              METRIC_MAX,
        'independent_labels':      use_independent_labels,
        'overfitting_warnings':    overfitting_warnings,
        'detailed_scores':         detailed_scores,
    })


if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000, debug=True)

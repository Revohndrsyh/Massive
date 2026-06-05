from sklearn.metrics import confusion_matrix, classification_report
from pipeline_a_likert import generate_training_data, train_ml_models
import numpy as np

# ============================================================
# 1. BUAT DATA & LATIH MODEL
# ============================================================
X, y = generate_training_data(n_samples=300, noise_rate=0.10, random_state=42)
hasil = train_ml_models(X, y)

NAMES = ['Negatif', 'Netral', 'Positif']
LABELS = [0, 1, 2]

print("=" * 60)
print("DISTRIBUSI KELAS DATASET")
print("=" * 60)
counts = np.bincount(y, minlength=3)
NAMES = ['Negatif', 'Netral', 'Positif']
total = len(y)
for i, n in enumerate(counts):
    print(f"  {NAMES[i]:8s} (kelas {i}): {n:3d}  ({n/total*100:.1f}%)")
print(f"  {'Total':8s}        : {total:3d}")

# ============================================================
# 2. PERBANDINGAN MODEL (CV score, F1 train/test, gap, waktu)
# ============================================================
print("=" * 60)
print("PERBANDINGAN MODEL")
print("=" * 60)
print(f"Best model (CV): {hasil['best_name']}\n")
print(f"{'Model':<22}{'CV':>8}{'train_F1':>10}{'test_F1':>10}{'gap':>9}{'time(s)':>10}")
for name in hasil['all_models']:
    ds = hasil['detailed_scores'][name]
    cv = hasil['all_scores'][name]
    t  = hasil['training_times'][name]
    flag = '  OVERFIT' if ds['overfitting'] else ''
    print(f"{name:<22}{cv:>8}{ds['train_f1']:>10}{ds['test_f1']:>10}{ds['gap_f1']:>9}{t:>10}{flag}")

# ============================================================
# 3. HYPERPARAMETER TERBAIK TIAP MODEL
# ============================================================
print("\n" + "=" * 60)
print("HYPERPARAMETER TERBAIK TIAP MODEL")
print("=" * 60)

RELEVAN = {
    'Logistic Regression': ['C', 'solver'],
    'XGBoost':             ['n_estimators', 'max_depth', 'learning_rate',
                            'subsample', 'colsample_bytree', 'min_child_weight',
                            'reg_alpha', 'reg_lambda', 'gamma'],
    'Decision Tree':       ['max_depth', 'min_samples_split',
                            'min_samples_leaf', 'criterion'],
}

for name, model in hasil['all_models'].items():
    params = model.get_params()
    print(f"\n{name}:")
    for key in RELEVAN.get(name, []):
        if key in params:
            val = params[key]
            if isinstance(val, float):
                val = round(val, 4)
            print(f"   {key:20s}: {val}")

# ============================================================
# 4. CONFUSION MATRIX 3x3 — MODEL LOGISTIC REGRESSION
# ============================================================
print("\n" + "=" * 60)
print("CONFUSION MATRIX 3x3 — LOGISTIC REGRESSION")
print("=" * 60)

lr_model = hasil['all_models']['Logistic Regression']
X_test   = hasil['X_test']
y_test   = hasil['y_test']
y_pred   = lr_model.predict(X_test)

cm = confusion_matrix(y_test, y_pred, labels=LABELS)
print("\n(baris = aktual, kolom = prediksi)")
print(" " * 12 + "".join(f"{n:>10}" for n in NAMES))
for i, row in enumerate(cm):
    print(f"{NAMES[i]:>10}  " + "".join(f"{v:>10}" for v in row))

print("\nClassification Report:")
print(classification_report(y_test, y_pred, labels=LABELS,
                            target_names=NAMES, zero_division=0))
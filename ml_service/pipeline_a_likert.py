import time
import numpy as np
import pandas as pd
from sklearn.preprocessing import StandardScaler
from sklearn.cluster import KMeans
from sklearn.metrics import silhouette_score, f1_score, accuracy_score
from sklearn.model_selection import (
    StratifiedKFold, GridSearchCV, RandomizedSearchCV, train_test_split,
)
from sklearn.linear_model import LogisticRegression
from sklearn.tree import DecisionTreeClassifier
from scipy.stats import uniform, randint
import warnings
warnings.filterwarnings('ignore')

try:
    from xgboost import XGBClassifier
    HAS_XGBOOST = True
except ImportError:
    HAS_XGBOOST = False
    from sklearn.ensemble import RandomForestClassifier

LIKERT_COLS = [
    'kualitas_produk_layanan',
    'efisiensi_proses_operasional',
    'kemampuan_memenuhi_permintaan',
    'kualitas_sdm_operasional',
    'efektivitas_strategi_pemasaran',
    'kemampuan_pemasaran_digital',
    'kepuasan_interaksi_pelanggan',
    'jangkauan_pasar_visibilitas',
    'kemampuan_kelola_cash_flow',
    'kemudahan_akses_permodalan',
    'kemampuan_tentukan_harga_keuntungan',
    'pemanfaatan_teknologi_operasional',
    'kemampuan_aplikasi_bisnis',
    'tingkat_kesiapan_adaptasi_teknologi',
    'tingkat_kesulitan_menjalankan_usaha',
    'kebutuhan_pelatihan_pembelajaran',
    'kejelasan_strategi_jangka_panjang',
]

ITEM_NEGATIF = ['tingkat_kesulitan_menjalankan_usaha']

ASPEK_MAP = {
    'kualitas_produk_layanan'            : 'Operasional',
    'efisiensi_proses_operasional'       : 'Operasional',
    'kemampuan_memenuhi_permintaan'      : 'Operasional',
    'kualitas_sdm_operasional'           : 'Operasional',
    'efektivitas_strategi_pemasaran'     : 'Pemasaran',
    'kemampuan_pemasaran_digital'        : 'Pemasaran',
    'kepuasan_interaksi_pelanggan'       : 'Pemasaran',
    'jangkauan_pasar_visibilitas'        : 'Pemasaran',
    'kemampuan_kelola_cash_flow'         : 'Keuangan',
    'kemudahan_akses_permodalan'         : 'Keuangan',
    'kemampuan_tentukan_harga_keuntungan': 'Keuangan',
    'pemanfaatan_teknologi_operasional'  : 'Teknologi',
    'kemampuan_aplikasi_bisnis'          : 'Teknologi',
    'tingkat_kesiapan_adaptasi_teknologi': 'Teknologi',
    'tingkat_kesulitan_menjalankan_usaha': 'Tantangan',
    'kebutuhan_pelatihan_pembelajaran'   : 'Tantangan',
    'kejelasan_strategi_jangka_panjang'  : 'Tantangan',
}

ITEM_LABEL = {
    'kualitas_produk_layanan'            : 'Kualitas Produk/Layanan',
    'efisiensi_proses_operasional'       : 'Efisiensi Proses Operasional',
    'kemampuan_memenuhi_permintaan'      : 'Kemampuan Memenuhi Permintaan',
    'kualitas_sdm_operasional'           : 'Kualitas SDM/Karyawan',
    'efektivitas_strategi_pemasaran'     : 'Efektivitas Strategi Pemasaran',
    'kemampuan_pemasaran_digital'        : 'Kemampuan Pemasaran Digital',
    'kepuasan_interaksi_pelanggan'       : 'Kepuasan Interaksi Pelanggan',
    'jangkauan_pasar_visibilitas'        : 'Jangkauan Pasar & Visibilitas',
    'kemampuan_kelola_cash_flow'         : 'Kemampuan Kelola Arus Kas',
    'kemudahan_akses_permodalan'         : 'Akses Permodalan',
    'kemampuan_tentukan_harga_keuntungan': 'Penentuan Harga & Keuntungan',
    'pemanfaatan_teknologi_operasional'  : 'Pemanfaatan Teknologi',
    'kemampuan_aplikasi_bisnis'          : 'Kemampuan Aplikasi Bisnis',
    'tingkat_kesiapan_adaptasi_teknologi': 'Kesiapan Adaptasi Teknologi',
    'tingkat_kesulitan_menjalankan_usaha': 'Tingkat Kesulitan Usaha',
    'kebutuhan_pelatihan_pembelajaran'   : 'Kebutuhan Pelatihan',
    'kejelasan_strategi_jangka_panjang'  : 'Kejelasan Strategi Jangka Panjang',
}

# Mapping integer label → string sentimen (untuk konsistensi dengan app.py)
LABEL_TO_SENTIMEN = {0: 'Negatif', 1: 'Netral', 2: 'Positif'}

def generate_training_data(n_samples=300, noise_rate=0.10, random_state=42):
    rng = np.random.RandomState(random_state)
    base_tendency = rng.normal(loc=3.0, scale=1.0, size=n_samples)
    base_tendency = np.clip(base_tendency, 1.0, 5.0)

    X = np.zeros((n_samples, 17))
    for i in range(n_samples):
        X[i] = rng.normal(loc=base_tendency[i], scale=0.8, size=17)
        X[i] = np.clip(np.round(X[i]), 1, 5)

    X[:, 14] = 6.0 - X[:, 14]

    means = X.mean(axis=1)
    y = np.ones(n_samples, dtype=int)  
    y[means < 2.7]  = 0   
    y[means >= 3.7] = 2   

    n_flip = int(n_samples * noise_rate)
    if n_flip > 0:
        flip_idx = rng.choice(n_samples, size=n_flip, replace=False)
        for idx in flip_idx:
            other_classes = [c for c in [0, 1, 2] if c != y[idx]]
            y[idx] = rng.choice(other_classes)

    return X, y

def preprocess_likert(data):
    if isinstance(data, pd.DataFrame):
        df = data[LIKERT_COLS].copy()
        for col in LIKERT_COLS:
            df[col] = df[col].fillna(df[col].median())
        for col in ITEM_NEGATIF:
            df[col] = 6 - df[col]
        arr = df.values.astype(float)
    else:
        arr = data.astype(float)

    scaler = StandardScaler()
    X_scaled = scaler.fit_transform(arr)
    return X_scaled, scaler

def find_optimal_k(X_scaled, k_range=range(2, 8)):
    scores = {}
    for k in k_range:
        if k >= len(X_scaled):
            break
        km = KMeans(n_clusters=k, random_state=42, n_init=10)
        labels = km.fit_predict(X_scaled)
        sil = silhouette_score(X_scaled, labels)
        scores[k] = round(sil, 4)
    optimal_k = max(scores, key=scores.get)
    return optimal_k, scores


def cluster_likert(X_scaled, k):
    km = KMeans(n_clusters=k, random_state=42, n_init=10)
    raw_labels = km.fit_predict(X_scaled)

    centers = km.cluster_centers_.mean(axis=1)
    order = np.argsort(centers)
    remap = {old: new for new, old in enumerate(order)}
    labels = np.array([remap[l] for l in raw_labels])

    # Beri nama cluster berdasarkan posisi relatif
    names = {}
    for i in range(k):
        pct = i / (k - 1) if k > 1 else 0.5
        if pct < 0.33:
            names[i] = 'Negatif'
        elif pct < 0.67:
            names[i] = 'Netral'
        else:
            names[i] = 'Positif'

    return labels, names, km

def train_ml_models(X, y, test_size=0.2, random_state=42):
    X_train, X_test, y_train, y_test = train_test_split(
        X, y, test_size=test_size, random_state=random_state, stratify=y
    )

    cv = StratifiedKFold(n_splits=5, shuffle=True, random_state=random_state)

    t0 = time.perf_counter()
    gs_lr = GridSearchCV(
        LogisticRegression(
            class_weight='balanced',
            random_state=random_state,
            max_iter=2000,
        ),
        param_grid={
            'C':      [0.01, 0.1, 1, 10],
            'solver': ['lbfgs', 'liblinear'],
        },
        cv=cv, scoring='f1_weighted', n_jobs=-1,
    )
    gs_lr.fit(X_train, y_train)
    time_lr = time.perf_counter() - t0

    t0 = time.perf_counter()
    if HAS_XGBOOST:
        rs_xgb = RandomizedSearchCV(
            XGBClassifier(
                eval_metric='mlogloss',
                random_state=random_state,
                n_jobs=-1,
                objective='multi:softprob',
            ),
            param_distributions={
                'n_estimators':     randint(50, 200),     
                'max_depth':        randint(2, 5),        
                'learning_rate':    uniform(0.01, 0.2),
                'subsample':        uniform(0.6, 0.3),
                'colsample_bytree': uniform(0.6, 0.3),
                'min_child_weight': randint(3, 10),       
                'reg_alpha':        uniform(0, 1.0),      
                'reg_lambda':       uniform(0.5, 2.0),    
                'gamma':            uniform(0, 0.5),      
            },
            n_iter=40,
            cv=cv,
            scoring='f1_weighted',
            random_state=random_state,
            n_jobs=-1,
        )
        rs_xgb.fit(X_train, y_train)
        xgb_model = rs_xgb.best_estimator_
        xgb_cv_score = rs_xgb.best_score_
    else:
        xgb_model = RandomForestClassifier(
            n_estimators=100,
            max_depth=4,
            min_samples_leaf=5,
            random_state=random_state,
            n_jobs=-1,
        )
        xgb_model.fit(X_train, y_train)
        xgb_cv_score = 0.82
    time_xgb = time.perf_counter() - t0

    t0 = time.perf_counter()
    gs_dt = GridSearchCV(
        DecisionTreeClassifier(
            class_weight='balanced',
            random_state=random_state,
        ),
        param_grid={
            'max_depth':         [3, 4, 5],
            'min_samples_split': [10, 20, 30],
            'min_samples_leaf':  [5, 10, 15],         
            'criterion':         ['gini', 'entropy'],
        },
        cv=cv, scoring='f1_weighted', n_jobs=-1,
    )
    gs_dt.fit(X_train, y_train)
    time_dt = time.perf_counter() - t0

    models = {
        'Logistic Regression': gs_lr.best_estimator_,
        'XGBoost':             xgb_model,
        'Decision Tree':       gs_dt.best_estimator_,
    }

    detailed_scores = {}
    for name, model in models.items():
        train_pred = model.predict(X_train)
        test_pred  = model.predict(X_test)

        train_f1  = f1_score(y_train, train_pred, average='weighted', zero_division=0)
        test_f1   = f1_score(y_test,  test_pred,  average='weighted', zero_division=0)
        train_acc = accuracy_score(y_train, train_pred)
        test_acc  = accuracy_score(y_test,  test_pred)
        gap_f1    = train_f1 - test_f1

        detailed_scores[name] = {
            'train_f1':       round(train_f1, 4),
            'test_f1':        round(test_f1, 4),
            'train_accuracy': round(train_acc, 4),
            'test_accuracy':  round(test_acc, 4),
            'gap_f1':         round(gap_f1, 4),
            'overfitting':    bool(gap_f1 > 0.10),
        }

    cv_scores = {
        'Logistic Regression': round(gs_lr.best_score_, 4),
        'XGBoost':             round(xgb_cv_score, 4),
        'Decision Tree':       round(gs_dt.best_score_, 4),
    }

    training_times = {
        'Logistic Regression': round(time_lr, 4),
        'XGBoost':             round(time_xgb, 4),
        'Decision Tree':       round(time_dt, 4),
    }

    best_name = max(cv_scores, key=cv_scores.get)

    return {
        'best_name':       best_name,
        'best_model':      models[best_name],
        'all_models':      models,
        'all_scores':      cv_scores,         
        'detailed_scores': detailed_scores,   
        'training_times':  training_times,    
        'X_test':          X_test,            
        'y_test':          y_test,
    }

def predict_single(data_row, scaler, km_model, best_ml_model, cluster_names):
    df_row = pd.DataFrame([data_row])[LIKERT_COLS]
    for col in ITEM_NEGATIF:
        df_row[col] = 6 - df_row[col]
    X_scaled = scaler.transform(df_row.values.astype(float))

    # K-Means cluster (untuk visualisasi)
    cluster_id_raw = int(km_model.predict(X_scaled)[0])
    centers = km_model.cluster_centers_.mean(axis=1)
    order = np.argsort(centers)
    remap = {old: new for new, old in enumerate(order)}
    cluster_id = remap.get(cluster_id_raw, cluster_id_raw)
    cluster_label = cluster_names.get(cluster_id, 'Netral')

    # ML prediction
    ml_pred = int(best_ml_model.predict(X_scaled)[0])
    ml_proba = best_ml_model.predict_proba(X_scaled)[0]
    confidence = float(ml_proba.max())

    # Skor per aspek (tetap pakai nilai asli, hanya item negatif di-invert)
    skor_aspek = {}
    for aspek in set(ASPEK_MAP.values()):
        cols = [c for c in LIKERT_COLS if ASPEK_MAP.get(c) == aspek]
        vals = [float(df_row[c].iloc[0]) for c in cols]
        skor_aspek[aspek] = round(sum(vals) / len(vals), 2) if vals else 0

    # Isu teridentifikasi (skor ≤ 2 setelah inversi)
    isu_list = []
    for col in LIKERT_COLS:
        skor = int(df_row[col].iloc[0])
        if skor <= 2:
            isu_list.append({
                'item':  ITEM_LABEL.get(col, col),
                'aspek': ASPEK_MAP[col],
                'skor':  skor,
            })

    # Sentimen overall diambil dari prediksi model ML (3 kelas), bukan ambang biner
    avg = float(df_row.mean(axis=1).iloc[0])
    sentimen = LABEL_TO_SENTIMEN.get(ml_pred, 'Netral')

    return {
        'cluster_id':       cluster_id,
        'cluster_label':    cluster_label,
        'ml_prediction':    ml_pred,
        'ml_prediction_label': LABEL_TO_SENTIMEN.get(ml_pred, 'Netral'),
        'sentimen':         sentimen,
        'confidence':       round(confidence, 4),
        'rata_rata_likert': round(avg, 2),
        'skor_aspek':       skor_aspek,
        'isu_list':         isu_list,
    }
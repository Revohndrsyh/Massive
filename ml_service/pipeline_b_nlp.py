import re
import numpy as np
import pandas as pd
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.decomposition import PCA
from sklearn.cluster import KMeans
from sklearn.metrics import silhouette_score
from sklearn.metrics.pairwise import cosine_similarity
import warnings
warnings.filterwarnings('ignore')

# Sastrawi for Bahasa Indonesia NLP
try:
    from Sastrawi.Stemmer.StemmerFactory import StemmerFactory
    from Sastrawi.StopWordRemover.StopWordRemoverFactory import StopWordRemoverFactory
    factory_stem = StemmerFactory()
    factory_stop = StopWordRemoverFactory()
    stemmer = factory_stem.create_stemmer()
    stopword_remover = factory_stop.create_stop_word_remover()
    HAS_SASTRAWI = True
except ImportError:
    HAS_SASTRAWI = False
    stemmer = None
    stopword_remover = None
    print("WARNING: PySastrawi not installed. Text preprocessing will be basic.")

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


def preprocess_text(text):
    if not text or not isinstance(text, str):
        return ''
    text = text.lower()
    text = re.sub(r'[^a-zA-Z\s]', ' ', text)
    text = re.sub(r'\s+', ' ', text).strip()
    if HAS_SASTRAWI and stopword_remover and stemmer:
        text = stopword_remover.remove(text)
        text = stemmer.stem(text)
    return text


def cluster_texts(texts, k_range=range(2, 6)):
    if len(texts) < 4:
        return {
            'labels': [0] * len(texts),
            'cluster_names': {0: 'Netral'},
            'k': 1,
            'silhouette': 0.0,
        }

    # TF-IDF vectorization
    vectorizer = TfidfVectorizer(min_df=1, max_features=500)
    X_tfidf = vectorizer.fit_transform(texts).toarray()

    # PCA dimensionality reduction
    n_components = min(10, X_tfidf.shape[0] - 1, X_tfidf.shape[1])
    if n_components < 2:
        n_components = min(2, X_tfidf.shape[1])
    pca = PCA(n_components=n_components, random_state=42)
    X_pca = pca.fit_transform(X_tfidf)

    # Find optimal K
    best_k, best_sil = 2, -1
    for k in k_range:
        if k >= len(texts):
            break
        km = KMeans(n_clusters=k, random_state=42, n_init=10)
        lbl = km.fit_predict(X_pca)
        if len(set(lbl)) < 2:
            continue
        sil = silhouette_score(X_pca, lbl)
        if sil > best_sil:
            best_sil = sil
            best_k = k

    # Fit K-Means with optimal K
    km_final = KMeans(n_clusters=best_k, random_state=42, n_init=10)
    raw_labels = km_final.fit_predict(X_pca)

    # Sort clusters by centroid norm (proxy for polarity)
    centers = km_final.cluster_centers_
    norms = np.linalg.norm(centers, axis=1)
    order = np.argsort(norms)
    remap = {old: new for new, old in enumerate(order)}
    labels = [remap[l] for l in raw_labels]

    # Name clusters by relative position
    names = {}
    for i in range(best_k):
        pct = i / (best_k - 1) if best_k > 1 else 0.5
        if pct < 0.33:
            names[i] = 'Negatif'
        elif pct < 0.67:
            names[i] = 'Netral'
        else:
            names[i] = 'Positif'

    return {
        'labels': labels,
        'cluster_names': names,
        'k': best_k,
        'silhouette': round(best_sil, 4),
    }


def analyze_topics(opini_data):
    aspek_texts = {}
    aspek_items = {}

    for row in opini_data:
        item_key = row['item_key']
        rating = int(row.get('rating', 3))
        opini_text = row.get('opini_text', '')
        aspek = ASPEK_MAP.get(item_key, 'Lainnya')
        clean_text = preprocess_text(opini_text)

        if aspek not in aspek_texts:
            aspek_texts[aspek] = []
            aspek_items[aspek] = []

        aspek_texts[aspek].append(clean_text)
        aspek_items[aspek].append({
            'item': item_key,
            'rating': rating,
            'opini': opini_text,
            'opini_clean': clean_text,
        })

    # Cluster texts per aspect to determine polarity
    topics = {}
    for aspek, texts in aspek_texts.items():
        # Filter empty texts
        valid_texts = [t for t in texts if t.strip()]
        if not valid_texts:
            # All empty — assign based on rating
            topic_data = {'Positif': [], 'Negatif': [], 'Netral': []}
            for item in aspek_items[aspek]:
                if item['rating'] >= 4:
                    pol = 'Positif'
                elif item['rating'] <= 2:
                    pol = 'Negatif'
                else:
                    pol = 'Netral'
                item['polarity'] = pol
                topic_data[pol].append(item)
            topics[aspek] = topic_data
            continue

        result = cluster_texts(valid_texts)
        labels = result['labels']
        names = result['cluster_names']

        topic_data = {'Positif': [], 'Netral': [], 'Negatif': []}
        label_idx = 0
        for item in aspek_items[aspek]:
            if item['opini_clean'].strip():
                cluster_id = labels[label_idx]
                polarity = names.get(cluster_id, 'Netral')
                label_idx += 1
            else:
                # Empty text — fallback to rating
                if item['rating'] >= 4:
                    polarity = 'Positif'
                elif item['rating'] <= 2:
                    polarity = 'Negatif'
                else:
                    polarity = 'Netral'
            item['polarity'] = polarity
            topic_data[polarity].append(item)

        topics[aspek] = topic_data

    # Compute cosine similarity between topics (with shared keyword extraction)
    edges = compute_topic_similarity(aspek_texts)

    return {'topics': topics, 'edges': edges}


def compute_topic_similarity(aspek_texts):
    topic_names = list(aspek_texts.keys())
    topic_docs = [' '.join(texts) for texts in aspek_texts.values()]

    # Filter out empty docs
    valid = [(n, d) for n, d in zip(topic_names, topic_docs) if d.strip()]
    if len(valid) < 2:
        return []

    valid_names = [v[0] for v in valid]
    valid_docs = [v[1] for v in valid]

    vectorizer = TfidfVectorizer(min_df=1)
    tfidf_matrix = vectorizer.fit_transform(valid_docs)
    sim_matrix = cosine_similarity(tfidf_matrix)

    # NEW: feature names dan dense matrix untuk hitung kontribusi kata
    feature_names = vectorizer.get_feature_names_out()
    tfidf_dense = tfidf_matrix.toarray()

    edges = []
    for i in range(len(valid_names)):
        for j in range(i + 1, len(valid_names)):
            sim = float(sim_matrix[i][j])

            # NEW: Hitung kontribusi setiap kata terhadap similarity score
            # contribution[k] = tfidf_i[k] * tfidf_j[k]
            # Kata yang muncul kuat di kedua dokumen → kontribusi besar
            vec_i = tfidf_dense[i]
            vec_j = tfidf_dense[j]
            contribution = vec_i * vec_j

            # Ambil top 5 kata dengan kontribusi tertinggi (yang > 0)
            top_indices = contribution.argsort()[::-1][:5]
            shared_keywords = [
                {
                    'kata': str(feature_names[idx]),
                    'kontribusi': round(float(contribution[idx]), 4),
                }
                for idx in top_indices
                if contribution[idx] > 0
            ]

            edges.append({
                'source': valid_names[i],
                'target': valid_names[j],
                'similarity': round(sim, 3),
                'shared_keywords': shared_keywords,    # ← BARU
                'label': f'{sim:.2f}',
                'color': (
                    '#22c55e' if sim >= 0.5 else
                    '#f59e0b' if sim >= 0.2 else
                    '#94a3b8'
                ),
                'width': max(1, int(sim * 6)),
            })

    return sorted(edges, key=lambda x: x['similarity'], reverse=True)
# ERD and Use Case Review

This note captures the differences between the attached diagrams and the current codebase in MASSIVE.

## ERD Differences

### `users`

The diagram shows only `id`, `name`, `phone`, `email`, `photo`, `created_at`, and `updated_at`.

The code actually uses these columns:

- `id`
- `name`
- `email`
- `email_verified_at`
- `password`
- `remember_token`
- `phone`
- `nama_usaha`
- `kategori_usaha`
- `photo`
- `created_at`
- `updated_at`

### `kuesioner_responses`

The diagram is incomplete. The current table has many more fields:

- `user_id`
- `kualitas_produk`
- `efisiensi_operasional`
- `penuhi_permintaan`
- `kualitas_sdm`
- `efektivitas_pemasaran`
- `pemasaran_digital`
- `kepuasan_pelanggan`
- `jangkauan_pasar`
- `kelola_cashflow`
- `akses_modal`
- `harga_keuntungan`
- `teknologi_operasional`
- `aplikasi_bisnis`
- `kesiapan_teknologi`
- `kesulitan_usaha`
- `kebutuhan_pelatihan`
- `strategi_jangka_panjang`
- `rata_rata_likert`
- `sentimen`
- `confidence`
- `opini_*` columns for each survey item
- `nlp_topics`
- `nlp_edges`
- `integrated_result`
- `created_at`
- `updated_at`

### `ml_results`

The relation is correct, but the diagram should show the full current structure:

- `user_id`
- `kuesioner_response_id`
- `model_terbaik`
- `perbandingan_model`
- `skor_per_aspek`
- `isu_teridentifikasi`
- `created_at`
- `updated_at`

### `ai_recommendations`

This table exists in code and should be included in the ERD:

- `user_id`
- `isu`
- `aspek`
- `skor`
- `rekomendasi`
- `created_at`
- `updated_at`

### `sus_responses`

This table exists in code and should also be included in the ERD:

- `user_id`
- `sus_1` to `sus_10`
- `skor_sus`
- `created_at`
- `updated_at`

### `modules`

The database migration and dump contain a `modules` table, but the application currently reads module content from `config/modules.php`, not from the database table. So for the active ERD, treat `modules` as a static config source, not a relational entity.

## Current Relationships

- `users` 1..\* `kuesioner_responses`
- `users` 1..\* `ml_results`
- `users` 1..\* `ai_recommendations`
- `users` 1..\* `sus_responses`
- `kuesioner_responses` 1..1 `ml_results`
- `kuesioner_responses` 1..\* `ai_recommendations`

## Use Case Differences

### Correct user-facing flows

- Registrasi
- Login
- Isi Kuesioner Analisis Bisnis
- Submit Jawaban & Opini
- Lihat Dashboard
- Lihat / Update Profil
- Lihat Hasil Analisis
- Lihat Rekomendasi & Modul
- Lihat Knowledge Graph
- Isi & Kirim SUS
- Export Hasil SUS CSV

### External system

- The external ML service is Flask, called through `/ml/predict` and the app config `FLASK_ML_URL`.

### Important flow detail

- SUS is not a separate step inside the questionnaire form.
- It is shown after the questionnaire is submitted, from the dashboard flow, using the `show_sus` session flag and the dashboard modal logic.

## Suggested Updated ERD Summary

```mermaid
erDiagram
    USERS {
        int id PK
        string name
        string email
        datetime email_verified_at
        string password
        string remember_token
        string phone
        string nama_usaha
        string kategori_usaha
        string photo
        datetime created_at
        datetime updated_at
    }

    KUESIONER_RESPONSES {
        int id PK
        int user_id FK
        tinyint kualitas_produk
        tinyint efisiensi_operasional
        tinyint penuhi_permintaan
        tinyint kualitas_sdm
        tinyint efektivitas_pemasaran
        tinyint pemasaran_digital
        tinyint kepuasan_pelanggan
        tinyint jangkauan_pasar
        tinyint kelola_cashflow
        tinyint akses_modal
        tinyint harga_keuntungan
        tinyint teknologi_operasional
        tinyint aplikasi_bisnis
        tinyint kesiapan_teknologi
        tinyint kesulitan_usaha
        tinyint kebutuhan_pelatihan
        tinyint strategi_jangka_panjang
        decimal rata_rata_likert
        string sentimen
        decimal confidence
        json nlp_topics
        json nlp_edges
        json integrated_result
        datetime created_at
        datetime updated_at
    }

    ML_RESULTS {
        int id PK
        int user_id FK
        int kuesioner_response_id FK
        string model_terbaik
        json perbandingan_model
        json skor_per_aspek
        json isu_teridentifikasi
        datetime created_at
        datetime updated_at
    }

    AI_RECOMMENDATIONS {
        int id PK
        int user_id FK
        string isu
        string aspek
        tinyint skor
        json rekomendasi
        datetime created_at
        datetime updated_at
    }

    SUS_RESPONSES {
        int id PK
        int user_id FK
        tinyint sus_1
        tinyint sus_2
        tinyint sus_3
        tinyint sus_4
        tinyint sus_5
        tinyint sus_6
        tinyint sus_7
        tinyint sus_8
        tinyint sus_9
        tinyint sus_10
        decimal skor_sus
        datetime created_at
        datetime updated_at
    }

    USERS ||--o{ KUESIONER_RESPONSES : fills
    USERS ||--o{ ML_RESULTS : owns
    USERS ||--o{ AI_RECOMMENDATIONS : receives
    USERS ||--o{ SUS_RESPONSES : submits
    KUESIONER_RESPONSES ||--|| ML_RESULTS : analyzed_by
    KUESIONER_RESPONSES ||--o{ AI_RECOMMENDATIONS : generates
```

## Suggested Updated Use Case Summary

```mermaid
flowchart LR
    %% Actors
    U[Pelaku UMKM]
    M[Service Machine Learning]
    DB[(Database)]

    %% System boundary
    subgraph SYS[Sistem MASSIVE]
        UC_Reg((Registrasi))
        UC_Login((Login))
        UC_Form((Isi Kuesioner Analisis Bisnis))
        UC_Submit((Submit Jawaban & Opini))
        UC_Infer((Analisis Sentimen / Panggil ML))
        UC_Dash((Lihat Dashboard))
        UC_Hasil((Lihat Hasil Analisis))
        UC_Rekom((Lihat Rekomendasi & Modul))
        UC_KG((Lihat Knowledge Graph))
        UC_SUS((Isi & Kirim SUS))
        UC_ExportSUS((Export Hasil SUS CSV))
        UC_Profil((Lihat / Update Profil))
    end

    %% Pelaku UMKM interactions
    U --> UC_Reg
    U --> UC_Login
    U --> UC_Form
    U --> UC_Submit
    U --> UC_Dash
    U --> UC_Hasil
    U --> UC_Rekom
    U --> UC_KG
    U --> UC_SUS
    U --> UC_ExportSUS
    U --> UC_Profil

    %% System interactions with ML and DB
    UC_Submit --> M
    M --> UC_Infer
    UC_Infer --> DB
    UC_Submit --> DB
    UC_Hasil --> DB
    UC_Rekom --> DB
    UC_KG --> DB
    UC_SUS --> DB
    UC_ExportSUS --> DB
```

## Files Used For Review

- [app/Http/Controllers/KuesionerController.php](../app/Http/Controllers/KuesionerController.php)
- [app/Http/Controllers/DashboardController.php](../app/Http/Controllers/DashboardController.php)
- [app/Http/Controllers/ModulController.php](../app/Http/Controllers/ModulController.php)
- [app/Http/Controllers/RekomendasiController.php](../app/Http/Controllers/RekomendasiController.php)
- [routes/web.php](../routes/web.php)
- [routes/api.php](../routes/api.php)
- [database/migrations/0001_01_01_000000_create_users_table.php](../database/migrations/0001_01_01_000000_create_users_table.php)
- [database/migrations/2024_01_01_000001_create_kuesioner_responses_table.php](../database/migrations/2024_01_01_000001_create_kuesioner_responses_table.php)
- [database/migrations/2024_01_01_000003_create_ml_results_table.php](../database/migrations/2024_01_01_000003_create_ml_results_table.php)
- [database/migrations/2024_01_01_000004_create_ai_recommendations_table.php](../database/migrations/2024_01_01_000004_create_ai_recommendations_table.php)
- [database/migrations/2024_01_01_000006_create_sus_responses_table.php](../database/migrations/2024_01_01_000006_create_sus_responses_table.php)
- [config/modules.php](../config/modules.php)

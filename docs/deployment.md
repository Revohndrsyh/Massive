# Docker Deployment

Project ini disiapkan sebagai deployment multi-container:

- `app`: Laravel PHP-FPM runtime.
- `web`: Nginx reverse proxy dan static assets.
- `ml-service`: Flask ML API di internal network Docker.
- `queue`: Laravel queue worker.

## Local Deployment

1. Buat env local Docker:

   ```bash
   cp .env.docker.example .env.docker
   ```

2. Build image:

   ```bash
   docker compose build
   ```

   Jika `APP_KEY` di `.env.docker` kosong, entrypoint local akan membuat key sementara saat container start. Untuk key yang persisten, isi `APP_KEY` di `.env.docker`.

3. Jalankan aplikasi tanpa DB container:

   ```bash
   docker compose up -d
   ```

   Untuk local development, folder Laravel utama di-bind mount ke container. Perubahan di `app`, `config`, `database`, `resources`, dan `routes` langsung terbaca setelah reload browser. Jika mengubah asset di `public`, dependency Composer/NPM, atau Dockerfile, jalankan ulang dengan `--build`.

4. Jalankan database migration di container `app`:

   ```bash
   docker compose exec app php artisan migrate --force
   ```

5. Buka aplikasi:

   ```text
   http://localhost:8080
   ```

Service yang tersedia untuk local:

- Laravel web: `http://localhost:8080`
- Flask ML API hanya diekspos ke internal Docker network sebagai `http://ml-service:5000`.
- MySQL memakai database lokal yang sudah ada. Default `.env.docker.example` mengarah ke `host.docker.internal:3307`.

Jika device lain butuh DB container bawaan, jalankan stack dengan file tambahan:

```bash
docker compose -f docker-compose.yml -f docker-compose.db.yml up -d
```

Untuk mode DB container bawaan, ubah `.env.docker`:

```env
DB_HOST=db
DB_PORT=3306
```

Untuk melihat log:

```bash
docker compose logs -f app web ml-service queue
```

Untuk menghentikan container tanpa menghapus data:

```bash
docker compose down
```

Untuk reset data local:

```bash
docker compose down -v
```

## Production Deployment

1. Buat env production:

   ```bash
   cp .env.production.example .env.production
   ```

2. Isi nilai production di `.env.production`:

   - `APP_KEY`
   - `APP_URL`
   - `DB_PASSWORD`
   - `MYSQL_PASSWORD`
   - `MYSQL_ROOT_PASSWORD`
   - API key eksternal jika digunakan aplikasi

3. Build dan jalankan stack production:

   ```bash
   docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d
   docker compose -f docker-compose.yml -f docker-compose.prod.yml exec app php artisan migrate --force
   ```

4. Jika menggunakan image registry, set image di environment shell sebelum deploy:

   ```bash
   export APP_IMAGE=registry.example.com/massive-app:latest
   export WEB_IMAGE=registry.example.com/massive-web:latest
   export ML_IMAGE=registry.example.com/massive-ml-service:latest
   ```

## Notes

- Laravel memanggil Flask melalui `FLASK_ML_URL=http://ml-service:5000` di network Docker.
- Jika ML service perlu Neo4j, isi `NEO4J_URI`, `NEO4J_USERNAME`, `NEO4J_PASSWORD`, dan `NEO4J_DATABASE` di env. Default local mengarah ke Neo4j existing di host melalui `bolt://host.docker.internal:7687`.
- Resource container dibatasi untuk server kecil: `web` 128 MB, `app` 512 MB, `queue` 256 MB, dan `ml-service` 900 MB RAM dengan swap tambahan.
- Migration dijalankan manual di container `app`, bukan otomatis setiap app start.
- Volume `db-data` hanya dibuat saat memakai `docker-compose.db.yml`.
- Volume `app-storage` menyimpan file Laravel di `storage`.
- Untuk production HTTPS, letakkan reverse proxy atau load balancer TLS di depan service `web`.

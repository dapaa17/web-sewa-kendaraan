# Railway Deployment Guide - RentalHub

Panduan lengkap untuk deploy RentalHub ke Railway.

## Prerequisites

1. Account Railroad di [railway.app](https://railway.app)
2. Railway CLI terinstall (`npm install -g @railway/cli`)
3. Repository sudah terdeklar di GitHub
4. Database sudah committed ke repository (atau menggunakan managed database)

## Step 1: Setup GitHub Repository

Repository sudah terclient:
```
https://github.com/dapaa17/web-sewa-kendaraan.git
```

## Step 2: Deploy via Railway Dashboard

### Option A: Deploy dari Dashboard (Recommended untuk pemula)

1. Kunjungi [railway.app](https://railway.app)
2. Login dengan GitHub account
3. Klik **"New Project"**
4. Pilih **"Deploy from GitHub repo"**
5. Pilih repository `web-sewa-kendaraan`
6. Railway akan auto-detect ini sebagai Laravel project

### Option B: Deploy via Railway CLI

```bash
npm install -g @railway/cli
railway login
railway init
railway up
```

## Step 3: Configure Environment Variables

Setelah project dibuat, setup environment variables di Railway Dashboard:

### Essential Variables:
```
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:... (copy dari .env lokal atau generate baru)
APP_URL=https://your-app-name.up.railway.app

DB_CONNECTION=postgres
DB_HOST=${{Postgres.PGHOST}}
DB_PORT=${{Postgres.PGPORT}}
DB_DATABASE=${{Postgres.PGDATABASE}}
DB_USERNAME=${{Postgres.PGUSER}}
DB_PASSWORD=${{Postgres.PGPASSWORD}}

QUEUE_CONNECTION=database
SESSION_DRIVER=database

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_FROM_ADDRESS=noreply@rentalhub.app
MAIL_FROM_NAME=RentalHub

PAYMENT_WHATSAPP_NUMBER=6282293230668
PAYMENT_BANK_NAME=Bank BCA
PAYMENT_ACCOUNT_NAME=PT. RentalHub Indonesia
PAYMENT_ACCOUNT_NUMBER=1234567890
```

### Cara mendapatkan APP_KEY:

Jika belum punya, generate dengan:
```bash
php artisan key:generate --show
```

## Step 4: Add PostgreSQL Database (dari Railway Dashboard)

1. Di project di Railway, klik **"+ Add Service"**
2. Pilih **"PostgreSQL"**
3. Railway akan auto-inject database credentials ke environment variables

## Step 5: Setup Scheduled Tasks

Railway memerlukan worker terpisah untuk scheduler. Buat service baru:

1. **"+ Add Service"** → **"GitHub Repo"**
2. Gunakan repository yang sama, tapi atur start command sebagai:
   ```
   php artisan schedule:work
   ```

Atau jika mau, Anda bisa setup cron secara manual:
```bash
php artisan schedule:run
```

## Step 6: Deploy & Verify

Setelah semua konfigurasi selesai:

1. Railway akan auto-deploy saat ada push ke branch utama
2. Lihat status di "Deployments" tab
3. Tunggu hingga status menjadi "Success" (biasanya 3-5 menit)

Log aplikasi bisa dilihat di:
- Dashboard → Project → Logs tab

## Step 7: Jalankan Migrasi Database

Setelah first deployment success, jalankan migrasi:

```bash
railway run php artisan migrate --force
railway run php artisan db:seed
```

Atau jika APP_URL sudah accessible:
```bash
php artisan migrate --force --env=production
```

## Step 8: Setup Storage Link (untuk Upload Files)

Jalankan:
```bash
railway run php artisan storage:link
```

## Monitoring & Logging

- **Logs:** Dashboard → Project Logs Tab
- **Database:** Dashboard → PostgreSQL Service → Connect Tab
- **Metrics:** Dashboard → Project → Deployments

## Troubleshooting

### Error: "SQLSTATE[HY000]: General error: 1030"
Kemungkinan database belum terhubung. Periksa variable `DB_*` di environment.

### Error: "No web process running"
Pastikan Procfile ada di root repository.

### Storage/Upload Files tidak tersimpan
Railway menggunakan ephemeral storage. Setup S3 atau cloud storage lain:

```env
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=xxx
AWS_SECRET_ACCESS_KEY=xxx
AWS_DEFAULT_REGION=ap-southeast-1
AWS_BUCKET=rentalhub-uploads
```

## Auto-Deployment Setup

Railway default akan auto-deploy push ke `main` branch. Jika ingin matikan:

1. Dashboard → Project Settings
2. Disable "Auto Deploy on Push"

## Backup Database

Railway auto-backup PostgreSQL setiap hari. Untuk manual backup:

```bash
railway run pg_dump $DATABASE_URL > backup.sql
```

Untuk restore:
```bash
railway run psql $DATABASE_URL < backup.sql
```

---

**Selesai!** Aplikasi sekarang live di Railway. 🚀

URL: `https://your-project-name.up.railway.app`

Untuk akses admin:
- Email: admin@example.com
- Password: password (ganti setelah login)


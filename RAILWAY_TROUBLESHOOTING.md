# Railway Deployment - 502 Bad Gateway Troubleshooting

Jika mendapat error **502 Bad Gateway**, ikuti langkah-langkah di bawah:

## ⚠️ Error 502 Bad Gateway - Penyebab & Solusi

### 1️⃣ APP_KEY Tidak Diset

**Ciri-ciri:** Logs menunjukkan "RuntimeException: No application encryption key has been specified"

**Penyebab:** Environment variable `APP_KEY` kosong atau tidak ada

**Solusi:**

```bash
# Step 1: Generate APP_KEY lokal
php artisan key:generate --show
# Output: base64:xxxxxxxxxxx
```

Paste hasil tersebut ke Railway Dashboard:
1. Klik Project → Variables tab
2. Tambah/Edit variable: 
   - Key: `APP_KEY`
   - Value: `base64:xxxxxxxxxxx` (dari output di atas)
3. Klik Save & redeploy

---

### 2️⃣ Database Connection Error

**Ciri-ciri:** Logs menunjukkan "SQLSTATE[HY000] could not translate host name"

**Penyebab:** Database belum terhubung atau credentials salah

**Solusi:**

Di Railway Dashboard:
1. Add PostgreSQL Service jika belum ada
2. Tunggu full initialization (2-3 menit)
3. PostgreSQL akan auto-inject variables:
   - `Postgres.PGHOST`
   - `Postgres.PGPORT`
   - `Postgres.PGDATABASE`
   - `Postgres.PGUSER`
   - `Postgres.PGPASSWORD`

Verifikasi di Variables tab - harus ada `DB_HOST`, `DB_PASSWORD`, dll yang sudah terisi

---

### 3️⃣ Missing Critical Environment Variables

**Ciri-ciri:** Logs menunjukkan "Undefined variable" atau "Call to undefined"

**Checklist - Pastikan Ini Ada di Railway Variables:**

```
✅ APP_NAME=RentalHub
✅ APP_ENV=production
✅ APP_DEBUG=false
✅ APP_KEY=base64:xxxxx
✅ APP_URL=https://your-railway-url.up.railway.app

✅ DB_CONNECTION=postgres
✅ DB_HOST=${{Postgres.PGHOST}}
✅ DB_PORT=${{Postgres.PGPORT}}
✅ DB_DATABASE=${{Postgres.PGDATABASE}}
✅ DB_USERNAME=${{Postgres.PGUSER}}
✅ DB_PASSWORD=${{Postgres.PGPASSWORD}}

✅ QUEUE_CONNECTION=database
✅ SESSION_DRIVER=database

✅ MAIL_MAILER=log
✅ MAIL_FROM_ADDRESS=noreply@rentalhub.app
```

Jika ada yang missing, add manually.

---

### 4️⃣ .env File Tidak Ada di Railway

**Solusi:** File `.env` dibuat otomatis oleh start command, tapi pastikan `.env.example` ada di repo

Cek:
```bash
git ls-files | grep "\.env"
```

Seharusnya ada `.env.example` di root folder.

---

### 5️⃣ Build Tidak Selesai atau Build Error

**Ciri-ciri:** Deployment stuck di "Building" atau build fails

**Solusi:**

Go to Railway Dashboard → Deployments → Recent deployment → View logs

Cek untuk error seperti:
- `Disk quota exceeded` → Clear cache
- `npm ERR!` → Dependency issue
- `PHP error` → Syntax error di code

Jika perlu rebuild:
1. Go to Settings
2. Clear build cache
3. Redeploy

---

### 6️⃣ Port Atau Start Command Issue

**Solusi:** Start command sudah auto-correct di `railway.json`

Jika masih error, manual set start command di Railway Dashboard → Builder Settings:

```bash
php -r "file_exists('.env') || copy('.env.example', '.env');" && php artisan key:generate && php artisan migrate --force && php artisan optimize:clear && php artisan serve --host=0.0.0.0 --port=$PORT
```

---

## 🔍 Cara Debug 502 Error

### 1. **View Real-Time Logs**

```bash
# Via Railway CLI
railway logs

# Atau via Dashboard
Railway Dashboard → Project → Logs tab
```

### 2. **Check Variables in Console**

```bash
railway run php -r "
echo 'APP_KEY: ' . (isset(\$_ENV['APP_KEY']) ? 'SET' : 'NOT SET') . '\n';
echo 'DB_HOST: ' . (isset(\$_ENV['DB_HOST']) ? $_ENV['DB_HOST'] : 'NOT SET') . '\n';
echo 'APP_URL: ' . (isset(\$_ENV['APP_URL']) ? $_ENV['APP_URL'] : 'NOT SET') . '\n';
"
```

### 3. **Test Database Connection**

```bash
railway run php artisan migrate:status
```

Jika error, database belum siap atau credentials salah.

### 4. **Test Laravel Cache**

```bash
railway run php artisan config:cache
railway run php artisan cache:clear
```

---

## ✅ Quick Fix Checklist

- [ ] APP_KEY sudah diset (format: `base64:xxxxx`)
- [ ] PostgreSQL service sudah added dan initialized
- [ ] Database credentials auto-populated di Variables
- [ ] APP_URL sesuai domain Railway
- [ ] `.env.example` ada di repository
- [ ] railway.json sudah ter-commit ke main branch
- [ ] Last push sudah di-trigger build (check Deployments tab)

---

## 🆘 Jika Masih Error

1. **Force redeploy:**
   ```bash
   railway up --force
   ```

2. **Clear build cache dan redeploy:**
   - Dashboard → Settings → Clear build cache
   - Re-trigger deployment via git push

3. **Check resource usage:**
   - Railway dashboard mungkin kehabisan bandwidth atau memory
   - Update plan jika perlu

4. **Nuclear option - delete & recreate:**
   - Delete deployment di Railway
   - Push code lagi
   - Railway akan fresh deploy

---

**Masih stuck? Lihat logs lebih detail: Railway Dashboard → Logs → Filter by "error" atau "exception"**


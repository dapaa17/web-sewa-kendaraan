# RentalHub

RentalHub adalah aplikasi rental kendaraan berbasis Laravel untuk mengelola penyewaan mobil atau motor dari sisi customer dan admin. Aplikasi ini mendukung registrasi dan login, booking kendaraan, verifikasi KTP, upload bukti pembayaran, verifikasi pembayaran oleh admin, waiting list untuk kendaraan yang masih dipakai, serta auto-cancel booking yang tidak dibayar.

## Fitur Utama

- Registrasi, login, logout, reset password, dan verifikasi email.
- Browse kendaraan dan cek ketersediaan tanggal sewa.
- Booking kendaraan dengan perhitungan durasi dan total harga otomatis.
- Jika jadwal bertabrakan dengan booking aktif yang sudah dibayar, customer tetap bisa booking dan masuk waiting list setelah pembayaran diverifikasi.
- Upload KTP oleh customer dan verifikasi KTP oleh admin.
- Dua jalur pembayaran manual: transfer lalu upload bukti di website, atau transfer lalu konfirmasi via WhatsApp admin.
- Upload bukti pembayaran, verifikasi pembayaran oleh admin, serta email notifikasi saat pembayaran diterima/ditolak.
- Dashboard admin untuk kelola kendaraan, booking, dan verifikasi KTP.
- Halaman admin khusus untuk memantau antrean waiting list per kendaraan.
- Board waiting list admin mendukung pencarian kendaraan/customer dan filter jenis kendaraan.
- Penyelesaian booking dengan perhitungan denda keterlambatan.
- Email otomatis ketika booking waiting list dipromosikan menjadi booking aktif.
- Admin bisa mengirim ulang email notifikasi status booking langsung dari detail booking.
- Status kendaraan hanya berubah menjadi `rented` saat tanggal sewa yang sudah dikonfirmasi memang sudah mulai.
- Customer menerima reminder email H-1 untuk booking yang sudah dikonfirmasi dan mulai besok.
- Auto-cancel booking unpaid melalui Laravel scheduler.

## Stack

- PHP 8.2+
- Laravel 12
- Blade templates + Vite
- Tailwind CSS tooling, Alpine.js, Axios
- Database SQLite default, bisa diganti ke MySQL
- Queue database, session database, dan public storage untuk upload file

## Prasyarat

Pastikan environment lokal sudah memiliki:

- PHP 8.2 atau lebih baru
- Composer
- Node.js dan npm
- SQLite atau MySQL

## Instalasi

1. Clone repository lalu masuk ke folder project.
2. Install dependency backend:

```bash
composer install
```

3. Install dependency frontend:

```bash
npm install
```

4. Salin file `.env.example` menjadi `.env`.
5. Generate application key:

```bash
php artisan key:generate
```

6. Atur koneksi database di `.env`.

   Default project menggunakan SQLite. Jika tetap memakai SQLite, pastikan file `database/database.sqlite` tersedia.

7. Jalankan migrasi dan seed akun default:

```bash
php artisan migrate --seed
```

8. Jika ingin data contoh kendaraan untuk demo, jalankan seeder tambahan:

```bash
php artisan db:seed --class=MotorSeeder
```

9. Buat symbolic link untuk file upload:

```bash
php artisan storage:link
```

## Konfigurasi Environment

Minimal, sesuaikan nilai berikut di `.env`:

```env
APP_NAME=RentalHub
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=sqlite
# Jika memakai MySQL:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=rentalhub
# DB_USERNAME=root
# DB_PASSWORD=

QUEUE_CONNECTION=database
SESSION_DRIVER=database

PAYMENT_WHATSAPP_NUMBER=6282293230668
PAYMENT_BANK_NAME=Bank BCA
PAYMENT_ACCOUNT_NAME=PT. RentalHub Indonesia
PAYMENT_ACCOUNT_NUMBER=1234567890

MAIL_MAILER=log
MAIL_FROM_ADDRESS="noreply@rentalhub.test"
MAIL_FROM_NAME="${APP_NAME}"
```

Catatan:

- Konfigurasi `PAYMENT_*` dipakai untuk informasi transfer dan konfirmasi pembayaran via WhatsApp admin.
- Upload gambar kendaraan, KTP, dan bukti transfer disimpan di disk `public`, jadi `php artisan storage:link` wajib dijalankan.
- Default yang aman untuk lokal adalah `MAIL_MAILER=log`, jadi email notifikasi akan tercatat di log Laravel.
- Jika ingin email notifikasi booking dan auth benar-benar terkirim, sesuaikan konfigurasi `MAIL_*` di `.env` ke SMTP/provider nyata.

## Akun Demo

Seeder bawaan membuat dua akun berikut:

- Admin
  - Email: `admin@example.com`
  - Password: `password`
- Customer
  - Email: `customer@example.com`
  - Password: `password`

`DatabaseSeeder` saat ini hanya memanggil `AdminSeeder`. Data kendaraan demo disediakan terpisah lewat `MotorSeeder`.

## Menjalankan Aplikasi

Untuk development, jalankan:

```bash
composer run dev
```

Script ini akan menyalakan beberapa proses sekaligus:

- `php artisan serve`
- `php artisan queue:listen`
- `php artisan pail`
- `npm run dev`

Jika ingin menjalankan manual, bisa pisahkan per terminal sesuai kebutuhan.

## Scheduler

Project ini memiliki scheduler untuk membatalkan booking yang belum dibayar setelah melewati batas waktunya. Semua booking yang masih pending pembayaran sekarang memakai batas maksimal 1 jam.

Jalankan scheduler lokal dengan terminal terpisah:

```bash
php artisan schedule:work
```

Command yang dijalankan scheduler:

```bash
php artisan bookings:cancel-unpaid --hours=1
php artisan vehicles:sync-rental-statuses
php artisan bookings:send-start-reminders
```

Untuk production, pastikan `php artisan schedule:run` sudah terpasang di cron.

## Testing

Menjalankan seluruh test:

```bash
php artisan test
```

Atau lewat Composer:

```bash
composer test
```

## Alur Penggunaan Singkat

1. Customer register lalu login.
2. Customer browse kendaraan dan membuat booking.
3. Customer upload KTP pada halaman profile.
4. Admin memverifikasi KTP customer.
5. Customer memilih metode pembayaran.
6. Customer memilih salah satu jalur pembayaran: upload bukti transfer di website atau transfer lalu konfirmasi via WhatsApp admin.
7. Admin memverifikasi pembayaran.
8. Jika kendaraan masih dipakai dan jadwal booking bertabrakan, booking customer akan masuk waiting list.
9. Saat booking aktif selesai, sistem mempromosikan antrean teratas secara otomatis dan mengirim email notifikasi ke customer terkait.
10. Setelah kendaraan dikembalikan, admin menyelesaikan booking dan sistem menghitung denda keterlambatan jika ada.

## Arti Status Booking

- `Terjadwal`: booking sudah dikonfirmasi dan aman, tetapi tanggal sewanya memang belum mulai.
- `Waiting List`: jadwal booking bentrok dengan kendaraan yang masih dipakai, jadi booking masuk antrean setelah pembayaran diverifikasi.
- `Menunggu Unit`: tanggal sewa sudah masuk, tetapi kendaraan dari booking sebelumnya belum dikembalikan.
- `Sedang Disewa`: masa sewa sedang berjalan dan kendaraan sudah dipakai sesuai jadwal booking.

## Catatan Penting

- Setelah registrasi, user akan diarahkan ke halaman login, bukan langsung login otomatis.
- Booking unpaid tanpa bukti pembayaran akan dibatalkan otomatis oleh scheduler sesuai batas waktu command.
- Status kendaraan akan berubah mengikuti tanggal mulai sewa yang sudah dikonfirmasi, waiting list, dan penyelesaian booking.
- Verifikasi KTP diperlukan sebelum customer dapat melanjutkan flow pembayaran.
- Booking yang start besok akan menerima reminder email otomatis sekali saja.

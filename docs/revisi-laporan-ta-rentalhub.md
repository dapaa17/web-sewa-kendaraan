### 2.3 Struktur Sistem

Struktur sistem pada aplikasi RentalHub ini terdiri dari struktur menu, hak akses pengguna, serta alur kerja sistem.

#### 2.3.1 Struktur Menu

##### a. Menu Pengguna

Pada sistem ini, pengguna dapat melihat halaman publik terlebih dahulu. Namun, untuk melakukan pemesanan kendaraan, pengguna wajib melakukan registrasi dan login. Menu yang tersedia bagi pengguna antara lain:

1. Home View, yaitu halaman utama yang menampilkan ringkasan layanan RentalHub serta informasi kendaraan yang tersedia.
2. Browse Kendaraan, digunakan oleh pengguna untuk mencari kendaraan berdasarkan nama kendaraan, jenis kendaraan, atau rentang harga.
3. Detail Kendaraan dan Kalender Ketersediaan, digunakan untuk melihat spesifikasi kendaraan, harga sewa, serta jadwal ketersediaan unit.
4. Booking View, digunakan pengguna untuk membuat pemesanan kendaraan dengan menentukan tanggal sewa, waktu pengambilan, serta catatan tambahan.
5. Pembayaran View, digunakan pengguna untuk memilih metode pembayaran (WhatsApp atau unggah bukti transfer) dan memantau status pembayaran.
6. Library Booking (Riwayat Booking), digunakan untuk melihat daftar booking milik pengguna beserta status transaksi.
7. Profil dan Upload KTP, digunakan untuk melengkapi data akun dan mengunggah dokumen KTP untuk proses verifikasi.
8. Review View, digunakan pengguna untuk memberikan ulasan setelah booking selesai.

##### b. Menu Admin

Menu yang tersedia bagi admin antara lain:

1. Dashboard Admin, menampilkan ringkasan data kendaraan, booking, pembayaran, dan aktivitas operasional.
2. Manajemen Kendaraan, digunakan untuk menambah, mengubah, dan menghapus data kendaraan.
3. Manajemen Booking, digunakan untuk memverifikasi pembayaran, melakukan reschedule, dan menyelesaikan transaksi.
4. Kalender Armada, digunakan untuk melihat ketersediaan seluruh kendaraan, memblokir tanggal maintenance, dan mengatur aturan harga.
5. Verifikasi KTP, digunakan untuk menyetujui atau menolak dokumen identitas customer.
6. Moderasi Review, digunakan untuk menyetujui, menolak, atau menghapus review pengguna.
7. Laporan Transaksi, digunakan untuk melihat rekap transaksi dan monitoring pendapatan.

#### 2.3.2 Hak Akses Sistem

Hak akses dalam sistem ini dibagi berdasarkan peran pengguna, yaitu pengunjung, customer, dan admin.

Use case diagram pada Gambar 2.3.2 berikut menunjukkan pembagian hak akses berdasarkan aktor pada sistem RentalHub.

```mermaid
flowchart TB
	subgraph ACT[Aktor]
		direction LR
		P[Pengunjung]
		C[Customer]
		A[Admin]
	end

	subgraph RH[Sistem RentalHub - Use Case Hak Akses]
		direction TB

		subgraph R1[ ]
			direction LR
			U1((Akses halaman publik))
			U2((Registrasi / Login))
			U3((Booking kendaraan))
			U4((Pembayaran booking))
		end

		subgraph R2[ ]
			direction LR
			U5((Riwayat booking & review))
			U6((Kelola data kendaraan))
			U7((Verifikasi pembayaran & KTP))
			U8((Moderasi review & laporan))
		end
	end

	P --> U1
	P --> U2

	C --> U1
	C --> U3
	C --> U4
	C --> U5

	A --> U6
	A --> U7
	A --> U8
```

Berdasarkan diagram tersebut, pengunjung hanya memiliki akses baca ke halaman publik serta proses autentikasi. Customer memiliki akses transaksi seperti booking, pembayaran, riwayat, upload KTP, dan review. Admin memiliki akses penuh pada fitur operasional dan kontrol sistem, meliputi manajemen kendaraan, booking, verifikasi, kalender armada, moderasi review, dan laporan transaksi.

#### 2.3.3 Gambaran Alur Kerja Sistem

Alur kerja sistem pada RentalHub dapat dijelaskan dalam tahapan berikut:

1. Pengguna membuka website RentalHub dan menelusuri informasi kendaraan pada halaman publik.
2. Jika ingin melakukan pemesanan, pengguna melakukan login atau registrasi akun customer.
3. Customer memilih kendaraan, menentukan tanggal sewa, lalu membuat booking.
4. Sistem memvalidasi ketersediaan kendaraan agar tidak terjadi bentrok jadwal pemesanan.
5. Jika valid, sistem menyimpan booking dengan status awal pending.
6. Customer memilih metode pembayaran melalui WhatsApp atau unggah bukti transfer.
7. Admin melakukan verifikasi pembayaran.
8. Jika pembayaran valid, status booking diperbarui menjadi confirmed. Jika belum valid, status pembayaran tetap pending atau failed hingga customer memperbaiki data pembayaran.
9. Setelah masa sewa selesai, admin melakukan inspeksi pengembalian dan menyelesaikan transaksi.
10. Customer dapat memberikan review, lalu review dimoderasi oleh admin sebelum ditampilkan di sistem.

#### 2.3.4 Flow Chart Sistem

```mermaid
flowchart TD
	A[Mulai] --> B[Pengguna membuka website RentalHub]
	B --> C{Sudah login?}
	C -- Belum --> D[Registrasi atau Login]
	D --> E[Masuk ke Dashboard Customer]
	C -- Ya --> E

	E --> F[Pilih kendaraan dan tanggal sewa]
	F --> G{Kendaraan tersedia?}
	G -- Tidak --> H[Pilih jadwal atau kendaraan lain]
	H --> F
	G -- Ya --> I[Buat booking status pending]

	I --> J[Pilih metode pembayaran]
	J --> K[WhatsApp atau unggah bukti transfer]
	K --> L[Admin verifikasi pembayaran]
	L --> M{Pembayaran valid?}
	M -- Tidak --> N[Perbaiki pembayaran]
	N --> K
	M -- Ya --> O[Booking confirmed]

	O --> P[Proses sewa berjalan]
	P --> Q[Admin inspeksi pengembalian]
	Q --> R[Booking completed]
	R --> S[Customer kirim review]
	S --> T[Admin moderasi review]
	T --> U[Selesai]
```

### 2.4 Daftar Tabel

Database aplikasi RentalHub terdiri dari tabel utama dan tabel pendukung Laravel sebagai berikut.

#### a. users

| No | Nama Field | Tipe Data | Keterangan |
| --- | --- | --- | --- |
| 1 | id | bigint UNSIGNED | Primary key, auto increment, identitas unik pengguna. |
| 2 | name | varchar(255) | Nama pengguna. |
| 3 | email | varchar(255) | Email unik pengguna. |
| 4 | password | varchar(255) | Password dalam bentuk hash. |
| 5 | role | enum('admin','customer') | Peran pengguna. |
| 6 | ktp_status | enum('pending','verified','rejected') | Status verifikasi KTP. |
| 7 | created_at | timestamp | Waktu data dibuat. |
| 8 | updated_at | timestamp | Waktu data diperbarui. |

#### b. vehicles

| No | Nama Field | Tipe Data | Keterangan |
| --- | --- | --- | --- |
| 1 | id | bigint UNSIGNED | Primary key, auto increment, identitas unik kendaraan. |
| 2 | name | varchar(255) | Nama kendaraan. |
| 3 | vehicle_type | enum('mobil','motor') | Jenis kendaraan. |
| 4 | plat_number | varchar(255) | Nomor plat kendaraan, bersifat unik. |
| 5 | transmission | enum('Manual','Otomatis') | Jenis transmisi kendaraan. |
| 6 | year | int | Tahun produksi kendaraan. |
| 7 | daily_price | decimal(10,2) | Harga sewa harian. |
| 8 | status | enum('available','rented','maintenance') | Status kendaraan. |
| 9 | image | varchar(255) | Path gambar kendaraan (opsional). |
| 10 | created_at | timestamp | Waktu data dibuat. |
| 11 | updated_at | timestamp | Waktu data diperbarui. |

#### c. bookings

| No | Nama Field | Tipe Data | Keterangan |
| --- | --- | --- | --- |
| 1 | id | bigint UNSIGNED | Primary key, auto increment, identitas unik booking. |
| 2 | user_id | bigint UNSIGNED | Foreign key ke tabel users (customer pemesan). |
| 3 | vehicle_id | bigint UNSIGNED | Foreign key ke tabel vehicles (unit yang disewa). |
| 4 | start_date | date | Tanggal mulai sewa. |
| 5 | end_date | date | Tanggal selesai sewa. |
| 6 | duration_days | int | Durasi sewa (hari). |
| 7 | total_price | decimal(10,2) | Total biaya booking. |
| 8 | status | enum/varchar | Status booking (pending, confirmed, dll). |
| 9 | payment_method | enum/varchar(50) | Metode pembayaran. |
| 10 | payment_status | enum('pending','paid','failed','refunded') | Status pembayaran. |
| 11 | payment_proof | varchar(255) | Bukti transfer (opsional). |
| 12 | created_at | timestamp | Waktu data dibuat. |
| 13 | updated_at | timestamp | Waktu data diperbarui. |

### 3.1 Proses Pembuatan Proyek

Subbab ini menjelaskan tahapan pengembangan sistem RentalHub sebagaimana ditampilkan pada diagram tahapan manajemen proyek. Proses pengembangan dilakukan secara bertahap agar setiap komponen, mulai dari perencanaan hingga pengujian, dapat diimplementasikan secara terstruktur dan saling terintegrasi.

1. Perencanaan

Tahap perencanaan dimulai dari analisis kebutuhan pengguna customer dan admin, identifikasi alur bisnis booking kendaraan, serta penentuan kebutuhan fungsional utama seperti autentikasi, verifikasi KTP, pembayaran, waiting list, dan moderasi review. Pada tahap ini juga disusun rancangan antarmuka awal berbasis Blade untuk memastikan navigasi antarhalaman mudah dipahami pengguna.

2. Perancangan Database

Tahap database berfokus pada perancangan struktur data inti sistem, meliputi tabel pengguna, kendaraan, booking, pembayaran, dan review. Relasi antarentitas dirancang agar mendukung proses transaksi sewa secara lengkap dari awal pemesanan hingga penyelesaian. Selain itu, skema migrasi disusun agar kompatibel pada lingkungan SQLite (pengujian lokal) maupun MySQL (deployment).

3. Implementasi Backend

Pada tahap backend, sistem dibangun menggunakan Laravel 12 dengan penerapan middleware autentikasi, otorisasi berbasis policy, dan logika bisnis booking. Fitur utama yang diimplementasikan mencakup validasi ketersediaan kendaraan, pengelolaan status booking-pembayaran, verifikasi KTP oleh admin, waiting list, serta otomatisasi melalui scheduler seperti pembatalan booking unpaid dan pengiriman reminder email.

4. Implementasi Frontend

Tahap frontend dilakukan dengan Blade template, Tailwind CSS, Alpine.js, dan Vite untuk membangun antarmuka yang responsif. Implementasi difokuskan pada halaman transaksi utama seperti browse kendaraan, form booking, halaman pembayaran, riwayat booking customer, dan dashboard admin. Setiap halaman diintegrasikan dengan status transaksi agar pengguna dapat memantau progres proses sewa secara jelas.

5. Pengujian Sistem

Tahap pengujian dilakukan untuk memastikan seluruh modul berjalan sesuai kebutuhan. Pengujian mencakup pengujian fungsional alur booking dan pembayaran, validasi proses verifikasi admin, pengujian scheduler dan queue, serta pengujian regresi setelah perbaikan bug. Hasil pengujian menunjukkan bahwa fitur inti RentalHub dapat berjalan stabil dan mendukung proses operasional rental kendaraan dari sisi customer maupun admin.

### 3.4 Kendala dan Solusi

Pada tahap implementasi sistem RentalHub, terdapat beberapa kendala yang muncul selama proses pengembangan dan pengujian. Kendala tersebut kemudian diselesaikan melalui perbaikan pada alur sistem, validasi data, dan penguatan proses operasional.

Kendala pertama adalah potensi bentrok jadwal pemesanan kendaraan ketika terdapat beberapa customer yang memesan unit yang sama pada rentang tanggal berdekatan. Solusi yang diterapkan adalah validasi ketersediaan kendaraan berbasis tanggal dan waktu sewa sebelum booking disimpan, sehingga sistem dapat menolak pemesanan yang overlap serta menjaga konsistensi data ketersediaan armada.

Kendala kedua adalah proses verifikasi pembayaran yang membutuhkan ketelitian karena metode pembayaran menggunakan konfirmasi WhatsApp dan unggah bukti transfer. Solusi yang dilakukan adalah menambahkan alur status pembayaran yang jelas (pending, paid, failed) dan menyediakan halaman verifikasi khusus bagi admin agar keputusan verifikasi dapat dilakukan secara terstruktur dan mudah ditelusuri.

Kendala ketiga adalah pengelolaan dokumen identitas customer (KTP), terutama pada tahap unggah, validasi, dan persetujuan admin. Solusi yang diterapkan yaitu pemisahan alur upload KTP di profil customer, penambahan status verifikasi KTP, serta modul verifikasi KTP di sisi admin agar proses persetujuan dokumen berjalan lebih tertib.

Kendala keempat adalah sinkronisasi status transaksi dari awal pemesanan sampai penyelesaian pengembalian kendaraan. Solusi yang dilakukan adalah penegasan alur status booking dan pembayaran pada setiap tahap proses, termasuk saat verifikasi, konfirmasi, dan penyelesaian transaksi, sehingga perubahan status lebih konsisten antara sisi customer dan admin.

Kendala kelima adalah menjaga kualitas ulasan agar konten yang tampil tetap relevan dan sesuai kebijakan sistem. Solusi yang diterapkan adalah fitur moderasi review oleh admin, sehingga setiap ulasan dapat ditinjau terlebih dahulu sebelum dipublikasikan.

Dengan penerapan solusi tersebut, proses operasional RentalHub menjadi lebih stabil, alur kerja admin lebih terarah, serta pengalaman pengguna dalam melakukan pemesanan kendaraan menjadi lebih aman dan terstruktur.

### 4.1 Kesimpulan

Berdasarkan hasil perancangan dan implementasi, alur kerja sistem RentalHub telah berjalan secara terstruktur mulai dari proses pengguna menelusuri kendaraan, melakukan login, membuat booking, melakukan pembayaran, verifikasi oleh admin, hingga penyelesaian transaksi dan pemberian review. Alur ini menunjukkan bahwa proses bisnis penyewaan kendaraan dapat dikelola dalam satu sistem yang saling terhubung dari tahap awal sampai tahap akhir.

Manfaat utama dari sistem ini adalah mempermudah customer dalam melakukan pemesanan kendaraan secara online, memantau status transaksi, serta melakukan pembayaran dengan lebih praktis. Dari sisi admin, sistem membantu pengelolaan data kendaraan, verifikasi dokumen dan pembayaran, pengaturan jadwal armada, serta penyusunan laporan transaksi secara lebih cepat dan rapi.

Dampak penerapan RentalHub adalah meningkatnya efisiensi operasional dan ketertiban administrasi pada usaha rental kendaraan. Risiko kesalahan pencatatan, bentrok jadwal, dan keterlambatan proses verifikasi dapat diminimalkan, sehingga kualitas layanan kepada customer meningkat dan pengambilan keputusan bisnis menjadi lebih tepat karena didukung data transaksi yang terdokumentasi dengan baik.

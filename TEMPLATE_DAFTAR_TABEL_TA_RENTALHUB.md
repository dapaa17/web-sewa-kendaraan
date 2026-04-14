# DAFTAR TABEL

Format penulisan:
- Nomor tabel mengikuti bab: Tabel 2.1, Tabel 3.1, dst.
- Judul tabel menggambarkan isi data.
- Nomor halaman diisi setelah naskah final selesai.

Contoh format:
Tabel 3.1 Judul tabel ..................................................... 27

---

Tabel 2.1 Teknologi yang Digunakan ........................................ [isi]
Tabel 2.2 Daftar Tabel Database dan Fungsi ................................ [isi]
Tabel 2.3 Relasi Tabel Utama Sistem ....................................... [isi]
Tabel 3.1 Hasil Pengujian Fitur Utama Sistem .............................. [isi]
Tabel 3.2 Ringkasan Skenario Uji Booking dan Payment ...................... [isi]
Tabel 3.3 Ringkasan Skenario Uji Review dan Moderasi ...................... [isi]
Tabel 3.4 Kendala Implementasi dan Solusi ................................ [isi]

---

## Template Isi Tabel Siap Tempel

### Tabel 3.1 Hasil Pengujian Fitur Utama Sistem
| No | Fitur | Hasil yang Diharapkan | Hasil Uji | Status |
|----|-------|------------------------|-----------|--------|
| 1 | Login/Registrasi | User dapat autentikasi | Berhasil | Lulus |
| 2 | Booking | Data booking tersimpan valid | Berhasil | Lulus |
| 3 | Upload bukti pembayaran | Bukti tersimpan dan tervalidasi | Berhasil | Lulus |
| 4 | Verifikasi pembayaran | Status booking berubah sesuai aksi admin | Berhasil | Lulus |
| 5 | Waiting list | Booking bentrok masuk antrean | Berhasil | Lulus |
| 6 | Complete booking | Inspeksi dan biaya tambahan tersimpan | Berhasil | Lulus |
| 7 | Review | Review hanya untuk booking eligible | Berhasil | Lulus |
| 8 | Reminder H-1 | Reminder dikirim satu kali | Berhasil | Lulus |

### Tabel 3.2 Kendala Implementasi dan Solusi
| No | Kendala | Dampak | Solusi |
|----|---------|--------|--------|
| 1 | Konflik booking bersamaan | Potensi data bentrok | Cache lock dan DB transaction |
| 2 | Akurasi status berbasis waktu | Status kendaraan bisa keliru | Gunakan pickup_time dan return_time |
| 3 | Konsistensi availability lintas modul | Hasil cek bisa berbeda | Sentralisasi logic di model scope |
| 4 | Dampak maintenance pada booking lanjutan | Jadwal customer terganggu | Maintenance hold dan reschedule admin |

---

## Catatan Pengisian
1. Isi nomor halaman paling akhir setelah seluruh layout Word final.
2. Gunakan style heading yang konsisten agar daftar isi/gambar/tabel otomatis rapi.
3. Jika jumlah tabel sedikit, minimal tampilkan Tabel 2.1 dan Tabel 3.1.

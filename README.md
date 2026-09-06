# AssetTrack - Sistem Manajemen Aset & Inventaris

Sistem manajemen siklus hidup aset dan inventaris berbasis web yang dibangun dengan Laravel dan TailwindCSS. Dilengkapi dengan kode QR otomatis, pelacakan sirkulasi peminjaman dengan audit trail, kontrol otorisasi multi-admin, dan retensi data laporan keuangan akhir tahun menggunakan Soft Deletes.

---

## 🌟 Fitur Utama

- **Otomatisasi QR Code & UUID**: Setiap aset baru otomatis diberikan UUID v4 unik dan QR code yang dapat dicetak langsung dalam format stiker label inventaris.
- **Pemeriksaan & Peminjaman Mobile First**: Halaman scan responsif (`/scan/{uuid}`) dan pemindai kamera (`/scan/camera`) yang memungkinkan staf memverifikasi status serta memproses peminjaman secara langsung.
- **State Machine Peminjaman**: Pengaturan status aset yang ketat (`Tersedia`, `Dipinjam`, `Rusak`) dengan pencatatan log sirkulasi lengkap.
- **Multi-Admin & Alur Persetujuan Registrasi**: Registrasi admin baru membutuhkan persetujuan Super Admin (`Pending Approval`). Semua transaksi peminjaman dan pengembalian dicatat dengan identitas admin penyetuju/penerima.
- **Integritas Laporan Keuangan (Soft Deletes)**: Aset yang ditandai rusak atau dibuang diarsipkan menggunakan Eloquent `SoftDeletes` (`deleted_at`), sehingga nilai kapitalisasi dan depresiasi tetap tercatat akurat dalam audit laporan akhir tahun (`/reports`).

---

## 🚀 Panduan Instalasi & Menjalankan Aplikasi

### 1. Kebutuhan Sistem
- PHP >= 8.2 (dengan ekstensi `pdo_sqlite` atau database pilihan)
- Composer
- Node.js & NPM

### 2. Langkah Instalasi
```bash
# Clone repository & masuk ke direktori proyek
git clone <repository_url>
cd UAS

# Install dependensi backend & frontend
composer install
npm install

# Konfigurasi file environment
cp .env.example .env
php artisan key:generate

# Jalankan migrasi dan seeder database awal
php artisan migrate:fresh --seed

# Build asset frontend
npm run build
```

### 3. Menjalankan Server
```bash
# Jalankan web server Laravel
php artisan serve
```
Akses aplikasi melalui peramban web di: `http://127.0.0.1:8000`

---

## 🔐 Akun Akses Awal (Super Admin)

Setelah menjalankan `php artisan migrate:fresh --seed`, akun Super Admin bawaan adalah:
- **Username / Login:** `sadmin` (atau `sadmin@assettrack.com`)
- **Password:** `yukisadmin`

---

## 🧪 Menjalankan Pengujian Otomatis (Feature Tests)

Semua skenario pengujian bisnis (autentikasi, registrasi admin, CRUD aset, peminjaman, pengembalian, soft delete, dan audit laporan) dapat diuji dengan perintah:
```bash
php artisan test
```

---

## 📄 Informasi Pembuat

Tugas UAS Pemrograman Framework  
**Yukie Fahzal Adi Kurnia** (NIM: 223111021)  
Program Studi Informatika &bull; Universitas Informatika dan Bisnis Indonesia (UNIBI)

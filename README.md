# LAPORAN PROYEK UJIAN AKHIR SEMESTER (UAS)
## SISTEM MANAJEMEN ASET DAN INVENTARIS KANTOR (ASSETTRACK)

**Informasi Mahasiswa:**
* **Nama:** Yukie Fahzal Adi Kurnia
* **NIM:** 223111021
* **Program Studi:** Informatika
* **Institusi:** Universitas Informatika dan Bisnis Indonesia (UNIBI)
* **Mata Kuliah:** Pemrograman Framework

---

## 1. Pendahuluan dan Latar Belakang

AssetTrack adalah sistem manajemen aset dan inventaris kantor berbasis web monolithic yang dirancang untuk mengelola seluruh siklus hidup barang secara terstruktur. Berbeda dengan pencatatan inventaris tradisional yang hanya berfokus pada jumlah stok, AssetTrack mencatat rantai kepemilikan (*chain of custody*), riwayat sirkulasi peminjaman, serta status kondisi aset secara real-time.

Sistem ini mengintegrasikan teknologi identifikasi QR Code berbasis UUID v4, otorisasi multi-admin dengan mekanisme persetujuan (*approval flow*), serta mekanisme *Soft Deletes* untuk menjamin integritas data laporan keuangan dan penyusutan aset pada akhir tahun.

---

## 2. Arsitektur Sistem dan Spesifikasi Teknologi

AssetTrack dibangun menggunakan arsitektur aplikasi monolitik (*monolithic architecture*), di mana logika antarmuka (*frontend*) dan logika bisnis (*backend*) dikelola dalam satu basis kode menggunakan kerangka kerja Laravel dan Blade.

### Tabel Spesifikasi Teknologi

| Komponen | Teknologi / Pustaka | Peran dan Tanggung Jawab |
| :--- | :--- | :--- |
| **Core Framework** | Laravel (PHP 8.2+) | Mengelola logika bisnis, autentikasi, otorisasi, state machine peminjaman, Eloquent Soft Deletes, dan penanganan event observer. |
| **Frontend & UI** | TailwindCSS + Blade | Menyediakan antarmuka korporat minimalis, komponen responsif untuk perangkat seluler, serta antarmuka pemindai QR Code. |
| **Database Engine** | MySQL / PostgreSQL / SQLite | Menyimpan data master aset, log audit transaksi sirkulasi (`asset_logs`), data pengguna, serta kolom `deleted_at`. |
| **QR Code Engine** | `simplesoftwareio/simple-qrcode` | Menghasilkan gambar QR Code berbasis URL unik/UUID v4 secara otomatis saat registrasi aset. |

---

## 3. Fitur Utama dan Logika Bisnis

### 3.1. Otomatisasi UUID dan Generasi QR Code
Setiap pendaftaran aset baru memicu Laravel Event Observer untuk menghasilkan UUID v4 unik. URL identifikasi (`/scan/{uuid}`) dikonversi secara otomatis menjadi grafis QR Code yang siap dicetak sebagai stiker label aset.

### 3.2. Pemindaian Mobile First dan Antarmuka Kamera
Aplikasi menyediakan halaman pemindai kamera interaktif (`/scan/camera`) dan tampilan detail responsif (`/scan/{uuid}`) untuk perangkat seluler, mempermudah staf memeriksa profil aset dan riwayat peminjaman langsung di lapangan.

### 3.3. State Machine Peminjaman Aset
Sistem menerapkan aturan transisi status barang yang ketat untuk menjaga konsistensi data:
* **Tersedia -> Dipinjam**: Aset dapat dipinjam oleh staf melalui persetujuan admin.
* **Dipinjam -> Tersedia**: Aset dikembalikan dan dicatat dalam log sirkulasi.
* **Tersedia / Dipinjam -> Rusak**: Aset yang ditandai rusak tidak dapat dipinjam kembali.

### 3.4. Multi-Admin dan Alur Persetujuan Registrasi
Pendaftaran akun admin baru memerlukan persetujuan dari Super Admin (`Pending Approval`). Setiap transaksi peminjaman dan pengembalian barang secara otomatis mencatat identitas admin yang memproses transaksi tersebut.

### 3.5. Integritas Laporan Keuangan (Soft Deletes)
Aset yang rusak, hilang, atau dibuang tidak dihapus secara permanen dari basis data (`HARD DELETE`). Menggunakan trait `Illuminate\Database\Eloquent\SoftDeletes`, kolom `deleted_at` diisi sehingga data tetap tersimpan. Laporan keuangan akhir tahun (`/reports`) menggunakan kueri `Asset::withTrashed()->get()` agar kalkulasi nilai kapitalisasi awal dan penyusutan aset tetap akurat untuk kebutuhan audit.

---

## 4. Skema Basis Data

### 4.1. Tabel `users`
* `id`: Primary Key (`bigint`)
* `name`: Nama pengguna (`string`)
* `email` / `username`: Identitas login (`string`, unique)
* `password`: Kata sandi terenkripsi (`string`)
* `role`: Peran akun (`enum('super_admin', 'admin')`)
* `status`: Status persetujuan akun (`enum('approved', 'pending')`)
* `created_at` / `updated_at`: Stempel waktu

### 4.2. Tabel `assets`
* `id`: Primary Key (`bigint`)
* `uuid`: UUID v4 unik (`uuid`, indexed)
* `name`: Nama barang/aset (`string`)
* `category`: Kategori aset (`string`)
* `purchase_price`: Harga perolehan (`decimal(15,2)`)
* `status`: Status ketersediaan (`enum('Tersedia', 'Dipinjam', 'Rusak')`)
* `created_at` / `updated_at`: Stempel waktu
* `deleted_at`: Stempel waktu Soft Delete (nullable)

### 4.3. Tabel `asset_logs`
* `id`: Primary Key (`bigint`)
* `asset_id`: Foreign Key ke `assets.id`
* `user_id`: Foreign Key ke `users.id` (peminjam/admin)
* `admin_id`: Foreign Key ke `users.id` (admin penyetuju)
* `action`: Jenis transaksi (`enum('REGISTERED', 'BORROWED', 'RETURNED', 'MARKED_BROKEN')`)
* `notes`: Catatan tambahan (`text`, nullable)
* `created_at`: Stempel waktu transaksi

---

## 5. Panduan Instalasi dan Pengoperasian

### 6.1. Persyaratan Sistem
* PHP >= 8.2 (dengan ekstensi `pdo_sqlite` atau PDO MySQL/PostgreSQL)
* Composer >= 2.x
* Node.js >= 18.x & NPM

### 5.2. Langkah-Langkah Instalasi

1. **Clone Repository dan Masuk Direktori Proyek:**
```bash
git clone <repository_url>
cd UAS
```

2. **Install Dependensi Backend dan Frontend:**
```bash
composer install
npm install
```

3. **Konfigurasi File Environment:**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Menjalankan Migrasi dan Seeder Database:**
```bash
php artisan migrate:fresh --seed
```

5. **Kompilasi Aset Frontend:**
```bash
npm run build
```

6. **Menjalankan Server Lokal:**
```bash
php artisan serve
```
Akses aplikasi melalui peramban web di alamat: `http://127.0.0.1:8000`

---

## 6. Kredensial Akses Awal (Super Admin)

Setelah menjalankan perintah `php artisan migrate:fresh --seed`, akun Super Admin bawaan yang terdaftar pada sistem adalah:

* **Username:** `sadmin` (atau `sadmin@assettrack.com`)
* **Password:** `yukisadmin`
* **Peran:** Super Admin (Akses penuh untuk verifikasi pendaftaran admin baru)

---

## 7. Pengujian Otomatis Sistem (Feature Tests)

Seluruh pengujian skenario logika bisnis (autentikasi, otorisasi persetujuan admin, pengelolaan data aset, sirkulasi peminjaman, pengembalian, penerapan soft delete, serta audit laporan keuangan) dilakukan menggunakan kerangka kerja pengujian bawaan Laravel.

Perintah untuk menjalankan suite pengujian:
```bash
php artisan test
```

---

## 8. Penutup

Sistem AssetTrack berhasil diimplementasikan sesuai dengan seluruh persyaratan fungsional dan non-fungsional yang ditetapkan. Kombinasi kerangka kerja Laravel dan TailwindCSS menghasilkan aplikasi monolitik yang responsif, aman, dan dapat diandalkan untuk pengelolaan sirkulasi inventaris kantor serta transparansi laporan audit keuangan.
Sistem AssetTrack berhasil diimplementasikan sesuai dengan seluruh persyaratan fungsional dan non-fungsional yang ditetapkan. Kombinasi kerangka kerja Laravel dan TailwindCSS menghasilkan aplikasi monolitik yang responsif, aman, dan dapat diandalkan untuk pengelolaan sirkulasi inventaris kantor serta transparansi laporan audit keuangan.

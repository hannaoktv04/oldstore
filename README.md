# 👟 OldStore – Sneaker E-Commerce Platform

**OldStore** adalah platform e-commerce berbasis web yang dirancang khusus untuk penjualan sepatu. Aplikasi ini dibangun menggunakan framework **Laravel** dengan sistem pembayaran otomatis serta manajemen inventaris yang terintegrasi.

Repository:  
🔗 https://github.com/hannaoktv04/oldstore

---

## 📌 Deskripsi Singkat
OldStore dikembangkan sebagai aplikasi web e-commerce modern yang mendukung proses belanja online mulai dari manajemen produk, keranjang belanja, checkout, hingga pembayaran otomatis menggunakan payment gateway. Aplikasi ini juga dilengkapi sistem admin untuk mengelola stok, pengguna, dan transaksi.

---

## ✨ Fitur Utama

### 🔐 Manajemen Pengguna & Keamanan
- **Sistem Multi-Role**  
  Pemisahan hak akses antara **Admin** dan **User**.
- **Manajemen Data Pengguna**  
  Admin dapat mengelola data akun dan mengatur role pengguna.
- **Proteksi Webhook Pembayaran**  
  Validasi callback Midtrans untuk mencegah manipulasi status transaksi.

---

### 🛒 Pengalaman Belanja & Pembayaran
- **Keranjang Belanja Fleksibel**  
  User dapat memilih item tertentu menggunakan checkbox sebelum checkout.
- **Pesan Langsung (Buy Now)**  
  Pembelian cepat langsung dari halaman detail produk tanpa masuk keranjang.
- **Integrasi Midtrans Snap**  
  Pembayaran online otomatis dengan update status transaksi secara real-time.
- **Perhitungan Ongkos Kirim Otomatis**  
  Ongkir dihitung berdasarkan berat produk dan alamat tujuan.

---

### 📦 Manajemen Produk & Inventaris
- **Varian Ukuran Produk**  
  Stok produk dikelola berdasarkan ukuran (contoh: size 36–45).
- **Galeri Produk**  
  Upload banyak foto produk untuk meningkatkan visualisasi.
- **Auto Cancel Order**  
  Pesanan yang tidak dibayar dalam waktu tertentu akan dibatalkan otomatis dan stok dikembalikan.

---

## 🛠️ Teknologi yang Digunakan
- **Backend**: Laravel 10 / 11  
- **Database**: PostgreSQL  
- **Payment Gateway**: Midtrans (Sandbox)  
- **Frontend**:  
  - Bootstrap 5  
  - Blade Template  
  - Vanilla JavaScript  
- **Tools Pendukung**:  
  - ngrok (pengujian webhook Midtrans secara lokal)

---

## ⚙️ Cara Instalasi & Menjalankan Project

### 1️ Clone Repository
```bash
git clone https://github.com/hannaoktv04/oldstore.git
cd oldstore
```
### 2️ Install Dependensi
```
composer install
npm install && npm run dev
```
### 3️ Konfigurasi Environment
```
cp .env.example .env
```
### 4️ Generate Application Key
```
php artisan key:generate
```
### 5️ Migrasi Database
```
php artisan migrate
```
### 6️ Jalankan Server
```
php artisan serve
```

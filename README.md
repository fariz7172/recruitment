# 🧠 ScreeningAI - Sistem Rekrutmen Berbasis AI

![Laravel](https://img.shields.io/badge/Laravel-10-red?logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.1+-blue?logo=php)
![AI](https://img.shields.io/badge/AI-Groq%20Llama%203.3-green)

Sistem rekrutmen modern yang menggunakan **Artificial Intelligence** untuk screening CV/Resume kandidat secara otomatis. Dilengkapi dengan fitur **Tes Psikometri** untuk evaluasi kepribadian dan kemampuan kognitif.

---

## ✨ Fitur Utama

| Fitur | Deskripsi |
|-------|-----------|
| 🤖 **AI Screening** | Analisis CV otomatis menggunakan Groq Llama 3.3 70B |
| 📄 **OCR PDF** | Ekstraksi teks dari file PDF menggunakan Smalot PDF Parser |
| 📊 **Tes Psikometri** | Tes DISC, Kognitif, dan kepribadian lainnya |
| 📈 **Scoring System** | Skor kesesuaian 0-100 untuk setiap kandidat |
| 🎯 **Rekomendasi Otomatis** | SANGAT_SESUAI, SESUAI, PERTIMBANGKAN, TIDAK_SESUAI |
| 🗑️ **Auto Cleanup** | Hapus CV otomatis jika ditolak/tidak sesuai |
| 👤 **Multi Role** | Admin dan Kandidat dengan hak akses berbeda |

---

## 🖼️ Screenshots

### Landing Page
Halaman utama dengan informasi lowongan terbaru dan fitur aplikasi.

### Dashboard Admin
Panel kontrol untuk mengelola lowongan dan melihat statistik lamaran.

### AI Screening Result
Hasil analisis AI dengan breakdown skor, skill match, dan rekomendasi.

---

## 📋 Panduan Penggunaan

### 1️⃣ Untuk Pelamar (Public)

1. **Lihat Lowongan**
   - Buka halaman utama atau menu "Lowongan"
   - Pilih posisi yang diminati

2. **Lamar Pekerjaan**
   - Klik "Lamar Sekarang" pada lowongan yang dipilih
   - Isi form lamaran:
     - Nama lengkap
     - Email
     - No. Telepon/WhatsApp (opsional)
     - Upload CV (PDF/JPG/PNG, max 2MB)
     - Catatan tambahan (opsional)
   - Klik "Kirim Lamaran"

3. **Menunggu Hasil**
   - CV akan dianalisis secara otomatis oleh AI
   - Jika lolos seleksi awal, akun kandidat akan dibuat otomatis
   - Cek email untuk informasi login

### 2️⃣ Untuk Admin

1. **Login**
   - Akses `/login`
   - Masukkan kredensial admin

2. **Kelola Lowongan**
   - Menu "Lowongan Kerja" → Lihat semua lowongan
   - Klik "Buat Lowongan" untuk membuat posisi baru
   - Isi detail: judul, deskripsi, departemen, lokasi, skill yang dibutuhkan, dll.

3. **Review Lamaran**
   - Menu "Lamaran Masuk" → Lihat semua aplikasi
   - Filter berdasarkan status atau lowongan
   - Klik pada lamaran untuk melihat detail

4. **AI Screening**
   - Pada halaman detail lamaran, klik "Screening AI"
   - Sistem akan menganalisis CV dan memberikan:
     - Skor kesesuaian (0-100)
     - Analisis skill match
     - Rekomendasi keputusan
     - Ringkasan profil kandidat

5. **Tes Psikometri**
   - Untuk kandidat yang lolos, akses menu Tes Psikometri
   - Pilih jenis tes (DISC, Kognitif, dll.)
   - Lihat hasil dan analisis kepribadian

6. **Update Status**
   - Ubah status lamaran: Pending → Reviewed → Shortlisted → Hired/Rejected
   - CV akan otomatis dihapus jika status = Rejected

### 3️⃣ Untuk Kandidat (Setelah Login)

1. **Login dengan akun yang diberikan**
   - Email: (email saat melamar)
   - Password: `password` (default)

2. **Lihat Status Lamaran**
   - Cek progress lamaran di dashboard

3. **Ikuti Tes Psikometri**
   - Jika diminta, ikuti tes psikometri
   - Jawab pertanyaan dengan jujur
   - Lihat hasil analisis kepribadian

---

## 🛠️ Instalasi & Setup

### Prasyarat
- PHP >= 8.1
- Composer
- MySQL/MariaDB
- Node.js & NPM (opsional, untuk Vite)

### Langkah Instalasi

```bash
# 1. Clone repository
git clone https://github.com/fariz7172/recruitment.git
cd recruitment

# 2. Install dependencies
composer install

# 3. Copy file environment
cp .env.example .env

# 4. Generate application key
php artisan key:generate

# 5. Konfigurasi database di .env
# DB_DATABASE=recruitment
# DB_USERNAME=root
# DB_PASSWORD=

# 6. Jalankan migrasi & seeder
php artisan migrate --seed

# 7. Buat symbolic link untuk storage
php artisan storage:link

# 8. Jalankan aplikasi
php artisan serve
```

### Konfigurasi AI (Groq)

1. Daftar di [console.groq.com](https://console.groq.com) (GRATIS)
2. Buat API Key
3. Tambahkan ke file `.env`:

```env
GROQ_API_KEY=gsk_xxxxxxxxxxxxxxxxxxxx
```

---

## 📤 Push ke GitHub

### Jika Belum Ada Repository Git

```bash
# 1. Inisialisasi git
git init

# 2. Tambahkan semua file
git add .

# 3. Commit pertama
git commit -m "Initial commit - Laravel Recruitment System"

# 4. Rename branch ke main (atau nama lain)
git branch -M main

# 5. Tambahkan remote origin
git remote add origin https://github.com/USERNAME/REPO_NAME.git

# 6. Push ke GitHub
git push -u origin main
```

### Jika Sudah Ada Repository

```bash
# 1. Tambahkan perubahan
git add .

# 2. Commit dengan pesan
git commit -m "Deskripsi perubahan"

# 3. Push ke GitHub
git push
```

### Contoh untuk Repository Ini

```bash
git remote add origin https://github.com/fariz7172/recruitment.git
git branch -M farizahmad.github.io
git push -u origin farizahmad.github.io
```

---

## 📁 Struktur Folder Penting

```
recruitment/
├── app/
│   ├── Http/Controllers/
│   │   ├── ApplicationController.php   # Kelola lamaran & AI screening
│   │   ├── JobController.php           # Kelola lowongan
│   │   └── PsychometricController.php  # Tes psikometri
│   ├── Models/
│   │   ├── Application.php             # Model lamaran
│   │   ├── Job.php                     # Model lowongan
│   │   └── ScreeningResult.php         # Hasil screening AI
│   └── Services/
│       └── GroqScreeningService.php    # Integrasi Groq AI
├── database/
│   ├── migrations/                     # Struktur database
│   └── seeders/                        # Data awal
├── public/
│   └── css/app.css                     # Stylesheet utama
├── resources/views/
│   ├── applications/                   # View lamaran
│   ├── jobs/                           # View lowongan
│   ├── psychometric/                   # View tes psikometri
│   └── layouts/app.blade.php           # Template utama
└── routes/web.php                      # Definisi routes
```

---

## 🔐 Kredensial Default

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@example.com | password |
| Kandidat | (sesuai email lamaran) | password |

> ⚠️ **Penting**: Ubah password default setelah instalasi!

---

## 🤝 Kontribusi

Kontribusi sangat diterima! Silakan:
1. Fork repository ini
2. Buat branch fitur (`git checkout -b fitur-baru`)
3. Commit perubahan (`git commit -m 'Tambah fitur baru'`)
4. Push ke branch (`git push origin fitur-baru`)
5. Buat Pull Request

---

## 📄 Lisensi

MIT License - Silakan gunakan dan modifikasi sesuai kebutuhan.

---

## 👨‍💻 Pembuat

**Fariz Ahmad**

- GitHub: [@fariz7172](https://github.com/fariz7172)

---

*Dibuat dengan ❤️ menggunakan Laravel 10 & Groq AI*

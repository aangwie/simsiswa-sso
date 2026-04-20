# SIMSiswa - Student Information Management System

**SIMSiswa** adalah aplikasi Sistem Manajemen Informasi Siswa modern yang dibangun menggunakan Laravel 12, Tailwind CSS, dan Alpine.js. Aplikasi ini dirancang untuk memberikan kemudahan bagi institusi pendidikan dalam mengelola data siswa, kelas, mata pelajaran, serta pembaruan sistem secara otomatis melalui integrasi GitHub.

## ✨ Fitur Utama

-   **Dashboard Dinamis**: Visualisasi data jumlah siswa (Total, Laki-laki, Perempuan, Lulus/Mutasi) dalam bentuk kartu dan grafik pendaftaran siswa per tahun menggunakan Chart.js.
-   **Manajemen Terstruktur**:
    -   **Data Siswa**: Pengelolaan data lengkap siswa termasuk identitas orang tua dan alamat, fitur filter pencarian real-time, dan fungsi sort tabel.
    -   **Data Kelas**: Pengelompokan siswa berdasarkan kelas masing-masing.
    -   **Mata Pelajaran**: Pengelolaan data mata pelajaran dengan antarmuka yang bersih.
-   **Output Laporan**: Cetak detail data siswa ke dalam format PDF yang profesional.
-   **Lokalisasi Penuh**: Seluruh format tanggal dan antarmuka telah disesuaikan ke Bahasa Indonesia (Contoh: "05 Juli 2010").
-   **Sistem Pengaturan & Update**:
    -   Pusat pengelolaan konfigurasi sistem.
    -   **GitHub Auto Update**: Pembaruan kode aplikasi langsung dari dashboard menggunakan GitHub Personal Access Token (PAT) dengan tampilan terminal log real-time.
-   **UI/UX Premium**:
    -   Sidebar yang dapat di-minimize/maximize untuk ruang kerja yang lebih luas.
    -   Navigasi yang dikelompokkan (Grouped Menu).
    -   Notifikasi interaktif menggunakan SweetAlert2.

## 🚀 Teknologi yang Digunakan

-   **Framework**: [Laravel 12](https://laravel.com)
-   **Styling**: [Tailwind CSS v4](https://tailwindcss.com)
-   **Frontend Logic**: [Alpine.js](https://alpinejs.dev)
-   **Charts**: [Chart.js](https://www.chartjs.org)
-   **Icons**: [Heroicons](https://heroicons.com)
-   **Notifications**: [SweetAlert2](https://sweetalert2.github.io)

## 🛠️ Instalasi

Ikuti langkah-langkah berikut untuk menjalankan proyek di lingkungan lokal Anda:

1.  **Clone Repositori**:
    ```bash
    git clone https://github.com/aangwie/simsiswa-sso.git
    cd simsiswa
    ```

2.  **Instal Dependensi PHP**:
    ```bash
    composer install
    ```

3.  **Instal Dependensi Frontend**:
    ```bash
    npm install
    npm run build
    ```

4.  **Konfigurasi Environment**:
    Salin file `.env.example` menjadi `.env` dan sesuaikan pengaturan database Anda:
    ```bash
    cp .env.example .env
    ```

5.  **Generate App Key**:
    ```bash
    php artisan key:generate
    ```

6.  **Migrasi Database**:
    Jalankan migrasi untuk membuat seluruh struktur tabel (termasuk skema penuh yang telah disediakan):
    ```bash
    php artisan migrate
    ```

7.  **Jalankan Aplikasi**:
    ```bash
    php artisan serve
    ```
    Buka `http://127.0.0.1:8000` di browser Anda.

## 📖 Penggunaan

1.  **Login**: Gunakan akun admin yang sudah terdaftar di tabel `users`.
2.  **Manajemen**: Akses menu **Manajemen** di sidebar untuk mengelola Siswa, Kelas, dan Mata Pelajaran.
3.  **Pengaturan GitHub**: 
    - Masuk ke menu **Pengaturan**.
    - Masukkan **GitHub Personal Access Token**, **URL Repositori**, dan **Branch**.
    - Klik **Simpan Perubahan**.
    - Gunakan tombol **GIT PULL** untuk memperbarui kode dari repositori secara otomatis.

---

Dikembangkan dengan ❤️ oleh **[aangwie]**.

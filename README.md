# SwiftBill

SwiftBill adalah sistem Point of Sale (POS) modern yang dirancang untuk efisiensi dan estetika tinggi. Dibangun menggunakan TALL stack, aplikasi ini memberikan pengalaman yang lancar untuk manajemen inventaris, transaksi, dan laporan profesional.

## Tech Stack

- PHP 8.2 atau lebih tinggi
- Laravel 11 Framework
- Livewire 3 & Volt
- Alpine.js
- Tailwind CSS
- SQLite Database
- Resend Email Service

## Panduan Instalasi

Ikuti langkah-langkah di bawah ini untuk menjalankan proyek di komputer lokal Anda.

### Prasyarat

Pastikan Anda sudah menginstal perangkat lunak berikut:
- PHP >= 8.2
- Composer
- Node.js & NPM

### Langkah Instalasi

1. **Clone Repositori**
   Salin proyek ini ke direktori lokal Anda:
   ```bash
   git clone https://github.com/Gansputra/Swiftbill.git
   cd Swiftbill
   ```

2. **Instal Dependensi PHP**
   Jalankan perintah berikut untuk mengunduh paket Laravel yang diperlukan:
   ```bash
   composer install
   ```

3. **Instal Dependensi JavaScript**
   Instal aset frontend:
   ```bash
   npm install
   ```

4. **Konfigurasi Lingkungan**
   Salin file contoh konfigurasi:
   ```bash
   copy .env.example .env
   ```

5. **Generate Application Key**
   Amankan aplikasi Anda dengan membuat key baru:
   ```bash
   php artisan key:generate
   ```

6. **Migrasi Database**
   Inisialisasi database dan buat struktur tabel:
   ```bash
   php artisan migrate
   ```

7. **Jalankan Server Pengembangan**
   Untuk mulai menggunakan aplikasi, jalankan dua perintah ini di jendela terminal yang berbeda:
   
   Kompilasi aset:
   ```bash
   npm run dev
   ```
   
   Jalankan server Laravel:
   ```bash
   php artisan serve
   ```

Sekarang aplikasi dapat diakses melalui http://127.0.0.1:8000.

## Kontributor 
<div align="center">
  <table>
    <tr>
      <td align="center">
        <a href="https://github.com/Gansputra">
          <img src="https://github.com/Gansputra.png" width="120px" style="border-radius: 50%" alt="Gansputra"/>
          <br />
          <b>Gansputra</b>
        </a>
      </td>
      <td align="center">
        <a href="https://github.com/akslzero">
          <img src="https://github.com/akslzero.png" width="120px" style="border-radius: 50%" alt="akslzero"/>
          <br />
          <b>akslzero</b>
        </a>
      </td>
      <td align="center">
        <a href="https://github.com/andreantaufikh">
          <img src="https://github.com/andreantaufikh.png" width="120px" style="border-radius: 50%" alt="andreantaufikh"/>
          <br />
          <b>andreantaufikh</b>
        </a>
      </td>
    </tr>
  </table>
</div>

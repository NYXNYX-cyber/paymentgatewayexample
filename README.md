# PAYMENT GATEWAY

![PAYMENT GATEWAY PHP](https://img.shields.io/badge/SC%20STORE-v1.0-blue.svg)
![License](https://img.shields.io/badge/License-MIT-green.svg)
![Status](https://img.shields.io/badge/Status-Active-success.svg)

Chiwa mengucapkan makasih banyak dengan kalian yang udah membeli/menggunakan script ini,jangan lupa baca petunjuk dibawah untuk menggunakan bot

## Petunjuk penggunaan

### 1️. Buat Akun Midtrans Terlebih Dahulu
kalian harus memiliki akun midtrans terlebih dahulu untuk mendapatkan Token API.

### 2️. Konfigurasi `API Token`
- Salin Token Pada  `.env.example` menjadi `.env`
- Isi kredensial database dan informasi lainnya yang diperlukan, seperti:
  - **Kredensial Database** (host, user, password, database name)
  - **Order Kuota**
  - **Data lainnya** sesuai kebutuhan

### 3️. Konfigurasi `MAIN_OWNER`
- `MAIN_OWNER` harus diisi dengan nomor dalam format angka.
- Nomor ini akan memiliki akses **owner** saat proses registrasi.

### 4️. Konfigurasi `CORRECT_COMMAND`
- Jika diatur ke `true`, fitur petunjuk perintah akan diaktifkan saat terjadi kesalahan input.

---

## 📌 Contoh `.env` 
```ini
PREFIX='.'
MAIN_OWNER=''
CORRECT_COMMAND='true'
BOT_NAME=""
DB_HOST=''
DB_PORT=
DB_USER=''
DB_PASSWORD=''
DB_NAME=''
BATAS_WAKTU_BAYAR=900000
QRIS_TEXT='00020101021126670016COM.NOBUBANK.WWW01189360050300000879140214516197937986630303UMI51440014ID.CO.QRIS.WWW0215ID20243618272390303UMI5204541153033605802ID5921CHIWA STORE OK21348796006SLEMAN61055526462070703A0163041BC9'
OKECONNECT_ID=''
OKECONNECT_KEY=''
PTERODACTYL_URL=''
PTERODACTYL_PLTA=''
PTERODACTYL_PLTC=""
DO_TOKEN=''
```

## Bug / Vuln Funding
[![Contact](https://img.shields.io/badge/Contact-WhatsApp-green?style=for-the-badge&logo=whatsapp&logoColor=white)](https://wa.me/6283891278036)
Chiwa mengapresiasi dan menghargai setiap issue yang dilaporkan,jadi jangan sungkan untuk melaporkan masalah yang ada demi menjaga script ini tetap terjaga

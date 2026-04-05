# Client Test Checklist

Checklist untuk pengetesan aplikasi client PC ke server baru `PerfectLum`.

Target server:
- Base URL: `https://new.qubyxtest.xyz`
- Sync endpoint: `https://new.qubyxtest.xyz/api/sync`

## 1. Server URL

- pastikan endpoint lama tidak dipakai lagi
- update base URL client ke:

```text
https://new.qubyxtest.xyz
```

- jika client meminta full endpoint sync, gunakan:

```text
https://new.qubyxtest.xyz/api/sync
```

## 2. Remote Credential

Ambil dari dashboard atau profile user:
- `Remote User` = `sync_user`
- `Remote Password` = `sync_password_raw`

Catatan:
- server memvalidasi sync berdasarkan `sync_user` dan `sync_password`
- jika client lama melakukan MD5 di sisi client, input yang dipakai user tetap password raw dari dashboard

## 3. Account Requirement

Akun yang dipakai untuk test harus:
- aktif (`status = 1`)
- enabled (`enabled = 1`)
- punya `facility_id`
- punya `sync_user`
- punya `sync_password_raw`

## 4. Client Machine Readiness

- clock/jam PC benar
- timezone OS benar
- koneksi HTTPS ke `new.qubyxtest.xyz` tidak diblok firewall/proxy
- cache config lama sudah dibersihkan
- domain lama tidak tertinggal di config
- jika ada whitelist domain atau certificate policy, tambahkan domain baru

## 5. Workstation Identity

Pastikan client mengirim `workstationid` yang stabil.

Server mengenali workstation dari:
- `req_header['workstationid']`

Jika berubah-ubah, server akan menganggapnya workstation baru.

## 6. First-Time Pairing Flow

Urutan pengetesan pairing:

1. buka client
2. isi server URL baru
3. login dengan remote credential
4. request `GROUPS`
5. pilih workgroup
6. kirim `SELECTGROUP`
7. lanjut sync:
   - `DISPLAY`
   - `SETTINGSNAMES`
   - `QATASKS`
   - history/task actions lain sesuai flow client

## 7. Expected Server Behavior

Saat first sync:
- workstation baru bisa otomatis dibuat di server jika belum ada
- workgroup dipilih saat `SELECTGROUP`
- displays akan di-create / update sesuai payload client
- settings workstation akan tersimpan
- QA tasks akan masuk ke `qa_tasks`

## 8. Functional Test Sequence

### A. Authentication

- login sync berhasil
- tidak ada auth failure
- user terhubung ke facility yang benar

### B. Workstation Registration

- workstation baru muncul di server jika belum ada
- `workstation_key` sesuai dengan identity client
- nama workstation terbaca benar

### C. Group Selection

- daftar group tampil
- workgroup bisa dipilih
- workstation tersambung ke workgroup yang benar

### D. Display Sync

- display dari client muncul di server
- display update tidak membuat duplikasi
- display tersambung ke workstation yang benar

### E. Settings Sync

- workstation/application settings terkirim
- server menerima dan menyimpan setting yang diharapkan

### F. QA Task Sync

- QA tasks masuk ke tabel `qa_tasks`
- `taskKey` konsisten
- `freq`, `freqCodes`, `nextdate`, `nextdateFixed` tersimpan

### G. Task Execution Test

- jalankan satu task dari client
- verifikasi server menerima hasil run
- cek:
  - `lastrundate`
  - `nextdate`
  - `nextdateFixed`
  - status task

## 9. QA Sync Regression Check

Masalah lama yang harus divalidasi:
- `nextdate` tidak boleh salah turun ke `00:00:00 UTC`
- setelah sync balik, task daily/weekly harus tetap punya next run yang masuk akal

Fokus pemeriksaan:
- bandingkan next run di client sebelum POST
- bandingkan payload balasan dari server
- bandingkan data akhir di `qa_tasks`

Expected:
- server baru mempertahankan `nextdate` presisi jika incoming timestamp hanya normalisasi midnight UTC
- `nextdateFixed` tetap bisa menyimpan day-anchor dari payload remote/client

## 10. What To Compare During Test

Untuk satu task QA contoh, catat:
- display
- task name
- `taskKey`
- frequency
- timestamp lokal sebelum sync
- timestamp yang dikirim ke server
- timestamp yang dibalas
- nilai akhir `nextdate` di server
- nilai akhir `nextdateFixed` di server

## 11. Validation In Web App

Setelah client sync, cek di web:
- Dashboard
- Displays
- Scheduler
- Calibrate Display
- Detail display
- Histories / Reports

Pastikan:
- data tampil
- tidak ada duplicate task/display
- due date masuk akal
- status display/task sesuai

## 12. Failure Signs

Berikut tanda ada masalah:
- login sync gagal padahal credential benar
- workstation baru terus-terusan tercipta
- display duplicate
- task duplicate
- `nextdate` berubah ke midnight UTC tanpa alasan
- task hilang setelah sync
- display tidak terhubung ke workstation yang benar
- server log penuh error sync

## 13. Artifacts To Collect If Test Fails

Jika test gagal, kumpulkan:
- screenshot config client
- screenshot error client
- request payload yang dikirim
- response payload dari server
- nama display
- `taskKey`
- timestamp sebelum dan sesudah sync
- waktu kejadian
- log server sync:
  - `storage/logs/sync_<login>_<date>.log`
  - `storage/logs/laravel.log`

## 14. Final Acceptance

Client test dianggap lulus jika:
- pairing berhasil
- workstation stabil
- display sync benar
- QA task sync benar
- eksekusi task test berhasil
- `nextdate` tidak salah dinormalisasi
- data tampil benar di web baru

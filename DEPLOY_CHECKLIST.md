# Production Deploy Checklist

Checklist operasional untuk deployment `PerfectLum` production.

Current production target:
- Domain: `https://new.qubyxtest.xyz`
- App root: `/home/u589795521/domains/qubyxtest.xyz/perfectlum_new`
- Web root: `/home/u589795521/domains/qubyxtest.xyz/public_html/new`

## 1. Environment

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL` sesuai domain production
- database production aktif dan bukan database uji
- session driver dan queue mode sesuai environment
- timezone aplikasi sesuai kebutuhan operasional
- `storage/` writable
- `bootstrap/cache/` writable

## 2. Web & Assets

- SSL aktif dan valid
- domain/subdomain sudah resolve ke hosting
- `public/build` tersedia
- asset CSS/JS termuat normal
- tidak ada mixed content
- halaman login bisa dibuka

## 3. Database

- backup database sebelum deploy
- migration berjalan tanpa error
- cek index penting tersedia:
  - `display_preferences(name, value, display_id)`
  - `tasks(display_id)`
  - `qa_tasks(display_id)`
  - `workstations(last_connected)`
- tidak ada akun dummy/test di production
- data inti terbaca normal:
  - `users`
  - `facilities`
  - `workgroups`
  - `workstations`
  - `displays`
  - `tasks`
  - `qa_tasks`
  - `histories`

## 4. Cron & Scheduler

- Cron hosting aktif tiap 1 menit
- command cron:

```bash
/usr/bin/php /home/u589795521/domains/qubyxtest.xyz/perfectlum_new/artisan schedule:run >/dev/null 2>&1
```

- verifikasi `php artisan schedule:list`
- verifikasi tidak ada command test sementara yang tertinggal
- verifikasi job production yang aktif:
  - `alert:disconnected`
  - `alert:daily`

## 5. Email

- `MAIL_MAILER` sesuai kebutuhan production
- SMTP sudah diisi jika email nyata diperlukan
- uji kirim email berhasil
- SPF/DKIM/DMARC domain sesuai

## 6. Security

- tidak ada route debug/test publik
- tidak ada credential sensitif di repo
- user admin/super sudah diaudit
- hak akses `super/admin/user` sudah diverifikasi
- timeout session sesuai policy
- auto logout karena inactivity sudah diuji

## 7. Desktop Smoke Test

- login desktop berhasil
- dashboard terbuka
- displays terbuka
- scheduler terbuka
- calibrate display terbuka
- search terbuka
- reports/histories terbuka
- logout berhasil

## 8. Mobile Smoke Test

- login mobile berhasil
- dashboard terbuka
- workspace terbuka
- tasks terbuka
- reports terbuka
- alerts terbuka
- displays terbuka
- profile terbuka
- logout berhasil

## 9. Functional Smoke Test

- buat scheduler baru
- buat calibration task baru
- buka detail display
- buka detail history
- verifikasi dialog scheduler/calibrate tampil normal
- verifikasi tabel/list refresh setelah create task
- verifikasi role restriction tetap bekerja

## 10. Remote Sync

- verifikasi endpoint sync client yang dipakai
- uji sync QA task
- verifikasi fix `nextdate` tidak tertimpa midnight UTC
- cek data sync masuk ke `qa_tasks` dengan benar
- cek display/task dashboard tidak error setelah sync

## 11. Monitoring

- pantau `storage/logs/laravel.log`
- pantau error 500
- pantau timeout query
- pantau penggunaan disk hosting
- pantau performa halaman berat:
  - dashboard
  - displays
  - scheduler
  - display calibration

## 12. Backup & Recovery

- backup database berkala tersedia
- `.env` production disimpan aman
- langkah restore terdokumentasi
- struktur deploy terdokumentasi:
  - app root di luar public web root
  - web root hanya expose entrypoint/public assets

## 13. Post Deploy Routine

Setelah setiap deploy:
- `php artisan optimize:clear`
- pastikan asset build terbaru tersedia
- cek halaman login
- cek dashboard
- cek log error
- cek smoke test dasar desktop dan mobile

## 14. Notes

- Hostinger shared hosting tidak menyediakan `crontab` CLI via SSH, jadi cron harus dikelola lewat hPanel
- Node.js tidak wajib di runtime jika asset sudah dibuild dan diupload
- deploy production harus menjaga server dan GitHub tetap sinkron

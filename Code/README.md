# Perpustakaan - PHP Native Final

## Struktur
- config/koneksi.php  -> koneksi mysqli
- config/functions.php -> helper (addLog, send_mail_simple, e)
- database/schema.sql -> schema (copied from uploaded file)
- public/* -> semua halaman (router di public/index.php)

## Cara pakai (XAMPP)
1. Letakkan folder `perpustakaan_final` di `htdocs`.
2. Import `database/schema.sql` via phpMyAdmin or:
   - mysql -u root
   - source C:/path/to/perpustakaan_final/database/schema.sql;
3. Buka: http://localhost/perpustakaan_final/public/index.php?url=login
4. Default users in schema.sql (password hashed using password_hash)

## Notes
- Email notification uses PHP mail() - configure php.ini or integrate PHPMailer for SMTP.
- Secure sessions, CSRF protection and input validation recommended for production.


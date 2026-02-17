# Preset XAMPP 8GB (Ubuntu Desktop)

Preset ini berisi konfigurasi siap pakai untuk mempercepat respons aplikasi CBT di laptop RAM 8GB.

## File preset
- `config/preset-xampp-8gb/httpd-8gb.conf`
- `config/preset-xampp-8gb/php-8gb.ini`
- `config/preset-xampp-8gb/mysql-8gb.cnf`

## Cara apply
1. Backup file config aktif:
```bash
sudo cp /opt/lampp/etc/httpd.conf /opt/lampp/etc/httpd.conf.bak
sudo cp /opt/lampp/etc/php.ini /opt/lampp/etc/php.ini.bak
sudo cp /opt/lampp/etc/my.cnf /opt/lampp/etc/my.cnf.bak
```

2. Merge manual isi preset ke file asli (direkomendasikan), atau replace penuh jika paham risikonya.

3. Validasi Apache config:
```bash
sudo /opt/lampp/bin/apachectl -t
```

4. Restart XAMPP:
```bash
sudo /opt/lampp/lampp restart
```

## Rollback cepat
```bash
sudo cp /opt/lampp/etc/httpd.conf.bak /opt/lampp/etc/httpd.conf
sudo cp /opt/lampp/etc/php.ini.bak /opt/lampp/etc/php.ini
sudo cp /opt/lampp/etc/my.cnf.bak /opt/lampp/etc/my.cnf
sudo /opt/lampp/lampp restart
```

## Catatan
- Jika RAM sering penuh, turunkan `innodb_buffer_pool_size` dari `1G` ke `768M`.
- Jangan aktifkan Xdebug saat produksi/ujian.

<?php
/*
 * KONFIGURASI DATABASE
 * Sesuaikan dengan MySQL/MariaDB di komputer Anda.
 * Bawaan XAMPP: user "root", password kosong, port 3306.
 * Jika Anda sudah memberi password pada root (atau memakai user lain), ubah di sini.
 */
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');          // ubah jika MySQL XAMPP Anda memakai port lain (mis. 3307)
define('DB_NAME', 'sewa_barang');   // nama database hasil import database.sql
define('DB_USER', 'root');
define('DB_PASS', '');

/* Data yang tampil di halaman depan */
define('SITE_NAME', 'Layanan Sewa Barang');
define('CONTACT_PHONE', '08563314786');
define('CONTACT_ADDRESS', 'Dsn. Jetak, Ds. Watukenongo, Kec. Pungging');

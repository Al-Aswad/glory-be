# Glory BE

REST API Gift With Laravel

## Tech Stack

-   Laravel 9
-   PHP min 8.0
-   MySQL
-   JWT Auth `php-open-source-saver/jwt-auth`

## Setup Local

`1. Clone project`

```bash
git clone https://al-aswad@bitbucket.org/al-aswad/glory_be.git
```

`2.` buka Project

`3.` composer install

`4.` rename file `.env.example` to `.env`

`5.` buat database

`6.` Sesuikan variable `database` pada file `.env` dengan yang ada pada komputer anda

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=be_db
DB_USERNAME=root
DB_PASSWORD=root
```

`7.` Jalankan perintah `php artisan migrate:f --seed` untuk menyegarkan database dan menjalankan seeder

```bash
php artisan migrate:f --seed
```

`8.` Jalankan perintah `php artisan server` untuk menjalankan aplikasi

```bash
php artisan server
```

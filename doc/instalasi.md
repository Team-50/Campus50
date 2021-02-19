# Instalasi Sistem

### Kebutuhan Sistem
___
1. PHP Minimal versi 7.3
2. Database MySQL Minimal versi 5.7 atau MariaDB Minimal versi 10

### Instalasi Frontend
1. Buka file ```.env.local.example``` kemudian simpan kembali menjadi file .env.local 
2. Sesuaikan isian dari konfigurasi file tersebut :   
asdasd
### Instalasi Backend (API)
___
1. Buat file ```.env```, kemudian salin isinya dari ```.env.example``` atau cara mudahnya "save as" file ```.env.example``` menjadi ```.env```
2. Eksekusi perintah ini untuk membuat JWT_SECRET. php artisan jwt:secret
3. Sesuaikan user dan nama database
4. Buat struktur database dengan mengeksekusi perintah : ```php artisan migrate```
5. Isi database dengan nilai default, supaya sistem bisa berjalan dengan mengeksekusi perintah : ```php artisan db:seed```
6. Extract file public.zip didalam folder storage/app sehingga strukturnya menjadi sebagai berikut :
* public/exported
* public/exported/pdf
* public/exported/excel
* public/images
* public/images/buktibayar
* public/images/pmb
* public/images/sliders
* public/users/no_photo.png
* public/logo.png
7. Buatlah shortcut atau symlink ke folder ```storage/app/public``` dari folder ```/public``` dengan cara mengeksekusi file ```symlink.php``` dengan sesuaikan pathnya terlebih dahulu : ```php symlink.php```

    Atau juga bisa diakses melalui web browser misalnya [localhost](http://localhost:8000/symlink.php)

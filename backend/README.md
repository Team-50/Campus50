# Campus50 Backend

## Instalasi
1. Buat file .env, kemudian salin isinya dari .env.example atau cara mudahnya "save as" file .env.example menjadi .env
2. Eksekusi perintah ini untuk membuat JWT_SECRET.
`php artisan jwt:secret`
3. Sesuaikan user dan nama database
4. Buat struktur database dengan mengeksekusi perintah :
`php artisan migrate`
5. Isi database dengan nilai default, supaya sistem bisa berjalan  dengan mengeksekusi perintah :
`php artisan db:seed`
6. extract file public.zip didalam folder storage/app sehingga strukturnya menjadi sebagai berikut :

<ul>
<li>public/exported</li>
<li>public/exported/pdf</li>
<li>public/exported/excel</li>
<li>public/images</li>
<li>public/images/buktibayar</li>
<li>public/images/pmb</li>
<li>public/images/sliders</li>
<li>public/users/no_photo.png</li>
<li>public/logo.png</li>
</ul>

7. Buatlah shortcut atau symlink ke folder storage/app/public dari folder /public dengan cara mengeksekusi file symlink.php tetapi sebelumnya sesuaikan terlebih dahulu pathnya.

`php symlink.php`

atau juga bisa diakses melalui web browser misalnya `http://localhost:8000/symlink.php`


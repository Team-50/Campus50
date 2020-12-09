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

A. public/exported
B. public/exported/pdf
C. public/exported/excel
D. public/images
E. public/images/buktibayar
F. public/images/pmb
G. public/images/sliders
H. public/users/no_photo.png
I. public/logo.png
``

7. Buatlah shortcut atau symlink ke folder storage/app/public dari folder /public dengan cara mengeksekusi file symlink.php tetapi sebelumnya sesuaikan terlebih dahulu pathnya.

`php symlink.php`

atau juga bisa diakses melalui web browser misalnya `http://localhost:8000/symlink.php`


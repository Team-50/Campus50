# Konfigurasi Zoom

### Silahkan konfigurasi zoom terlebih dahulu dengan menyimpan credential integrasi ke zoom yang item-itemnya adalah sebagai berikut (https://marketplace.zoom.us/docs/guides/build/jwt-app) :
1. API Key
2. API Secret
3. IM Chat History Token
___
### Langkah-langkah konfigurasinya sebagai berikut:
1. Masuklah ke *App Marketplace Zoom* terlebih dahulu melalui url https://marketplace.zoom.us/
2. Sign In menggunakan akun zoom yang telah didaftarkan sebelumnya
3. Pada halaman utama *App Marketplace Zoom*, masuk ke *Develop* > *Build App*
4. Pilih *Create* pada *JWT*, kemudian masukkan *Campus50* ke dalam kolom nama aplikasi
5. Pada *Basic Information* isi data sebagai berikut:
* Nama Aplikasi     : Campus50
* Nama Perusahaan   : Team50
6. Masukkan id dan email sesuai dengan data login awal yg diinput pada *Developer Contact Information*
7. Setelah selesai isi semua data yang dibutuhkan, masuklah ke konfigurasi sistem pada https://campus50.sttindonesia.ac.id/
8. Masuk ke halaman Zoom pada _plugin_
9. Klik *Tambah*, kemudian isilah kolom yang ada sesuai dengan data *App Credentials* yang dihasilkan dari zoom tadi
10. Klik Data yang baru ditambahkan dan lakukan sinkronisasi
11. Sistem akan menghasilkan pemberitahuan bahwa data berhasil disinkronisasi dan akan menggenerasi *Zoom ID* dan *Status* secara otomatis

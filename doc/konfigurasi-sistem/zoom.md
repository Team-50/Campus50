# Konfigurasi Zoom

### Silahkan konfigurasi zoom terlebih dahulu dengan menyimpan credential integrasi ke zoom yang item-itemnya adalah sebagai berikut [Market Place](https://marketplace.zoom.us/docs/guides/build/jwt-app) :
1. API Key
2. API Secret
3. IM Chat History Token
___
### Langkah-langkah konfigurasinya sebagai berikut:
1. Masuklah ke [*App Marketplace Zoom*](https://marketplace.zoom.us/) terlebih dahulu
2. Sign In menggunakan akun zoom yang telah didaftarkan sebelumnya
3. Pada halaman utama *App Marketplace Zoom*, masuk ke ```*Develop* > *Build App*```
4. Pilih *Create* pada *JWT*, kemudian masukkan *Campus50* ke dalam kolom nama aplikasi
5. Pada *Basic Information* isi data sebagai berikut:
```
1. Nama Aplikasi     : Campus50
2. Nama Perusahaan   : Team50
```
6. Masukkan nama dan email sesuai dengan data login awal yg diinput pada *Developer Contact Information*
7. Bagian optional boleh dikosongkan saja
8. Setelah selesai isi semua data yang dibutuhkan, masuklah ke konfigurasi sistem pada [Campus50](https://campus50.sttindonesia.ac.id/)
9. Setelah melakukan login, masuk ke halaman Zoom pada ```_plugin_```
10. Klik *Tambah*, kemudian isilah email serta kolom yang ada sesuai dengan data *App Credentials* yang dihasilkan dari zoom tadi, kosongkan field ```*Zoom ID*``` dan ```*Status*```
11. Klik Data yang baru ditambahkan dan lakukan sinkronisasi
12. Sistem akan menghasilkan pemberitahuan bahwa data berhasil disinkronisasi dan akan menggenerasi ```*Zoom ID*``` dan ```*Status*``` secara otomatis

# Campus50

Sebuah sistem informasi untuk mengelola perguruan tinggi dengan fitur-fitur sebagai berikut :
## Bagi Developer 
1. Clean code by Prettier.

## Cara update repositori dari parent

1. git remote add upstream https://github.com/Team-50/Campus50.git
2. git fetch upstream
3. git rebase upstream/main
4. git push origin main --force
5. git pull

Catatan: kalau `main` tidak jalan ganti pakai `master`

## Cara membuat branch

1. git branch
2. git branch nama_branch
3. git checkout nama_branch

Setelah melakukan perubahan pada nama_branch (baik menambah atau memodifikasi atau menghapus file),
lakukan commit terlebih dahulu:

1. git add .
2. git commit -m "catatan_commit"
3. git checkout main
4. git merge nama_branch

Setelah branch di merge, best practice-nya branch tsb dihapus (INGAT!!! Biasakan 1 Task = 1 Branch):

1. git branch -D nama_branch

## Mac Multiple PHP

Bagi pengguna Mac bila ingin PHP bisa jalan beberapa versi silahkan ikuti panduan di https://getgrav.org/blog/macos-bigsur-apache-multiple-php-versions

## Remove .DS_STORE from git for mac user

`find . -name .DS_Store -print0 | xargs -0 git rm -f --ignore-unmatch`
and then add inside `.gitignore` :
`.DS_STORE`

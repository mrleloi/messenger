# vagrant
※Đây là phần cài máy ảo hỗ trợ vagrant.

→Search máy ảo

★https://app.vagrantup.com/boxes/search

→Cài máy ảo

★https://www.vagrantup.com/

# Cài đặt
## 1. Cài đặt VirtualBox
Tải VirtualBox tại địa chỉ: https://www.virtualbox.org/wiki/Downloads
## 2. Cài đặt Vagrant
https://developer.hashicorp.com/vagrant/downloads
## 3. Cài đặt Make
### Windows
Tải Make tại địa chỉ: http://gnuwin32.sourceforge.net/packages/make.htm
### Ubuntu
Ubuntu đã có sẵn Make, nếu chưa có thì cài đặt bằng lệnh:
```
sudo apt-get install make
```
## 4. Đưa file cả setting vào thư mục chứa source code
## 5. Chạy lệnh
```
make [os] [option]
```
Trong đó:
- os: là tên của lệnh (cái này là hệ điều hành bạn đang dùng) (ví dụ: win, winArm, linux)
- option: nếu bạn muốn cài LEMP(Linux, Nginx, MySQL, PHP) thì thêm option là stack:lemp, nếu không thì bỏ qua option này và mặc định sẽ cài LAMP(Linux, Apache, MySQL, PHP)

Ví dụ:
```
make linux stack:lemp
```
hoặc
```
make win
```
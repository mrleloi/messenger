# THIS FILE IS FOR LEMP STACK (Linux, Nginx, MySQL, PHP)
# In addition to LAMP stack, this file also installs phpMyAdmin, MongoDB, and Composer
# set timezone to Tokyo
timedatectl set-timezone Asia/Tokyo

# THIS CONFIGURATION IS FOR PHPMYADMIN AND MYSQL ONLY (Comment out if you don't need phpMyAdmin and MySQL)
# Install phpMyAdmin (Replace 'your_phpmyadmin_password' with your desired password)
echo "phpmyadmin phpmyadmin/dbconfig-install boolean true" | sudo debconf-set-selections
echo "phpmyadmin phpmyadmin/app-password-confirm password root" | sudo debconf-set-selections
echo "phpmyadmin phpmyadmin/mysql/admin-pass password root" | sudo debconf-set-selections
echo "phpmyadmin phpmyadmin/mysql/app-pass password root" | sudo debconf-set-selections
echo "phpmyadmin phpmyadmin/reconfigure-webserver multiselect apache2" | sudo debconf-set-selections
# Set MySQL root password (Replace 'your_mysql_root_password' with your desired password)
echo "mysql-server mysql-server/root_password password root" | sudo debconf-set-selections
echo "mysql-server mysql-server/root_password_again password root" | sudo debconf-set-selections

# Install packages some packages for development
apt-get update -y && apt-get install -y \
    git vim curl wget zip unzip gnupg\
    nginx phpmyadmin mysql-server\
    php8.1-fpm php-mbstring php-cli php-bcmath php-json php-xml php-zip php-pdo php-common php-tokenizer php-mysql php-curl php-gd php-intl php-soap php-xmlrpc \
    nodejs npm && npm install n -g && n stable && apt purge -y nodejs npm
curl -fsSL https://pgp.mongodb.com/server-6.0.asc | sudo gpg -o /usr/share/keyrings/mongodb-server-6.0.gpg --dearmor
echo "deb [ arch=amd64,arm64 signed-by=/usr/share/keyrings/mongodb-server-6.0.gpg ] https://repo.mongodb.org/apt/ubuntu jammy/mongodb-org/6.0 multiverse" | sudo tee /etc/apt/sources.list.d/mongodb-org-6.0.list
apt-get update && apt-get install -y mongodb-org

# Install Composer
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Configure Nginx
cp /etc/nginx/sites-available/default /etc/nginx/sites-available/default.bak
cp /var/www/html/vagrant-main/nginx/default /etc/nginx/sites-available/default
sudo cp /var/www/html/vagrant-main/cert/my-cert.pem /etc/ssl/certs/my-cert.pem
sudo cp /var/www/html/vagrant-main/cert/my-key.pem /etc/ssl/private/my-key.pem
# Configure PHP
cp /etc/php/8.1/fpm/php.ini /etc/php/8.1/fpm/php.ini.bak
cp /var/www/html/vagrant-main/nginx/php.ini /etc/php/8.1/fpm/php.ini
service nginx restart

# Copy mysql configuration files
cp /var/www/html/vagrant-main/mysql/mysqld.cnf /etc/mysql/mysql.conf.d/mysqld.cnf
cp /var/www/html/vagrant-main/phpmyadmin/config.inc.php /usr/share/phpmyadmin/config.inc.php

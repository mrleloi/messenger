#! bin/sh
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
    apache2\
    phpmyadmin mysql-server\
    php libapache2-mod-php php-mbstring php-cli php-bcmath php-json php-xml php-zip php-pdo php-common php-tokenizer php-mysql php-curl php-gd php-intl php-soap php-xmlrpc \
    nodejs npm && npm install n -g && n stable && apt purge -y nodejs npm \
    && curl -fsSL https://pgp.mongodb.com/server-6.0.asc | sudo gpg -o /usr/share/keyrings/mongodb-server-6.0.gpg --dearmor
echo "deb [ arch=amd64,arm64 signed-by=/usr/share/keyrings/mongodb-server-6.0.gpg ] https://repo.mongodb.org/apt/ubuntu jammy/mongodb-org/6.0 multiverse" | sudo tee /etc/apt/sources.list.d/mongodb-org-6.0.list
apt-get update && apt-get install -y mongodb-org

# Install Composer
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy apache2 confliguration files
cp /var/www/html/vagrant-main/apache2/apache2.conf /etc/apache2/apache2.conf
cp /var/www/html/vagrant-main/apache2/000-default.conf /etc/apache2/sites-available/000-default.conf
cp /var/www/html/vagrant-main/php/php.ini /etc/php/8.1/apache2/php.ini
# ## enable ssl for all site
cp /var/www/html/vagrant-main/apache2/default-ssl.conf /etc/apache2/sites-available/default-ssl.conf
sudo cp /var/www/html/vagrant-main/cert/my-cert.pem /etc/ssl/certs/my-cert.pem
sudo cp /var/www/html/vagrant-main/cert/my-key.pem /etc/ssl/private/my-key.pem
sudo a2enmod ssl
sudo a2ensite default-ssl.conf

# Copy mysql configuration files
cp /var/www/html/vagrant-main/mysql/mysqld.cnf /etc/mysql/mysql.conf.d/mysqld.cnf
cp /var/www/html/vagrant-main/phpmyadmin/config.inc.php /usr/share/phpmyadmin/config.inc.php

# apache Pass of AH00558
echo "ServerName localhost" | tee /etc/apache2/conf-available/fqdn.conf && a2enconf fqdn && a2enmod rewrite

# Restart apache
# sudo systemctl reload apache2
sudo systemctl restart apache2
# Start Apache in the foreground
# sudo /usr/sbin/apachectl -DFOREGROUND

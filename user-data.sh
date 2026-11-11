#!/bin/bash
# Install Apache Web Server and PHP
dnf install -y httpd wget unzip php php-mysqli
# Download app files
wget https://github.com/SEU-USUARIO/livros-app/archive/refs/heads/main.zip
unzip main.zip
cp -r livros-app-main/* /var/www/html/
# Config file for the RDS form (outside the public folder)
touch /var/www/livros-config.php
chown apache:apache /var/www/livros-config.php
chmod 600 /var/www/livros-config.php
# Turn on web server
chkconfig httpd on
service httpd start

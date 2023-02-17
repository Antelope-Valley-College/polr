#!/usr/bin/env bash

# Install required binaries
sed -ir 's/http\:/https\:/g' /etc/apt/sources.list
apt-get update
apt-get install -y unzip git xmlsec1
curl https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin
docker-php-source extract
for module in pdo pdo_mysql; do
  docker-php-ext-configure $module
  docker-php-ext-install $module
done

# Stage the application installer
git clone https://github.com/Antelope-Valley-College/polr.git --depth=1

# Update the branch if needed
cd polr
if [ ! -z "$1" ]; then
 git fetch origin '+refs/heads/*:refs/remotes/origin/*'
 git checkout origin/$1 
fi

# Stage an env file so default database config is available
chmod 755 ../mysql_env.txt

cat > .env <<EOF
DB_CONNECTION=mysql
`grep MYSQL_HOST= ../mysql_env.txt|sed 's/MYSQL_HOST/DB_HOST/'`
DB_PORT=3306
`grep MYSQL_DATABASE= ../mysql_env.txt|sed 's/MYSQL_DATABASE/DB_DATABASE/'`
`grep MYSQL_USER= ../mysql_env.txt|sed 's/MYSQL_USER/DB_USERNAME/'`
`grep MYSQL_PASSWORD= ../mysql_env.txt|sed 's/MYSQL_PASSWORD/DB_PASSWORD/'`
EOF

# Install the application
rm composer.lock
composer.phar install --no-dev -o

# Install the database
# php artisan migrate:fresh --force

# Fix filesystem permissions
cd ..
chmod -R 755 polr
chown -R www-data:www-data .

# Setup apache
a2enmod rewrite

cat >/etc/apache2/sites-enabled/000-default.conf <<EOF
<VirtualHost *:80>
    ServerName polr-web
    ServerAlias polr-web

    DocumentRoot "/var/www/html/polr/public"
    <Directory "/var/www/html/polr/public">
        Require all granted
        Options Indexes FollowSymLinks
        AllowOverride All
        Order allow,deny
        Allow from all
    </Directory>
</VirtualHost>
EOF

# Setup PHP for development
cp /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini

#Reload Apache config
apache2ctl -k graceful 
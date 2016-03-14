#!/usr/bin/env bash

sudo add-apt-repository ppa:ondrej/php > /dev/null
sudo apt-get update > /dev/null

sudo apt-get install curl zip php7.0 php7.0-xml php7.0-zip  -y

curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

cd /vagrant
composer install -o



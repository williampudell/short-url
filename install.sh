#!/bin/bash

echo "######################################################################"
echo "Aguarde: A instalação começará agora.......e vai demorar um pouco :)  "
echo "######################################################################"
echo "      Créditos do script de instalação do servidor: arbabnazar        "
echo "      https://github.com/arbabnazar/Automated-LAMP-Installation       "
echo "######################################################################"

#Update the repositories

sudo apt-get update

#Apache, Php, MySQL and required packages installation

sudo apt-get -y install apache2 php5 libapache2-mod-php5 php5-mcrypt php5-curl php5-mysql php5-gd php5-cli php5-dev mysql-client
php5enmod mcrypt

#The following commands set the MySQL root password to MYPASSWORD123 when you install the mysql-server package.

sudo debconf-set-selections <<< 'mysql-server mysql-server/root_password password MYPASSWORD123'
sudo debconf-set-selections <<< 'mysql-server mysql-server/root_password_again password MYPASSWORD123'
sudo apt-get -y install mysql-server

#Restart all the installed services to verify that everything is installed properly

echo -e "\n"

service apache2 restart && service mysql restart > /dev/null

echo -e "\n"

if [ $? -ne 0 ]; then
   echo "Por favor verifique os serviços de instalaçao, Existe algum $(tput bold)$(tput setaf 1)Problema$(tput sgr0)"
else
   echo "Serviços instalados run $(tput bold)$(tput setaf 2)com sucesso$(tput sgr0)"
fi

echo -e "\n"

FROM ubuntu:18.04

RUN apt-get update
RUN apt-get install -y software-properties-common

RUN apt-get -y install curl netcat wget telnet vim bzip2 ssmtp locales bash-completion net-tools iputils-ping \
    build-essential git libfreetype6-dev libpng-dev libzmq3-dev pkg-config python-dev python-numpy python-pip software-properties-common swig zip sudo unzip git-core software-properties-common sqlite systemd \
    && locale-gen en_US.utf8 \
    && localedef -i en_US -c -f UTF-8 -A /usr/share/locale/locale.alias en_US.UTF-8 

ENV \
    LC_ALL=en_US.UTF-8 \
    LANG=en_US.UTF-8 \
    LANGUAGE=en_US.UTF-8 \
    TZ=Europe/Madrid \
    DEBIAN_FRONTEND=noninteractive

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

RUN apt-get install -y  php php-dev libmcrypt-dev php-pear php-fpm php-mbstring php-xml php-mysql php-json php-intl php-opcache php-gd php-readline php-curl php-gmp php7.2-sqlite
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN mv composer.phar /usr/bin/composer
RUN echo 'alias ll="ls -lha"' >> ~/.bashrc
RUN  pecl channel-update pecl.php.net
RUN  pecl install mcrypt-1.0.1
RUN echo "extension=mcrypt.so" >> /etc/php/7.2/cli/php.ini
RUN echo "extension=mcrypt.so" >> /etc/php/7.2/fpm/php.ini

RUN echo "mysql-server mysql-server/root_password password toor" | debconf-set-selections
RUN echo "mysql-server mysql-server/root_password_again password toor" | debconf-set-selections

RUN apt-get update && \
    apt-get -y install mysql-server sphinxsearch && \
    mkdir -p /var/lib/mysql && \
    mkdir -p /var/run/mysqld && \
    mkdir -p /var/log/mysql && \
    chown -R mysql:mysql /var/lib/mysql && \
    chown -R mysql:mysql /var/run/mysqld && \
    chown -R mysql:mysql /var/log/mysql && \
    sed -i -e "$ a [client]\n\n[mysql]\n\n[mysqld]"  /etc/mysql/my.cnf && \
    sed -i -e "s/\(\[client\]\)/\1\ndefault-character-set = utf8/g" /etc/mysql/my.cnf && \
    sed -i -e "s/\(\[mysql\]\)/\1\ndefault-character-set = utf8/g" /etc/mysql/my.cnf && \
    sed -i -e "s/\(\[mysqld\]\)/\1\ninit_connect='SET NAMES utf8'\ncharacter-set-server = utf8\ncollation-server=utf8_unicode_ci\nbind-address = 0.0.0.0/g" /etc/mysql/my.cnf && \
    service mysql start && sleep 10 && \
    mysql --user=root --password=toor -e "GRANT ALL PRIVILEGES ON *.* TO 'root'@'%' IDENTIFIED BY 'toor' WITH GRANT OPTION; FLUSH PRIVILEGES;"

VOLUME /var/lib/mysql

RUN sudo systemctl enable mysql

RUN mkdir /app
WORKDIR /app
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh
ENTRYPOINT /entrypoint.sh

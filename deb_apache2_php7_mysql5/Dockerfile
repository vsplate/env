FROM ubuntu:16.04
MAINTAINER Ambulong <zeng.ambulong@gmail.com>

RUN apt-get update && \
    apt-get install -y apt-utils net-tools debconf-utils iputils-ping wget curl vim unzip supervisor cron && \
    echo "mysql-server mysql-server/root_password password toor" | debconf-set-selections && \
    echo "mysql-server mysql-server/root_password_again password toor" | debconf-set-selections && \
    apt-get -y install mysql-server && \
    service mysql start  && mysql -u root -ptoor -e 'create database test' && \
    apt-get -y install mysql-client && \
    apt-get -y install php php-xml php-gd php-mbstring php-mysql php-pdo php-sqlite3 php-mcrypt && \
    apt-get -y install apache2 libapache2-mod-php && \
    apt-get clean

RUN rm -rf /var/www/html/*

COPY php.ini /etc/php/7.0/apache2/php.ini
COPY supervisord.conf /etc/supervisord.conf

EXPOSE 80

ENTRYPOINT ["/usr/bin/supervisord", "-n", "-c",  "/etc/supervisord.conf"]

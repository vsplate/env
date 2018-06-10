FROM debian:stretch
MAINTAINER Ambulong <zeng.ambulong@gmail.com>

RUN apt-get update && \
    apt-get install -y apt-utils net-tools debconf-utils iputils-ping wget curl vim unzip supervisor cron
RUN echo "mysql-server mysql-server/root_password password toor" | debconf-set-selections && \
    echo "mysql-server mysql-server/root_password_again password toor" | debconf-set-selections && \
    apt-get -y install mysql-server && \
    apt-get -y install mysql-client && \
    export DEBIAN_FRONTEND=noninteractive DEBCONF_NONINTERACTIVE_SEEN=true && \
    echo "tzdata tzdata/Areas select Etc\n tzdata tzdata/Zones/Etc select UTC"  | debconf-set-selections && \
    apt-get -y install tzdata && \
    apt-get -y install php php-xml php-gd php-mbstring php-mysql php-pdo && \
    apt-get -y install apache2 libapache2-mod-php && \
    apt-get clean

RUN service mysql start && mysql -u root -ptoor -e 'use mysql;update user set authentication_string=PASSWORD("toor"),plugin="mysql_native_password" where user="root";flush privileges;'

RUN rm -rf /var/www/html/*

COPY php.ini /etc/php/7.0/apache2/php.ini
COPY supervisord.conf /etc/supervisord.conf

EXPOSE 80

ENTRYPOINT ["/usr/bin/supervisord", "-n", "-c",  "/etc/supervisord.conf"]

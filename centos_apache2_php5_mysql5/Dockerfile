FROM centos:centos6
MAINTAINER Ambulong <zeng.ambulong@gmail.com>

ENV DEBIAN_FRONTEND noninteractive

RUN yum -y install curl wget unzip git vim \
iproute python-setuptools hostname inotify-tools yum-utils which \
epel-release openssh-server openssh-clients

RUN yum -y install python-setuptools \
&& mkdir -p /var/log/supervisor \
&& easy_install supervisor

RUN yum -y install httpd mod_ssl php php-devel php-mbstring php-gd php-pdo php-soap php-xmlrpc php-xml php-mysql mysql mysql-server crontabs

RUN yum -y install yum -y install sendmail sendmail-cf mailx \
&& echo 'echo 127.0.0.1 localhost localhost.localdomain `hostname` > /etc/hosts && /etc/init.d/sendmail start' > /sendmail_startup.sh

RUN yum clean all \
&& rm -rf /var/www/html/*

RUN service mysqld start && mysqladmin -u root password toor && mysql -u root -ptoor -e 'CREATE DATABASE IF NOT EXISTS test;'

COPY httpd.conf /etc/httpd/conf/httpd.conf
COPY php.ini /etc/php.ini
COPY supervisord.conf /etc/supervisord.conf

EXPOSE 80

ENTRYPOINT ["/usr/bin/supervisord", "-n", "-c",  "/etc/supervisord.conf"]

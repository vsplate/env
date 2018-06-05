FROM centos:centos6
MAINTAINER Ambulong <zeng.ambulong@gmail.com>

ENV DEBIAN_FRONTEND noninteractive

RUN yum -y install curl wget unzip git vim \
iproute python-setuptools hostname inotify-tools yum-utils which \
epel-release openssh-server openssh-clients

RUN yum -y install python-setuptools \
&& mkdir -p /var/log/supervisor \
&& easy_install supervisor

RUN yum -y install httpd mod_ssl php php-devel php-gd php-pdo php-soap php-xmlrpc php-xml php-mysql mysql mysql-server crontabs

RUN yum clean all \
&& rm -rf /var/www/html/*

RUN service mysqld start && mysqladmin -u root password toor

COPY php.ini /etc/php.ini
COPY supervisord.conf /etc/supervisord.conf

EXPOSE 80

ENTRYPOINT ["/usr/bin/supervisord", "-n", "-c",  "/etc/supervisord.conf"]

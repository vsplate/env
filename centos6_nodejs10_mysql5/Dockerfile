FROM centos:centos6
MAINTAINER Ambulong <zeng.ambulong@gmail.com>

ENV DEBIAN_FRONTEND noninteractive

RUN yum -y install curl wget unzip git vim \
iproute python-setuptools hostname inotify-tools yum-utils which \
epel-release 

RUN yum -y install python-setuptools \
&& mkdir -p /var/log/supervisor \
&& easy_install supervisor

RUN yum -y install mysql mysql-server crontabs

RUN service mysqld start && mysqladmin -u root password toor

RUN curl --silent --location https://rpm.nodesource.com/setup_10.x | sudo bash -

RUN yum -y install nodejs gcc-c++ make npm

RUN yum clean all

ENV JAVA_HOME /usr/lib/jvm/jre-openjdk

COPY supervisord.conf /etc/supervisord.conf

EXPOSE 80

ENTRYPOINT ["/usr/bin/supervisord", "-n", "-c",  "/etc/supervisord.conf"]

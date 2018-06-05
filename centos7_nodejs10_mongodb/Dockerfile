FROM centos:centos7
MAINTAINER Ambulong <zeng.ambulong@gmail.com>

ENV DEBIAN_FRONTEND noninteractive

RUN yum -y install curl wget unzip git vim \
iproute python-setuptools hostname inotify-tools yum-utils which \
epel-release 

RUN yum -y install python-setuptools \
&& mkdir -p /var/log/supervisor \
&& easy_install supervisor

COPY mongodb-org.repo /etc/yum.repos.d/mongodb-org.repo

RUN yum -y install mongodb-org

RUN curl --silent --location https://rpm.nodesource.com/setup_10.x | bash -

RUN yum -y install nodejs gcc-c++ make

RUN yum clean all

COPY supervisord.conf /etc/supervisord.conf

EXPOSE 80

ENTRYPOINT ["/usr/bin/supervisord", "-n", "-c",  "/etc/supervisord.conf"]

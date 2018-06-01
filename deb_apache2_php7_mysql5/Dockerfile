FROM ubuntu:16.04
MAINTAINER Ambulong <zeng.ambulong@gmail.com>

ENV DEBIAN_FRONTEND noninteractive

RUN apt-get update && \
    apt-get install -y apt-utils net-tools debconf-utils iputils-ping wget curl vim unzip supervisor && \
    echo "mysql-server mysql-server/root_password password toor" | debconf-set-selections && \
    echo "mysql-server mysql-server/root_password_again password toor" | debconf-set-selections && \
    apt-get -y install mysql-server && \
    apt-get -y install mysql-client && \
    export DEBIAN_FRONTEND=noninteractive DEBCONF_NONINTERACTIVE_SEEN=true && \
    echo "tzdata tzdata/Areas select Etc\n tzdata tzdata/Zones/Etc select UTC"  | debconf-set-selections && \
    apt-get -y install tzdata && \
    apt-get -y install php php-xml php-gd php-mbstring php-mysql php-pdo && \
    apt-get -y install apache2 libapache2-mod-php && \
    apt-get clean && \
    sed -i -e 's/^exit 0/service mysql start\nexit 0/i' /etc/rc.local && \
    sed -i -e 's/^exit 0/service apache2 start\nexit 0/i' /etc/rc.local && \
    service mysql start && \
    service apache2 start

COPY supervisord.conf /etc/supervisord.conf

EXPOSE 80

ENTRYPOINT ["/usr/bin/supervisord", "-n", "-c",  "/etc/supervisord.conf"]

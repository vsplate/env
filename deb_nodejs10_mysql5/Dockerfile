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
    apt-get -y install tzdata

RUN curl -sL https://deb.nodesource.com/setup_10.x -o nodesource_setup.sh && \
    bash nodesource_setup.sh && \
    apt-get -y install nodejs build-essential && \
    apt-get clean && rm -rf nodesource_setup.sh && \
    npm install -g express

COPY supervisord.conf /etc/supervisord.conf

EXPOSE 80

ENTRYPOINT ["/usr/bin/supervisord", "-n", "-c",  "/etc/supervisord.conf"]

FROM debian:stretch
MAINTAINER Ambulong <zeng.ambulong@gmail.com>

RUN apt-get update && \
    apt-get install -y apt-utils net-tools debconf-utils iputils-ping wget curl vim unzip supervisor cron && \
    apt-get clean

COPY gotty_linux_amd64.tar.gz /root/gotty_linux_amd64.tar.gz

RUN tar -zxvf /root/gotty_linux_amd64.tar.gz -C /root && \
    rm -rf /root/gotty_linux_amd64.tar.gz

COPY supervisord.conf /etc/supervisord.conf

EXPOSE 80

ENTRYPOINT ["/usr/bin/supervisord", "-n", "-c",  "/etc/supervisord.conf"]

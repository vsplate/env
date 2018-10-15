FROM vsplate/deb_apache2_php7_mysql5:latest
MAINTAINER Ambulong <zeng.ambulong@gmail.com>

COPY index.php /var/www/html/index.php
COPY db.sql /tmp/db.sql
COPY vsplatego.sh /tmp/vsplatego.sh

RUN mkdir /var/www/html/pma && /bin/sh /tmp/vsplatego.sh

COPY pma /var/www/html/pma

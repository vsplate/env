FROM php:7.2.2-apache

COPY ghostscript-9.23.tar.gz /tmp/ghostscript-9.23.tar.gz

RUN cd /tmp \
    && tar -xzf ghostscript-9.23.tar.gz \
    && cd ghostscript-9.23 \
    && ./configure \
    && make && make install && make clean \
    && ldconfig \
    && cd /tmp \
    && rm -rf ghostscript-9.23*

COPY ImageMagick-6.9.2-10.tar.xz /tmp/ImageMagick-6.9.2-10.tar.xz

RUN cd /tmp \
    && tar -xvf ImageMagick-6.9.2-10.tar.xz -C /tmp/ \
    && cd /tmp/ImageMagick-6.9.2-10 \
    && ./configure && make \
    && make install \
    && ldconfig /usr/local/lib
 
RUN pecl install imagick \
    && docker-php-ext-enable imagick \
    && rm -rf /tmp/ImageMagick*
 
RUN docker-php-ext-install pdo_mysql


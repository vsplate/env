FROM php:7.2.2-apache

COPY ghostscript-9.23.tar.gz /tmp/ghostscript-9.23.tar.gz

RUN apt-get update && apt-get -y install libpng-dev libjpeg-dev libxml2-dev

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
    && ./configure && make -j$(nproc)\
    && make install \
    && ldconfig /usr/local/lib \
    && rm -rf /tmp/ImageMagick*
 
RUN curl -fsSL 'http://pecl.php.net/get/imagick-3.4.3.tgz' -o imagick.tar.gz \
    && mkdir -p imagick \
    && tar -xf imagick.tar.gz -C imagick --strip-components=1 \
    && rm imagick.tar.gz \
    && (cd imagick \
    && phpize \
    && ./configure --enable-imagick \
    && make -j$(nproc) \
    && make install \
    ) \
    && rm -r imagick \
    && docker-php-ext-enable imagick

RUN echo "extension=imagick.so" > /usr/local/etc/php/conf.d/ext-imagick.ini
 
RUN docker-php-ext-install pdo_mysql


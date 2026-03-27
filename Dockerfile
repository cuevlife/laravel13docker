FROM php:8.4-cli-alpine

# ติดตั้ง Extensions และ Composer
RUN apk add --no-cache bash git icu-dev libpng-dev libzip-dev zip unzip \
    && docker-php-ext-install pdo_mysql gd intl zip opcache

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ตั้งค่า OPcache และ Upload Limit
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini" && \
    sed -i 's/;opcache.enable=1/opcache.enable=1/' "$PHP_INI_DIR/php.ini" && \
    sed -i 's/;opcache.validate_timestamps=1/opcache.validate_timestamps=1/' "$PHP_INI_DIR/php.ini" && \
    sed -i 's/;opcache.memory_consumption=128/opcache.memory_consumption=256/' "$PHP_INI_DIR/php.ini" && \
    sed -i 's/upload_max_filesize = 2M/upload_max_filesize = 20M/' "$PHP_INI_DIR/php.ini" && \
    sed -i 's/post_max_size = 8M/post_max_size = 25M/' "$PHP_INI_DIR/php.ini"

WORKDIR /app

# เพิ่ม Entrypoint Script
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

# คำสั่งหลัก
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8001"]

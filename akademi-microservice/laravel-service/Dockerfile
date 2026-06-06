FROM php:8.4-cli
WORKDIR /var/www/html
RUN apt-get update && apt-get install -y \
    git unzip zip curl \
    libzip-dev libonig-dev \
    libxml2-dev libsqlite3-dev \

    nodejs npm \

    && docker-php-ext-install \
       mbstring zip pdo pdo_mysql \
       pdo_sqlite xml \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY . .
RUN composer install --no-dev --no-interaction \
    --prefer-dist --optimize-autoloader

RUN npm install
RUN npm run build

EXPOSE 8000
CMD ["php","artisan","serve","--host=0.0.0.0","--port=8000"]
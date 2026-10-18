# Image có sẵn Swoole, PHP 8.4
FROM phpswoole/swoole:php8.4

# Cài Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set timezone (Asia/Ho_Chi_Minh)
RUN ln -sf /usr/share/zoneinfo/Asia/Ho_Chi_Minh /etc/localtime \
    && echo "Asia/Ho_Chi_Minh" > /etc/timezone \
    && echo 'date.timezone = "Asia/Ho_Chi_Minh"' > /usr/local/etc/php/conf.d/timezone.ini

# Cài PECL inotify (optional: nếu dùng Laravel Octane watcher)
# inotify + pcntl
RUN pecl install inotify && docker-php-ext-enable inotify \
    && docker-php-ext-install pcntl

# Install Node.js + chokidar
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g chokidar

RUN apt-get update && apt-get install -y procps && rm -rf /var/lib/apt/lists/*

COPY ../laravel /servers/laravel
COPY ../watch.sh /servers/laravel
COPY ../startApp.php /servers/laravel

WORKDIR /servers/laravel

ENTRYPOINT ["/servers/laravel/watch.sh"]

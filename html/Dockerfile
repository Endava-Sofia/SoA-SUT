FROM php:8.2-apache
RUN docker-php-ext-install mysqli
## MBM
WORKDIR /var/www/html
COPY . /var/www/html
EXPOSE 80
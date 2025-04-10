FROM php:8.2-apache
RUN docker-php-ext-install mysqli
## MBM
WORKDIR /var/www/html
COPY . /var/www/html

# Ensure the target directory exists before copying
RUN mkdir -p /var/www/html/uploads/profile_pictures

# Copy the provided default profile picture from its location within the build context
COPY uploads/profile_pictures/default_profile.png /var/www/html/uploads/profile_pictures/default_profile.png

# Set permissions for the uploads directory
RUN chown -R www-data:www-data /var/www/html/uploads && \
    chmod -R 755 /var/www/html/uploads

EXPOSE 80
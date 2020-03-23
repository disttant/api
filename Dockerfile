FROM achetronic/laravel-php:latest



#### LARAVEL OPERATIONS
# Installing system temporary packages
RUN apt-get install -y -qq --force-yes composer git zip unzip php7.3-zip --no-install-recommends > /dev/null

# Creating a temporary folder for our app
RUN mkdir -p /tmp/laravel

# Download the entire project
COPY . /tmp/laravel/

# Create needed folders for composer autoloader optimization
RUN mkdir -p /app/storage/passport
RUN mkdir -p /app/database
RUN mkdir -p /app/database/seeds
RUN mkdir -p /app/database/factories

# Defining which packages Composer will install
RUN cp /tmp/laravel/composer.lock /app/composer.lock
RUN cp /tmp/laravel/composer.json /app/composer.json

# Please, Composer, install them
RUN composer install -d /app --no-dev --no-scripts

# Moving Laravel to the right place
RUN cp -r /tmp/laravel/* /app
RUN rm -rf /tmp/laravel
RUN touch /app/.env

# Setting the configurations values for Laravel
RUN cd /app && composer dump-autoload

# Deleting system temporary packages
RUN apt-get purge -y -qq --force-yes composer git zip unzip php7.3-zip > /dev/null

# Cleaning the system
RUN apt-get -y -qq --force-yes autoremove > /dev/null

# Changing permissions of the entire Laravel
RUN chown www-data:www-data -R /app
RUN find /app -type f -exec chmod 644 {} \;
RUN find /app -type d -exec chmod 755 {} \;



#### FINAL OPERATIONS
RUN rm -rf /init.sh && touch /init.sh
RUN echo "#!/bin/bash" >> /init.sh
RUN echo "service php7.3-fpm start" >> /init.sh
RUN echo "shopt -s dotglob" >> /init.sh
RUN echo "mv /app/* /var/www/" >> /init.sh
#RUN echo 'printf "%s" "${PASSPORT_PRIVATE_KEY}" > /var/www/storage/passport/oauth-private.key' >> /init.sh
#RUN echo 'printf "%s" "${PASSPORT_PUBLIC_KEY}" > /var/www/storage/passport/oauth-public.key' >> /init.sh
RUN echo "php /var/www/artisan config:cache" >> /init.sh
RUN echo "/bin/bash" >> /init.sh
RUN chown root:root /init.sh
RUN chmod +x /init.sh
EXPOSE 9000
CMD /init.sh


FROM achetronic/laravel-php:1.0



#### LARAVEL PRE-STEPS
# Getting environment variables
ENV APP_VENDOR=Company
ENV APP_NAME=Product
ENV APP_ENV=production
ENV APP_KEY=base64:VNt8gejhfX7I3UwNv40focB3QOoH7Jofju9C4h5UeNY=
ENV APP_DEBUG=false
ENV APP_URL=http://product.company.es
ENV LOG_CHANNEL=stack
ENV DB_CONNECTION=mysql
ENV DB_HOST=127.0.0.1
ENV DB_PORT=3306
ENV DB_DATABASE=database
ENV DB_USERNAME=user
ENV DB_PASSWORD=pass
ENV BROADCAST_DRIVER=log
ENV CACHE_DRIVER=file
ENV QUEUE_CONNECTION=sync
ENV SESSION_DRIVER=database
ENV SESSION_LIFETIME=120
ENV REDIS_HOST=127.0.0.1
ENV REDIS_PASSWORD=null
ENV REDIS_PORT=6379
ENV MAIL_DRIVER=smtp
ENV MAIL_HOST=smtp.sendgrid.net
ENV MAIL_PORT=587
ENV MAIL_USERNAME=apikey
ENV MAIL_PASSWORD=password
ENV MAIL_ENCRYPTION=tls
ENV MAIL_FROM_NAME=Company
ENV MAIL_FROM_ADDRESS=noreply@company.com
ENV MAIL_ADMIN_NAME=Company
ENV MAIL_ADMIN_ADDRESS=admin@company.es
ENV OAUTH_URI=http://accounts-svc.default.svc.cluster.local
ENV OAUTH_CHECK_TOKEN_ROUTE=/internal/oauth/access_token/validate

# Setting environment variables
RUN export APP_VENDOR=$APP_VENDOR && \
    export APP_NAME=$APP_NAME && \
    export APP_ENV=$APP_ENV && \
    export APP_KEY=$APP_KEY && \
    export APP_DEBUG=$APP_DEBUG && \
    export APP_URL=$APP_URL && \
    export LOG_CHANNEL=$LOG_CHANNEL && \
    export DB_CONNECTION=$DB_CONNECTION && \
    export DB_HOST=$DB_HOST && \
    export DB_PORT=$DB_PORT && \
    export DB_DATABASE=$DB_DATABASE && \
    export DB_USERNAME=$DB_USERNAME && \
    export DB_PASSWORD=$DB_PASSWORD && \
    export BROADCAST_DRIVER=$BROADCAST_DRIVER && \
    export CACHE_DRIVER=$CACHE_DRIVER && \
    export QUEUE_CONNECTION=$QUEUE_CONNECTION && \
    export SESSION_DRIVER=$SESSION_DRIVER && \
    export SESSION_LIFETIME=$SESSION_LIFETIME && \
    export REDIS_HOST=$REDIS_HOST && \
    export REDIS_PASSWORD=$REDIS_PASSWORD && \
    export REDIS_PORT=$REDIS_PORT && \
    export MAIL_DRIVER=$MAIL_DRIVER && \
    export MAIL_HOST=$MAIL_HOST && \
    export MAIL_PORT=$MAIL_PORT && \
    export MAIL_USERNAME=$MAIL_USERNAME && \
    export MAIL_PASSWORD=$MAIL_PASSWORD && \
    export MAIL_ENCRYPTION=$MAIL_ENCRYPTION && \
    export MAIL_FROM_NAME=$MAIL_FROM_NAME && \
    export MAIL_FROM_ADDRESS=$MAIL_FROM_ADDRESS && \
    export MAIL_ADMIN_NAME=$MAIL_ADMIN_NAME && \
    export MAIL_ADMIN_ADDRESS=$MAIL_ADMIN_ADDRESS && \
    export OAUTH_URI=$OAUTH_URI && \
    export OAUTH_CHECK_TOKEN_ROUTE=$OAUTH_CHECK_TOKEN_ROUTE



#### LARAVEL OPERATIONS
# Installing system temporary packages
RUN apt-get install -y -qq --force-yes composer git zip unzip php7.3-zip --no-install-recommends > /dev/null

# Creating a temporary folder for our app
RUN mkdir -p /tmp/laravel

# Download the entire project
COPY . /tmp/laravel/

# Create needed folders for composer autoloader optimization
# RUN mkdir -p /var/www/database
# RUN mkdir -p /var/www/database/seeds
# RUN mkdir -p /var/www/database/factories
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
RUN echo "php /var/www/artisan config:cache" >> /init.sh
RUN echo "/bin/bash" >> /init.sh
RUN chown root:root /init.sh
RUN chmod +x /init.sh
EXPOSE 9000
CMD /init.sh


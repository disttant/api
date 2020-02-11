## About Adaptative API

## Dependencies

* PHP >= 7.2.0
* BCMath PHP Extension
* Ctype PHP Extension
* JSON PHP Extension
* Mbstring PHP Extension
* OpenSSL PHP Extension
* PDO PHP Extension
* Tokenizer PHP Extension
* XML PHP Extension

## Preparing

## Installing for production

#### 1. The hard way

0. Install Composer, Git
1. Clone the repository to a temporary folder
2. Copy composer.json and composer.lock to the final location
3. Go to that folder: 
```
cd /final/location
```
4. Execute: 
```
composer install --no-dev --no-scripts
```
5. Move the temporary folder's content to the final location
6. Re-build the autoload file executing: 
```
composer dump-autoload
```
7. Configure *.env* file
8. Create your DB tables executing: 
```
php artisan migrate
```
9. Give the right permissions to the files: 
```
find /final/location -type f -exec chmod 644 {} \;
find /final/location -type d -exec chmod 755 {} \;
```

#### 2. The easy way
0. Install Docker
1. Clone the achetronic/laravel-service repository
2. Place your .env into Dockerfile-config
3. Execute: 
```
docker build --build-arg GIT_APPLICATION=https://your/project.git \
             -t achetronic/adaptative-api .
```
4. Rise up your container
```
docker run --rm \
           -p 80:80 \
           --name adaptative-api \
           -it \
           --mount type=volume,src=storage,dst=/var/www/storage \
           -d \
           achetronic/adaptative-api
```


## Security Vulnerabilities

If you discover security vulnerabilities, please send 
an e-mail to Alby Hernández [me@achetronic.com]. 

All security vulnerabilities will 
be fixed as soon as we notice them.

## License
This is privative software and it is NOT allowed to redistribute
any copy neither partial not complete.
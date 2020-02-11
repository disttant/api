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

#### The hard way
```
1. Clone the repository to a temporary folder
2. Copy composer.json and composer.lock to the final location
3. Go to that folder: cd /final/location
4. Execute: composer install --no-dev --no-scripts
5. Move the temporary folder's content to the final location
6. Execute: composer dump-autoload
7. Configure .env file
8. Execute: php artisan migrate
9. Give the right permissions to the files: 
    * find /final/location -type f -exec chmod 644 {} \;
    * find /final/location -type d -exec chmod 755 {} \;
```
## Security Vulnerabilities

If you discover security vulnerabilities, please send 
an e-mail to Alby Hernández [me@achetronic.com]. 

All security vulnerabilities will 
be fixed as soon as we notice them.

## License
This is privative software and it is NOT allow to redistribute
any copy neither partial not complete.
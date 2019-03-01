# Ever-changing Hierarchy GmbH

## Install the application

    docker-compose up -d
    docker-compose exec slim sh
    curl -s https://getcomposer.org/installer | php
    php composer.phar install
    
## Run the application

The application is available in http://127.0.0.1:8090

## Run the tests

    docker-compose exec slim /var/www/vendor/bin/phpunit
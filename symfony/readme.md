## Symfony Version

1. Install PHP dependencies
`composer install`
2. Install Symfony CLI
Go to [Symfony CLI](https://symfony.com/download) and follow the instructions.
3. Start server
`symfony serve:start`
4. Call the url `http://localhost:8000/api/short_url` with cURL or postman adding the Authorization header and URL in request body.

## Unit test
Execute `php bin/phpunit` (everything is configured in phpunit.xml.dist)

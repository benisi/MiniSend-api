## About Minisend

MiniSend is a cloud-based transactional email service that allows our customers to manage transactional emails


## Project setup

## Install all dependencies
```
composer install
```
### Create a .env file in the root directory from the .env.example file, add your database, mail driver and queue connection credentials
```
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

// configure the mail driver of your choice (important!)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=

QUEUE_CONNECTION=
```

### Run all migrations
```
php artisan migrate
```

### On the root directory run
```
php artisan serve
php artisan queue:work
```

### To run the test
```
vendor/bin/phpunit --testdox
```
### Send a POST request to http://{your local address}/api/v1/email with the payload

```
{
    "from": {
        "email": "dad@doe.com",
        "name": "father doe"
    },
    "to": [
        {
            "email": "john@doe.com",
            "name": "John doe"
        },
        {
            "email": "mark@doe.com",
            "name": "Mark doe"
        }
    ],
    "subject": "Hi from {$company}",
    "text": "test",
    "html": "<h1>{$company} is saying hi</h1><p>testing html with {$company}</p>",
    "variables": [
        {
            "email": "john@doe.com,
            "substitutions": [
                {
                    "var": "company",
                    "value": "MiniSend"
                }
            ]
        }
    ],
    "attachments" : [
                {
                    "filename" : "test.jpg",
                    "content" : "base 64 file content"
                }
            ]
}
```
### With Headers
```
Authorization: Bearer {generated token}
Accept: application/json
```

### Note

- attachments is optional
- You can either send a text or an html not both
- if you add a variable kindly provide it's substitution, if this is not done nothing will be substituted

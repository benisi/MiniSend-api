## About Minisend

MiniSend is a cloud-based transactional email service that allows our customers to manage transactional emails


## Project setup

## Install all dependencies
```
composer install
```
### Create a .env file in the root directory from the .env.example file, add your database credentials
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

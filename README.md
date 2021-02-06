# MiniSend Application Setup -- Step By Step Guide

## Dependencies

-   PHP 7.3
-   GIT
-   Node v14.15.4
-   NPM 6.14.11
-   Composer
-   Mailtrap account

## Steps to run the app on local environment

`git clone https://github.com/tannu13/mini-send.git`

`cd mini-send`

`composer install`

`npm install` (Not required to run it locally)

`Copy shared .env file to project root`

### Changes include

-   Database to sqlite (`DB_CONNECTION=sqlite`)
-   Change Queue functionality to database dependent (`QUEUE_CONNECTION=database`)
-   Mailtrap SMTP settings (username & password)

`touch database/database.sqlite`

`php artisan migrate`

`php artisan serve`

`php artisan queue:work` (For processing queues)

# minibank
MiniBank Code Challenge from Slack

Developed in Windows

## How to Run

### Prerequisites
* Install the latest [Node.js](https://nodejs.org/en/), [Git](https://git-scm.com/download/win), [Composer](https://getcomposer.org/Composer-Setup.exe) and [XAMPP](https://www.apachefriends.org/pt_br/download.html)

### Step by step
* Open the command prompt and run:
```
cd\ && cd xampp/htdocs && git clone https://github.com/bigmarcolino/minibank.git && cd minibank && npm i && npm run prod && composer install
```
* Open http://localhost/phpmyadmin and create a database called "minibank" with the utf8mb4_unicode_ci collation
* Go back to command prompt and run:
```
php artisan migrate
```
* Create an account in [Mailtrap](https://mailtrap.io/), sign in and, if you do not have an inbox, create an inbox
* Open the inbox and open the SMTP Settings tab. The SMTP username and password values will be shown
* Open the .env file in the project root and fill MAIL_USERNAME and MAIL_PASSWORD variables with the SMTP username and password

The last three steps are required to verify email sending when creating an account. When you create an account in MiniBank, emails will be sent to the Mailtrap inbox

* Run the XAMPP Control Panel and start Apache and MySQL. At the prompt, run:
```
php artisan serve
```
* Open http://localhost:8000 to open the MiniBank website

## Unit and Feature Tests

### Run
It is not necessary to run Apache, MySQL and php artisan serve before run tests, because the tests run in memory

* Open the command prompt and run (with backslash, not slash):
```
vendor\bin\phpunit
```

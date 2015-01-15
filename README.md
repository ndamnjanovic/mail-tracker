mail-tracker
============

Mini application that creates usage report of user's gmail account.

More information can be found in [official docs](https://developers.google.com/admin-sdk/reports/).

## Installation

- Clone the repository
- Install dependencies (from console)
    - run ```composer install```
- To set permissions: ```chmod -R 777 app/storage && chmod -R 777 public/uploads```
- Create database: ```mysqladmin -u root -p password YOUR PASSWORD create NAME_FROM_DATABASE_CONFIG```
- Create config file for Artdarek package. ```php artisan config:publish artdarek/oauth-4-laravel```
- Add new constant for Google Service that will handle Reports API. Check my pull request ``` https://github.com/Lusitanian/PHPoAuthLib/pull/343 ```